<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 09:19
 */

namespace DspSofts\CronManagerBundle\Tests\Form;

use DspSofts\CronManagerBundle\Entity\Search\CronTaskLogSearch;
use DspSofts\CronManagerBundle\Form\CronTaskLogSearchType;
use Symfony\Component\Form\Test\TypeTestCase;

class CronTaskLogSearchTypeTest extends TypeTestCase
{
    public function testSubmit()
    {
        $dateTime = new \DateTime();
        $dateTime->setTime(0, 0, 0);
        $dateTime->sub(new \DateInterval('P7D'));

        $cronTaskLogSearch = new CronTaskLogSearch();
        $cronTaskLogSearch->setDateStart($dateTime);

        $form = $this->factory->create(CronTaskLogSearchType::class, $cronTaskLogSearch);

        $formData = array(
            'dateStart' => $dateTime,
        );

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($cronTaskLogSearch, $form->getData());
    }
}
