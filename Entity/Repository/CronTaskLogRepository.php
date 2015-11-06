<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 04/11/15 22:11
 */

namespace DspSofts\CronManagerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;

class CronTaskLogRepository extends EntityRepository
{
	/**
	 * @return CronTaskLog[]
	 */
	public function findByPidNotNull()
	{
		$queryBuilder = $this->createQueryBuilder('cron_task_log');
		$queryBuilder->where('cron_task_log.pid IS NOT NULL');
		$queryBuilder->orderBy('cron_task_log.dateStart', 'desc');

		$query = $queryBuilder->getQuery();

		return $query->getResult();
	}

	/**
	 * @param \DateTime $dateTime
	 * @return CronTaskLog[]
	 */
	public function findFinishedByDate(\DateTime $dateTime)
	{
		$queryBuilder = $this->createQueryBuilder('cron_task_log');
		$queryBuilder->select('cron_task_log');
		$queryBuilder->where('cron_task_log.pid IS NULL');
		$queryBuilder->andWhere('cron_task_log.dateStart >= :dateStart');
		//$queryBuilder->groupBy('cron_task_log.cronTask, cron_task_log.status');
		$queryBuilder->orderBy('cron_task_log.dateStart', 'desc');

		$queryBuilder->setParameters(array('dateStart' => $dateTime));
		$query = $queryBuilder->getQuery();

		return $query->getResult();
	}
}
