<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 10/11/15 08:33
 */

namespace DspSofts\CronManagerBundle\Entity\Search;

class CronTaskLogSearch
{
    /** @var \DateTime */
    private $dateStart;

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

    public function __construct()
    {
        $this->dateStart = new \DateTime();
        $this->dateStart->setTime(0, 0, 0);
    }
}
