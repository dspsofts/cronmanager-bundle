<?php

/**
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 09/11/15 01:12
 */

namespace DspSofts\CronManagerBundle\Tests\Entity;

use DspSofts\CronManagerBundle\Entity\CronTaskLog;

class CronTaskLogTest extends \PHPUnit_Framework_TestCase
{
    public function testFields()
    {
        $cronTaskLog = new CronTaskLog();

        $this->assertNull($cronTaskLog->getId());

        $cronTaskLog->setStatus(CronTaskLog::STATUS_SUCCESS);
        $this->assertEquals(CronTaskLog::STATUS_SUCCESS, $cronTaskLog->getStatus());

        $testDate = new \DateTime();
        $cronTaskLog->setDateStart($testDate);
        $this->assertEquals($testDate, $cronTaskLog->getDateStart());

        $this->assertNull($cronTaskLog->getDateEnd());
        $cronTaskLog->setDateEnd($testDate);
        $this->assertEquals($testDate, $cronTaskLog->getDateEnd());

        $cronTaskLog->setFilePath('test');
        $this->assertEquals('test', $cronTaskLog->getFilePath());

        $cronTaskLog->setPid('123');
        $this->assertEquals('123', $cronTaskLog->getPid());
    }
}
