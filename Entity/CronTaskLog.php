<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 01/11/15 13:05
 */

namespace DspSofts\CronManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *      @ORM\Index(name="date_start", columns={"date_start"}),
 * 	    @ORM\Index(name="date_end", columns={"date_end"}),
 * 	    @ORM\Index(name="status", columns={"status"})
 * })
 */
class CronTaskLog
{
    const STATUS_RUNNING = 'RUNNING';
    const STATUS_OK = 'OK';
    const STATUS_FAILED = 'FAILED';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_cron_task_log", type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="DspSofts\CronManagerBundle\Entity\CronTask")
     * @ORM\JoinColumn(name="id_cron_task", referencedColumnName="id_cron_task")
     */
    private $cronTask;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateStart;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEnd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filePath;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return CronTaskLog
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return CronTaskLog
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return CronTaskLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set cronTask
     *
     * @param \DspSofts\CronManagerBundle\Entity\CronTask $cronTask
     *
     * @return CronTaskLog
     */
    public function setCronTask(\DspSofts\CronManagerBundle\Entity\CronTask $cronTask = null)
    {
        $this->cronTask = $cronTask;

        return $this;
    }

    /**
     * Get cronTask
     *
     * @return \DspSofts\CronManagerBundle\Entity\CronTask
     */
    public function getCronTask()
    {
        return $this->cronTask;
    }

    /**
     * Set filePath
     *
     * @param string $filePath
     *
     * @return CronTaskLog
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
