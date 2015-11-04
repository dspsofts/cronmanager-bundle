<?php

/**
 * Main command which launches all crons.
 * This command should be run every minute by crontab.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 12:51
 */

namespace DspSofts\CronManagerBundle\Command;

use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Util\PlanificationChecker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RunCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('dsp:cron:run');
        $this->setDescription('Runs Cron Tasks if needed');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Running Cron Tasks...</comment>');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $cronTaskRepo = $em->getRepository('DspSoftsCronManagerBundle:CronTask');
        /** @var CronTask[] $cronTasks */
        $cronTasks = $cronTaskRepo->findAll();

        $planificationChecker = new PlanificationChecker();

        foreach ($cronTasks as $cronTask) {
            $run = $planificationChecker->isExecutionDue($cronTask->getPlanification());

            if ($run) {
                $output->writeln(sprintf('Running Cron Task <info>%s</info>', $cronTask->getName()));
                $cli = 'exec ' . $this->getContainer()->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR . 'console dsp:cron:runjob -c ' . $cronTask->getId() . ' &';
                $output->writeln(sprintf('Command line : <info>%s</info>', $cli));
                $process = new Process($cli);
                $process->setTimeout(0);
                $process->start();
            } else {
                $output->writeln(sprintf('Skipping Cron Task <info>%s</info>', $cronTask->getName()));
            }
        }

        $output->writeln('<comment>Done!</comment>');
    }
}
