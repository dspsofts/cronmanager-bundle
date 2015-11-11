<?php

/**
 *
 * @author Pierre Feyssaguet <pfeyssaguet@gmail.com>
 * @since 11/11/15 09:07
 */

namespace DspSofts\CronManagerBundle\Tests\Entity\Search;

use DspSofts\CronManagerBundle\Entity\Search\CronTaskLogSearch;

class CronTasKLogSearchTest extends \PHPUnit_Framework_TestCase
{
    public function testDateStart()
    {
        $cronTaskLogSearch = new CronTaskLogSearch();
        $dateTime = new \DateTime();
        $dateTime->setTime(0, 0, 0);

        $this->assertEquals($dateTime, $cronTaskLogSearch->getDateStart());
    }
}
