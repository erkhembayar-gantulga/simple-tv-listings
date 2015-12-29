<?php

namespace TVListings\Tests\ValueObject;

use TVListings\Domain\ValueObject\DayOfWeek;

class DayOfWeekTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     */
    public function it_should_return_name_of_week_of_the_date($dateString, $expectedDayOfWeek)
    {
        $this->assertEquals($expectedDayOfWeek, (string) new DayOfWeek(new \DateTimeImmutable($dateString)));
    }

    public function dataProvider()
    {
        return array(
            array('2015-12-28', 'Даваа'),
            array('2015-12-29', 'Мягмар'),
            array('2015-12-30', 'Лхагва'),
            array('2015-12-31', 'Пүрэв'),
            array('2016-01-01', 'Баасан'),
            array('2016-01-02', 'Бямба'),
            array('2016-01-03', 'Ням'),
        );
    }
}
