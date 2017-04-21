<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 09:43
 */

namespace DspSofts\CronManagerBundle\Util;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class CronManipulator
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var PlanificationChecker
     */
    private $planificationChecker;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ManagerRegistry $managerRegistry, PlanificationChecker $planificationChecker, $kernelRootDir, LoggerInterface $logger = null)
    {
        $this->managerRegistry = $managerRegistry;
        $this->logger = $logger;
        $this->planificationChecker = $planificationChecker;
        $this->kernelRootDir = $kernelRootDir;
    }

    public function checkRunningCrons()
    {
        $entityManager = $this->managerRegistry->getManagerForClass('DspSoftsCronManagerBundle:CronTaskLog');
        $cronTaskLogRepo = $entityManager->getRepository('DspSoftsCronManagerBundle:CronTaskLog');
        $cronTaskLogs = $cronTaskLogRepo->searchRunning();
        foreach ($cronTaskLogs as $cronTaskLog) {
            if (posix_getpgid($cronTaskLog->getPid()) === false) {
                if ($this->logger !== null) {
                    $this->logger->info(sprintf('PID %s not found for cron task log id %s, terminating task...', $cronTaskLog->getPid(), $cronTaskLog->getId()));
                }
                $cronTaskLog->setStatus(CronTaskLog::STATUS_FAILED);
                $cronTaskLog->setPid(null);
                $cronTaskLog->setDateEnd(new \DateTime());
                $entityManager->persist($cronTaskLog);
                $entityManager->flush();
            }
        }
    }

    public function runCrons()
    {
        $entityManager = $this->managerRegistry->getManagerForClass('DspSoftsCronManagerBundle:CronTask');
        $cronTaskRepo = $entityManager->getRepository('DspSoftsCronManagerBundle:CronTask');
        $cronTasks = $cronTaskRepo->findCronsToLaunch();

        foreach ($cronTasks as $cronTask) {
            $run = true;
            if (!$cronTask->getRelaunch()) {
                $run = $this->planificationChecker->isExecutionDue($cronTask->getPlanification());
            }

            if ($run) {
                if ($this->logger !== null) {
                    $this->logger->info(sprintf('Running Cron Task <info>%s</info>', $cronTask->getName()));
                }
                $cli = 'exec ' . $this->kernelRootDir . DIRECTORY_SEPARATOR . 'console dsp:cron:runjob -c ' . $cronTask->getId() . ' &';
                if ($this->logger !== null) {
                    $this->logger->info(sprintf('Command line : <info>%s</info>', $cli));
                }
                try {
                    $process = new Process($cli);
                    $process->setTimeout(0);
                    $process->start();
                    // FIXME attendre un peu avant de quitter le process pere
                    sleep(1);
                } catch (\Exception $e) {
                    if ($this->logger !== null) {
                        $this->logger->critical("Erreur en lançant le process : " . get_class($e) . " : " . $e->getMessage());
                        $this->logger->critical($e->getTraceAsString());
                    }
                }
            } else {
                if ($this->logger !== null) {
                    $this->logger->info(sprintf('Skipping Cron Task <info>%s</info>', $cronTask->getName()));
                }
            }
        }
    }
}
