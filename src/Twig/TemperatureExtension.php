<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TemperatureExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('filtertemp', [$this, 'filtertempFunc']),
        ];
    }

    public function filtertempFunc(\DateTime $start,$key,$i,$temp)
    {
        $date =  (clone $start)->modify("+".($key +$i * 7)."days");
         return  $temp[$date->format('Y-m-d')] ?? [];

    }
}
