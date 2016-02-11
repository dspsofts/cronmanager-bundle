<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 10/11/15 08:33
 */

namespace DspSofts\CronManagerBundle\Entity\Search;

use DspSofts\CronManagerBundle\Entity\CronTask;

class CronTaskLogSearch
{
    /** @var \DateTime */
    private $dateStart;

    /** @var CronTask */
    private $cronTask;

    public function __construct()
    {
        $this->dateStart = new \DateTime();
        $this->dateStart->setTime(0, 0, 0);
    }

    /**
     * @param \DateTime $dateStart
     * @return CronTaskLogSearch
     */
    public function setDateStart(\DateTime $dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @return CronTask
     */
    public function getCronTask()
    {
        return $this->cronTask;
    }

    /**
     * @param CronTask $cronTask
     * @return CronTaskLogSearch
     */
    public function setCronTask($cronTask)
    {
        $this->cronTask = $cronTask;

        return $this;
    }

}
