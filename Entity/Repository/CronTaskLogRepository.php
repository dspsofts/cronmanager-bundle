<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 04/11/15 22:11
 */

namespace DspSofts\CronManagerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;
use DspSofts\CronManagerBundle\Entity\Search\CronTaskLogSearch;

class CronTaskLogRepository extends EntityRepository
{
    /**
     * @param CronTaskLogSearch $cronTaskLogSearch
     * @return CronTaskLog[]
     */
    public function searchRunning(CronTaskLogSearch $cronTaskLogSearch)
    {
        $queryBuilder = $this->createQueryBuilder('cron_task_log');
        $queryBuilder->where('cron_task_log.pid IS NOT NULL');
        $queryBuilder->orderBy('cron_task_log.dateStart', 'desc');

        $params = array();

        if ($cronTaskLogSearch->getCronTask() !== null) {
            $queryBuilder->andWhere('cron_task_log.cronTask = :cronTask');
            $params['cronTask'] = $cronTaskLogSearch->getCronTask();
        }

        $queryBuilder->setParameters($params);
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    /**
     * @param CronTaskLogSearch $cronTaskLogSearch
     * @return CronTaskLog[]
     */
    public function searchFinished(CronTaskLogSearch $cronTaskLogSearch)
    {
        $queryBuilder = $this->createQueryBuilder('cron_task_log');
        $queryBuilder->select('cron_task_log');
        $queryBuilder->where('cron_task_log.pid IS NULL');
        $queryBuilder->andWhere('cron_task_log.dateStart >= :dateStart');

        $queryBuilder->orderBy('cron_task_log.dateStart', 'desc');

        $params = array(
            'dateStart' => $cronTaskLogSearch->getDateStart(),
        );

        if ($cronTaskLogSearch->getCronTask() !== null) {
            $queryBuilder->andWhere('cron_task_log.cronTask = :cronTask');
            $params['cronTask'] = $cronTaskLogSearch->getCronTask();
        } else {
            $queryBuilder->groupBy('cron_task_log.cronTask, cron_task_log.status');
        }

        $queryBuilder->setParameters($params);
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
