<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 02/11/15 11:36
 */

namespace DspSofts\CronManagerBundle\Tests\Util;

use DspSofts\CronManagerBundle\Util\PlanificationChecker;

class PlanificationCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerPlanification
     */
    public function testPlanificationEntry($planification, \DateTime $timestamp, $expected)
    {
        $planificationChecker = new PlanificationChecker($timestamp);
        $actual = $planificationChecker->isExecutionDue($planification);
        $this->assertEquals($expected, $actual);
    }

    public function providerPlanification()
    {
        return array(
            array('* * * * *', new \DateTime(), true),
            array('* */3 * * *', \DateTime::createFromFormat('Y-m-d H:i:s', '2012-05-07 18:30:00'), true),
            array('* 6-8 * * *', \DateTime::createFromFormat('Y-m-d H:i:s', '2012-05-07 18:30:00'), false),
            array('* 10-20 * * *', \DateTime::createFromFormat('Y-m-d H:i:s', '2012-05-07 18:30:00'), true),
            array('* 10-20/2 * * *', \DateTime::createFromFormat('Y-m-d H:i:s', '2012-05-07 18:30:00'), true),
            array('* 6-8,10-20/2 * * *', \DateTime::createFromFormat('Y-m-d H:i:s', '2012-05-07 18:30:00'), true),
            array('* 6-8,10-20/2 * * *', \DateTime::createFromFormat('Y-m-d H:i:s', '2012-05-07 19:30:00'), false),
        );
    }
}
