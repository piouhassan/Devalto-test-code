<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CalendarExtension extends AbstractExtension
{


    public function getFunctions(): array
    {
        return [
            new TwigFunction('calendarformat', [$this, 'calendarDateFormat']),
        ];
    }

    public function calendarDateFormat(\DateTime $start,$key,$i)
    {
        return (clone $start)->modify("+".($key +$i * 7)."days")->format('d');
    }
}
