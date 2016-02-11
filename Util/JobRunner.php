<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 10:08
 */

namespace DspSofts\CronManagerBundle\Util;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class JobRunner
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var string
     */
    private $logDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $logFile;

    /**
     * @var CronTaskLog
     */
    private $cronTaskLog;

    public function __construct(ManagerRegistry $managerRegistry, $kernelRootDir, $logDir, LoggerInterface $logger = null)
    {
        $this->managerRegistry = $managerRegistry;
        $this->kernelRootDir = $kernelRootDir;
        $this->logDir = $logDir;
        $this->logger = $logger;
    }

    public function runJob($cronTaskId)
    {
        $entityManagerCronTask = $this->managerRegistry->getManagerForClass('DspSoftsCronManagerBundle:CronTask');
        /** @var EntityRepository $cronTaskRepo */
        $cronTaskRepo = $entityManagerCronTask->getRepository('DspSoftsCronManagerBundle:CronTask');

        /** @var CronTask $cronTask */
        $cronTask = $cronTaskRepo->findOneBy(array('id' => $cronTaskId));

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

        $entityManagerCronTaskLog = $this->managerRegistry->getManagerForClass('DspSoftsCronManagerBundle:CronTaskLog');
        $this->cronTaskLog = new CronTaskLog();
        $entityManagerCronTaskLog->persist($this->cronTaskLog);

        $this->cronTaskLog->setFilePath(str_replace($this->logDir, '', $this->logFile));
        $this->cronTaskLog->setCronTask($cronTask);
        $this->cronTaskLog->setDateStart(new \DateTime());
        $this->cronTaskLog->setStatus(CronTaskLog::STATUS_RUNNING);

        // Set $lastrun for this crontask
        $cronTask->setLastRun(new \DateTime());
        $cronTask->setRelaunch(false);
        $entityManagerCronTask->persist($cronTask);
        $entityManagerCronTask->flush();

        // Check if task is already running
        $taskAlreadyRunning = false;
        if ($cronTask->getIsUnique()) {
            /** @var EntityRepository $cronTaskRepo */
            $cronTaskLogRepo = $entityManagerCronTaskLog->getRepository('DspSoftsCronManagerBundle:CronTaskLog');

            // TODO create a specific method for this, no need to parse every running task here
            $runningTasks = $cronTaskLogRepo->searchRunning();
            foreach ($runningTasks as $runningTask) {
                if ($runningTask->getCronTask() == $cronTask) {
                    $taskAlreadyRunning = true;
                    if ($this->logger !== null) {
                        $this->logger->warning('This task is unique and it is already running');
                    }

                    $this->cronTaskLog->setStatus(CronTaskLog::STATUS_ALREADY_RUNNING);
                    $this->cronTaskLog->setDateEnd(new \DateTime());
                    $this->cronTaskLog->setPid(null);
                    $entityManagerCronTaskLog->persist($this->cronTaskLog);
                    $entityManagerCronTaskLog->flush();
                }
            }
        }

        if (!$taskAlreadyRunning) {
            $command = $cronTask->getCommand();
            $type = $cronTask->getType();

            $execString = '';
            if ($type == CronTask::TYPE_SYMFONY) {
                if ($this->logger !== null) {
                    $this->logger->info(sprintf('Executing symfony command %s ...', $command));
                }
                $execString = 'exec ' . $this->kernelRootDir . DIRECTORY_SEPARATOR . 'console' . ' ' . $command;
            } elseif ($type == CronTask::TYPE_COMMAND) {
                if ($this->logger !== null) {
                    $this->logger->info(sprintf('Executing command %s ...', $command));
                }
                $execString = 'exec ' . $command;
            } elseif ($type == CronTask::TYPE_URL) {
                if ($this->logger !== null) {
                    $this->logger->info(sprintf('Executing URL %s ...', $command));
                    $this->logger->error('NOT IMPLEMENTED YET !');
                }
                $execString = '';
            }

            if ($this->logger !== null) {
                $this->logger->debug("Final command line = $execString");
            }
            // Run the command
            $this->runCommand($execString, $cronTask->getTimeout());
        }
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
        $entityManager = $this->managerRegistry->getManagerForClass('DspSoftsCronManagerBundle:CronTaskLog');

        try {
            $process = new Process($string);
            $process->setTimeout($timeout);
            $process->start(array($this, 'callbackProcess'));

            $this->cronTaskLog->setPid($process->getPid());
            $entityManager->persist($this->cronTaskLog);
            $entityManager->flush();

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
            $entityManager->persist($this->cronTaskLog);
            $entityManager->flush();

            return $exitCode !== 0;
        } catch (\Exception $e) {
            $this->cronTaskLog->setStatus(CronTaskLog::STATUS_FAILED);
            $this->cronTaskLog->setPid(null);
            $this->cronTaskLog->setDateEnd(new \DateTime());
            $entityManager->persist($this->cronTaskLog);
            $entityManager->flush();

            return 1;
        }
    }
}
