<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 04/11/15 12:09
 */

namespace DspSofts\CronManagerBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class RunJobCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    private $entityManager;

    private $logDir;

    private $logFile;

    /**
     * @var CronTaskLog
     *
     */
    private $cronTaskLog;

    protected function configure()
    {
        $this->setName('dsp:cron:runjob');
        $this->setDescription('Runs a job');
        $this->addOption('crontask', 'c', InputOption::VALUE_REQUIRED, "CronTask id");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager entityManager */
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var EntityRepository $cronTaskRepo */
        $cronTaskRepo = $this->entityManager->getRepository('DspSoftsCronManagerBundle:CronTask');

        /** @var CronTask $cronTask */
        $cronTask = $cronTaskRepo->findOneBy(array('id' => $input->getOption('crontask')));

        $this->logDir = $this->getContainer()->getParameter('dsp_softs_cron_manager.logs_dir');
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->logDir)) {
            $filesystem->mkdir($this->logDir);
        }

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

        $execString = '';
        if ($type == CronTask::TYPE_SYMFONY) {
            $output->writeln(sprintf('Executing symfony command <comment>%s</comment>...', $command));
            $execString = 'exec ' . $this->getContainer()->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR . 'console' . ' ' . $command;
        } elseif ($type == CronTask::TYPE_COMMAND) {
            $output->writeln(sprintf('Executing command <comment>%s</comment>...', $command));
            $execString = 'exec ' . $command;
        } elseif ($type == CronTask::TYPE_URL) {
            $output->writeln(sprintf('Executing URL <comment>%s</comment>...', $command));
            $output->writeln('<error>NOT IMPLEMENTED YET !</error>');
            $execString = '';
        }

        $output->writeln("Final command line = <info>$execString</info>");
        // Run the command
        $this->runCommand($execString, $cronTask->getTimeout());

        $output->writeln('<info>FINISHED</info>');
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

    private function runCommand($string, $timeout = 0)
    {
        try {
            $process = new Process($string);
            $process->setTimeout($timeout);
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

            return $exitCode !== 0;
        } catch (\Exception $e) {
            $this->cronTaskLog->setStatus(CronTaskLog::STATUS_FAILED);
            $this->cronTaskLog->setPid(null);
            $this->cronTaskLog->setDateEnd(new \DateTime());
            $this->updateCronTaskLog();

            return 1;
        }
    }
}
