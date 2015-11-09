<?php

/**
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 09/11/15 01:00
 */

namespace DspSofts\CronManagerBundle\Tests\Form;

use DspSofts\CronManagerBundle\Entity\CronTask;
use DspSofts\CronManagerBundle\Form\CronTaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class CronTaskTypeTest extends TypeTestCase
{
    public function testSubmit()
    {
        $cronTask = new CronTask();

        $form = $this->factory->create(new CronTaskType(), $cronTask);
        $formData = array(
            'name' => 'test cron task',
            'planification' => '* * * * *',
            'type' => CronTask::TYPE_SYMFONY,
            'command' => 'assets:deploy --symlink',
            'isActive' => true,
        );

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($cronTask, $form->getData());
    }
}
