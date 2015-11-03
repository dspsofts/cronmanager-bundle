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
    }
}
