<?php

/**
 * Main command which launches all crons.
 * This command should be run every minute by crontab.
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 12:51
 */

namespace DspSofts\CronManagerBundle\Command;


use DspSofts\CronManagerBundle\Entity\CronTaskLog;
use DspSofts\CronManagerBundle\Util\PlanificationChecker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Filesystem\Filesystem;

class RunCommand extends ContainerAwareCommand
{
	private $output;

	private $logDir;

	private $logFile;

	protected function configure()
	{
		$this->setName('dsp:cron:run');
		$this->setDescription('Runs Cron Tasks if needed');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('<comment>Running Cron Tasks...</comment>');

		$this->output = $output;
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		$cronTasks = $em->getRepository('DspSoftsCronManagerBundle:CronTask')->findAll();

		$this->logDir = $this->getContainer()->getParameter('dsp_softs_cron_manager.logs_dir');
		$filesystem = new Filesystem();
		if (!$filesystem->exists($this->logDir)) {
			$filesystem->mkdir($this->logDir);
		}

		$planificationChecker = new PlanificationChecker();

		foreach ($cronTasks as $cronTask) {
			// Get the last run time of this task, and calculate when it should run next
			/*
			$lastRun = $cronTask->getLastRun() ? $cronTask->getLastRun()->format('U') : 0;
			$nextRun = $lastRun + $cronTask->getInterval();

			// We must run this task if:
			// * time() is larger or equal to $nextrun
			$run = (time() >= $nextRun);
			*/

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

				$cronTaskLog = new CronTaskLog();
				$em->persist($cronTaskLog);

				$cronTaskLog->setFilePath(str_replace($this->logDir, '', $this->logFile));
				$cronTaskLog->setCronTask($cronTask);
				$cronTaskLog->setDateStart(new \DateTime());
				$cronTaskLog->setStatus(CronTaskLog::STATUS_RUNNING);

				// Set $lastrun for this crontask
				$cronTask->setLastRun(new \DateTime());
				$em->persist($cronTask);

				$em->flush();

				try {
					$commands = $cronTask->getCommands();
					foreach ($commands as $command) {
						$output->writeln(sprintf('Executing command <comment>%s</comment>...', $command));

						// Run the command
						$this->runCommand($command);
					}

					$output->writeln('<info>SUCCESS</info>');
					$cronTaskLog->setStatus(CronTaskLog::STATUS_OK);
				} catch (\Exception $e) {
					$output->writeln('<error>ERROR</error>');
					$cronTaskLog->setStatus(CronTaskLog::STATUS_FAILED);
				}

				$cronTaskLog->setDateEnd(new \DateTime());
				$em->flush();
			} else {
				$output->writeln(sprintf('Skipping Cron Task <info>%s</info>', $cronTask->getName()));
			}
		}

		// Flush database changes
		$em->flush();

		$output->writeln('<comment>Done!</comment>');
	}

	private function runCommand($string)
	{
		// Split namespace and arguments
		$namespace = explode(' ', $string)[0];

		// Set input
		$command = $this->getApplication()->find($namespace);
		$input = new StringInput($string);

		$handle = fopen($this->logFile, 'w+');
		$output = new StreamOutput($handle);

		// Send all output to the console
		$returnCode = $command->run($input, $output);

		fclose($handle);

		return $returnCode != 0;
	}
}