<?php
/**
 * Created by PhpStorm.
 * User: Hassan PIOU
 * Date: 28/02/2021
 * Time: 08:35
 */

namespace App\Calendar;


interface XmlDataParserInterface
{

    /**
     * @return \SimpleXMLElement
     * for the title
     */
    public  function getStationInformation():\SimpleXMLElement;
    /**
     * Total is the sum of all temperature
     * @return array of tree elements
     *  first : array without hours
     *  Second : array with hours
     * third : array with hours in the outdoor festival date range
     */
    public  function reorderData():array;

    /** Array look like  date = [max,min,average]
     * @return array
     */
    public  function calendarData():array;

    /**
     *  return the auspicious date for an outdoor festival
     * Considering the human body temperature which is 37°,
     * the best time for outdoor festival is when temperature is high
     * @return string
     */
    public  function bestDayForFestival():string;

    /**
     *  return morning,afternoon,evening and night average temperature
     * @return object
     */
    public  function daysAverage():object;
}