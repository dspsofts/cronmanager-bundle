<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 30/04/15 17:06
 */

namespace DspSofts\CronManagerBundle\Tests\Entity;

use DspSofts\CronManagerBundle\Entity\CronTask;

class CronTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $cronTask = new CronTask();
        $this->assertNull($cronTask->getId());
    }

    public function testName()
    {
        $cronTask = new CronTask();
        $cronTask->setName('test');
        $this->assertEquals('test', $cronTask->getName());
    }

    public function testCommand()
    {
        $cronTask = new CronTask();
        $cronTask->setCommand('test');
        $this->assertEquals('test', $cronTask->getCommand());
    }

    public function testPlanification()
    {
        $cronTask = new CronTask();
        $cronTask->setPlanification('* * * * *');
        $this->assertEquals('* * * * *', $cronTask->getPlanification());
    }

    public function testTimeout()
    {
        $cronTask = new CronTask();
        $this->assertNull($cronTask->getTimeout());

        $cronTask->setTimeout(1200);
        $this->assertEquals(1200, $cronTask->getTimeout());
    }

    public function testType()
    {
        $cronTask = new CronTask();
        $cronTask->setType(CronTask::TYPE_SYMFONY);
        $this->assertEquals(CronTask::TYPE_SYMFONY, $cronTask->getType());
    }

    public function testLastRun()
    {
        $testDate = new \DateTime();
        $cronTask = new CronTask();
        $cronTask->setLastRun($testDate);
        $this->assertEquals($testDate, $cronTask->getLastRun());
    }

    public function testIsActive()
    {
        $cronTask = new CronTask();
        $this->assertFalse($cronTask->getIsActive());
        $cronTask->setIsActive(true);
        $this->assertTrue($cronTask->getIsActive());
    }

    public function testIsUnique()
    {
        $cronTask = new CronTask();
        $this->assertTrue($cronTask->getIsUnique());
        $cronTask->setIsUnique(false);
        $this->assertFalse($cronTask->getIsUnique());
    }

    public function testSlug()
    {
        $cronTask = new CronTask();
        $cronTask->setSlug('test');
        $this->assertEquals('test', $cronTask->getSlug());
    }
}
