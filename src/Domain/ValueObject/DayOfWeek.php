<?php

namespace TVListings\Domain\ValueObject;

final class DayOfWeek
{
    const MON = 'Даваа';
    const TUE = 'Мягмар';
    const WED = 'Лхагва';
    const THU = 'Пүрэв';
    const FRI = 'Баасан';
    const SAT = 'Бямба';
    const SUN = 'Ням';

    /**
     * var \DateTime
     */
    private $dateTime;

    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function __toString()
    {
        $daysOfWeek = array(
            1 => self::MON,
            2 => self::TUE,
            3 => self::WED,
            4 => self::THU,
            5 => self::FRI,
            6 => self::SAT,
            7 => self::SUN,
        );

        return $daysOfWeek[$this->dateTime->format('N')];
    }
}
