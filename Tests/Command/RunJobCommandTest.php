<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 10:31
 */

namespace DspSofts\CronManagerBundle\Tests\Command;

class RunJobCommandTest extends CommandTestCase
{
    public function testRunJob()
    {
        $jobRunner = $this->getMockBuilder('DspSofts\CronManagerBundle\Util\JobRunner')
            ->disableOriginalConstructor()
            ->getMock();

        $client = self::createClient();

        $client->getContainer()->set('dsp_cm.util.job_runner', $jobRunner);

        $jobRunner->expects($this->once())->method('runJob')->with('test');
        $this->runCommand($client, 'dsp:cron:runjob -c test');
    }
}
