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
use DspSofts\CronManagerBundle\Entity\CronTaskLog;
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

        $cronManipulator = $this->getContainer()->get('dsp_cm.util.cron_manipulator');
        $cronManipulator->checkRunningCrons();
        $cronManipulator->runCrons();

        $output->writeln('<comment>Done!</comment>');
    }
}
