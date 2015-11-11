<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 11:21
 */

namespace DspSofts\CronManagerBundle\Tests\Validator\Constraints;

use DspSofts\CronManagerBundle\Validator\Constraints\Planification;
use DspSofts\CronManagerBundle\Validator\Constraints\PlanificationValidator;
use Symfony\Component\Validator\Context\ExecutionContext;

class PlanificationValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $constraint;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ExecutionContext */
    private $context;

    public function setUp()
    {
        $this->constraint = new Planification();
        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param $value
     * @param $result
     *
     * @dataProvider providerTestValidate
     */
    public function testValidate($value, $result)
    {
        $validator = new PlanificationValidator();
        $validator->initialize($this->context);

        if (!$result) {
            $this->context->expects($this->once())->method('addViolation');
        } else {
            $this->context->expects($this->never())->method('addViolation');
        }

        $validator->validate($value, $this->constraint);
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

    public function tearDown()
    {
        $this->constraint = null;
    }
}
