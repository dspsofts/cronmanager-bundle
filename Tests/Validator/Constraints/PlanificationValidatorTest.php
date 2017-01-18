<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 11:21
 */

namespace DspSofts\CronManagerBundle\Tests\Validator\Constraints;

use DspSofts\CronManagerBundle\Validator\Constraints\Planification;
use DspSofts\CronManagerBundle\Validator\Constraints\PlanificationValidator;

class PlanificationValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $value
     * @param $result
     *
     * @dataProvider providerTestValidate
     */
    public function testValidate($value, $result)
    {
        $constraint = new Planification();
        $context = $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContext')
            ->disableOriginalConstructor()
            ->getMock();

        $validator = new PlanificationValidator();
        $validator->initialize($context);

        if (!$result) {
            $context->expects($this->once())->method('addViolation');
        } else {
            $context->expects($this->never())->method('addViolation');
        }

        $validator->validate($value, $constraint);
    }

    public function providerTestValidate()
    {
        return array(
            array('', false),
            array('* * * * *', true),
            array('* * * * * *', false),
            array('a b c d e', false),
            array('2 * * * *', true),
            array('* * * * 9', false),
            array('* * * * *', true),
            array('* */3 * * *', true),
            array('* 6-8 * * *', true),
            array('* 10-20 * * *', true),
            array('* 10-20/2 * * *', true),
            array('* 6-8,10-20/2 * * *', true),
            array('* 6-8,10-20/2 * * *', true),
        );
    }
}
