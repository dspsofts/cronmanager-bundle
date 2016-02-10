<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 10/02/16 12:16
 */

namespace DspSofts\CronManagerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use DspSofts\CronManagerBundle\Entity\CronTask;

class CronTaskRepository extends EntityRepository
{
    /**
     * @return CronTask[]
     */
    public function findCronsToLaunch()
    {
        $queryBuilder = $this->createQueryBuilder('cron_task');
        $queryBuilder->where('cron_task.isActive = 1');
        $queryBuilder->orWhere('cron_task.relaunch = 1');

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
