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
		$queryBuilder->orderBy('cron_task_log.dateStart');

		$query = $queryBuilder->getQuery();

		return $query->getResult();
	}
}
