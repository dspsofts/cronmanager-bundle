<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 10:45
 */

namespace DspSofts\CronManagerBundle\Tests\Command;

class RunCommandTest extends CommandTestCase
{
    public function testRunJob()
    {
        $cronManipulator = $this->getMockBuilder('DspSofts\CronManagerBundle\Util\CronManipulator')
            ->disableOriginalConstructor()
            ->getMock();

        $client = self::createClient();

        $client->getContainer()->set('dsp_cm.util.cron_manipulator', $cronManipulator);

        $cronManipulator->expects($this->once())->method('checkRunningCrons');
        $cronManipulator->expects($this->once())->method('runCrons');
        $this->runCommand($client, 'dsp:cron:run');
    }
}
