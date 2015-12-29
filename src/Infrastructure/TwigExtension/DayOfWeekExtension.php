<?php

namespace TVListings\Infrastructure\TwigExtension;

use TVListings\Domain\ValueObject\DayOfWeek;

class DayOfWeekExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'dayOfWeek';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('dayOfWeek', array($this, 'getDayOfWeek')),
        );
    }

    public function getDayOfWeek(\DateTimeImmutable $specifiedDate)
    {
        return (string) new DayOfWeek($specifiedDate);
    }
}
