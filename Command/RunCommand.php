<?php

/**
 * Main command which launches all crons.
 * This command should be run every minute by crontab.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 12:51
 */

namespace DspSofts\CronManagerBundle\Command;


use Doctrine\ORM\EntityManager;
use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;
use DspSofts\CronManagerBundle\Util\PlanificationChecker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class RunCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    private $entityManager;
    
    private $output;

    private $logDir;

    private $logFile;

    /**
     * @var CronTaskLog
     */
    private $cronTaskLog;

    protected function configure()
    {
        $this->setName('dsp:cron:run');
        $this->setDescription('Runs Cron Tasks if needed');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running Cron Tasks...</comment>');

        $this->output = $output;
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $cronTaskRepo = $this->entityManager->getRepository('DspSoftsCronManagerBundle:CronTask');
        /** @var CronTask[] $cronTasks */
        $cronTasks = $cronTaskRepo->findAll();

        $this->logDir = $this->getContainer()->getParameter('dsp_softs_cron_manager.logs_dir');
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->logDir)) {
            $filesystem->mkdir($this->logDir);
        }

        $planificationChecker = new PlanificationChecker();

        foreach ($cronTasks as $cronTask) {
            $run = $planificationChecker->isExecutionDue($cronTask->getPlanification());

            if ($run) {
                $output->writeln(sprintf('Running Cron Task <info>%s</info>', $cronTask->getName()));

                $dir = $this->logDir
                    . DIRECTORY_SEPARATOR . date('Y')
                    . DIRECTORY_SEPARATOR . date('Ymd')
                    . DIRECTORY_SEPARATOR . $cronTask->getSlug();

                if (!$filesystem->exists($dir)) {
                    $filesystem->mkdir($dir);
                }

                $this->logFile = $dir . DIRECTORY_SEPARATOR . date('Ymd_His') . '.log';

                $this->cronTaskLog = new CronTaskLog();
                $this->entityManager->persist($this->cronTaskLog);

                $this->cronTaskLog->setFilePath(str_replace($this->logDir, '', $this->logFile));
                $this->cronTaskLog->setCronTask($cronTask);
                $this->cronTaskLog->setDateStart(new \DateTime());
                $this->cronTaskLog->setStatus(CronTaskLog::STATUS_RUNNING);

                // Set $lastrun for this crontask
                $cronTask->setLastRun(new \DateTime());
                $this->entityManager->persist($cronTask);

                $this->entityManager->flush();

                $command = $cronTask->getCommand();
                $type = $cronTask->getType();

                $output->writeln("Type = <info>$type</info>");
                $output->writeln("Command = <info>$command</info>");

                $execString = '';
                if ($type == CronTask::TYPE_SYMFONY) {
                    $output->writeln(sprintf('Executing symfony command <comment>%s</comment>...', $command));
                    $execString = $this->getContainer()->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR . 'console' . ' ' . $command;
                } elseif ($type == CronTask::TYPE_COMMAND) {
                    $output->writeln(sprintf('Executing command <comment>%s</comment>...', $command));
                    $execString = $command;
                } elseif ($type == CronTask::TYPE_URL) {
                    $output->writeln(sprintf('Executing URL <comment>%s</comment>...', $command));
                    $output->writeln('<error>NOT IMPLEMENTED YET !</error>');
                    $execString = '';
                }

                $output->writeln("Final command line = <info>$execString</info>");
                // Run the command
                $this->runCommand($execString);

                $output->writeln('<info>FINISHED</info>');
                $this->entityManager->flush();
            } else {
                $output->writeln(sprintf('Skipping Cron Task <info>%s</info>', $cronTask->getName()));
            }
        }

        // Flush database changes
        $this->entityManager->flush();

        $output->writeln('<comment>Done!</comment>');
    }

    private function updateCronTaskLog()
    {
        $this->entityManager->persist($this->cronTaskLog);
        $this->entityManager->flush();
    }

    public function callbackProcess($type, $buffer)
    {
        $log = $buffer;
        if (strtolower($type) === 'err') {
            $this->cronTaskLog->setStatus(CronTaskLog::STATUS_RUNNING_WITH_WARNINGS);
            $log = 'ERROR : ' . $log . PHP_EOL;
        }

        $log = date('Y-m-d H:i:s') . ' ' . $log;
        file_put_contents($this->logFile, $log, FILE_APPEND);
    }
    
    private function runCommand($string)
    {
        $process = new Process($string);
        $process->start(array($this, 'callbackProcess'));

        $this->cronTaskLog->setPid($process->getPid());
        $this->updateCronTaskLog();

        $exitCode = $process->wait();

        $this->cronTaskLog->setDateEnd(new \DateTime());

        if ($exitCode > 0) {
            $this->cronTaskLog->setStatus(CronTaskLog::STATUS_FAILED);
        } elseif ($process->getErrorOutput() != '') {
            $this->cronTaskLog->setStatus(CronTaskLog::STATUS_WARNING);
        } else {
            $this->cronTaskLog->setStatus(CronTaskLog::STATUS_SUCCESS);
        }

        $this->cronTaskLog->setPid(null);
        $this->updateCronTaskLog();

        return $exitCode != 0;
    }
}