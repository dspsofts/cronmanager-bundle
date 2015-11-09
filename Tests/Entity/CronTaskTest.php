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
    public function testFields()
    {
        $cronTask = new CronTask();

        $this->assertNull($cronTask->getId());

        $cronTask->setName('test');
        $this->assertEquals('test', $cronTask->getName());

        $cronTask->setCommand('test');
        $this->assertEquals('test', $cronTask->getCommand());

        $cronTask->setIsActive(true);
        $this->assertTrue($cronTask->getIsActive());

        $cronTask->setPlanification('* * * * *');
        $this->assertEquals('* * * * *', $cronTask->getPlanification());

        $this->assertNull($cronTask->getTimeout());
        $cronTask->setTimeout(1200);
        $this->assertEquals(1200, $cronTask->getTimeout());

        $cronTask->setType(CronTask::TYPE_SYMFONY);
        $this->assertEquals(CronTask::TYPE_SYMFONY, $cronTask->getType());

        $testDate = new \DateTime();
        $cronTask->setLastRun($testDate);
        $this->assertEquals($testDate, $cronTask->getLastRun());
    }
}
