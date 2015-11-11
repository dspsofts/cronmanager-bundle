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
    protected function configure()
    {
        $this->setName('dsp:cron:runjob');
        $this->setDescription('Runs a job');
        $this->addOption('crontask', 'c', InputOption::VALUE_REQUIRED, "CronTask id");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jobRunner = $this->getContainer()->get('dsp_cm.util.job_runner');

        $jobRunner->runJob($input->getOption('crontask'));

        $output->writeln('<info>FINISHED</info>');
    }
}
