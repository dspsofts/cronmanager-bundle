<?php

/**
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 09/11/15 01:12
 */

namespace DspSofts\CronManagerBundle\Tests\Entity;

use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Entity\CronTaskLog;

class CronTaskLogTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $cronTaskLog = new CronTaskLog();
        $this->assertNull($cronTaskLog->getId());
    }

    public function testStatus()
    {
        $cronTaskLog = new CronTaskLog();
        $cronTaskLog->setStatus(CronTaskLog::STATUS_SUCCESS);
        $this->assertEquals(CronTaskLog::STATUS_SUCCESS, $cronTaskLog->getStatus());
    }

    public function testDateStart()
    {
        $testDate = new \DateTime();
        $cronTaskLog = new CronTaskLog();
        $cronTaskLog->setDateStart($testDate);
        $this->assertEquals($testDate, $cronTaskLog->getDateStart());
    }

    public function testDateEnd()
    {
        $testDate = new \DateTime();
        $cronTaskLog = new CronTaskLog();
        $this->assertNull($cronTaskLog->getDateEnd());
        $cronTaskLog->setDateEnd($testDate);
        $this->assertEquals($testDate, $cronTaskLog->getDateEnd());
    }

    public function testFilePath()
    {
        $cronTaskLog = new CronTaskLog();
        $cronTaskLog->setFilePath('test');
        $this->assertEquals('test', $cronTaskLog->getFilePath());
    }

    public function testPid()
    {
        $cronTaskLog = new CronTaskLog();
        $cronTaskLog->setPid(123);
        $this->assertEquals(123, $cronTaskLog->getPid());
    }

    public function testCronTask()
    {
        $cronTask = new CronTask();
        $cronTask->setName('test');

        $cronTaskLog = new CronTaskLog();
        $cronTaskLog->setCronTask($cronTask);

        $this->assertEquals($cronTask, $cronTaskLog->getCronTask());
    }
}

