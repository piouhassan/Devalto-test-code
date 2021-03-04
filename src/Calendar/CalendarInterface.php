<?php
/**
 * Created by PhpStorm.
 * User: Hassan PIOU
 * Date: 28/02/2021
 * Time: 08:35
 */
namespace App\Calendar;


interface CalendarInterface
{

    /**
     * @return \DateTime of starting date
     */
     public function getStartingDay():\DateTime;


    /**
     * @return int
     * number of week in the month
     */
     public function getWeeks():int;

}