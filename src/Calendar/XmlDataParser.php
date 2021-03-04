<?php
/**
 * Created by PhpStorm.
 * User: Hassan PIOU
 * Date: 28/02/2021
 * Time: 08:36
 */

namespace App\Calendar;


class XmlDataParser implements XmlDataParserInterface
{
    private $climatedata;
    private $stationinformation;
    private $stationdata = [];


    /**
     * XmlDataParser constructor.
     * @param $xml_file
     * in param the path to xml file
     */
    public function __construct($xml_file)
    {
        $xml = simplexml_load_file($xml_file);
        $this->climatedata = $xml->children();
    }

    /**
     * @return \SimpleXMLElement
     * for the title
     */
    public function getStationInformation():\SimpleXMLElement{
        $this->stationinformation = $this->climatedata->stationinformation;
        return $this->stationinformation;
    }


    /**
     * Total is the sum of all temperature
     * @return array of tree elements
     *  first : array without hours
     *  Second : array with hours
     * third : array with hours in the outdoor festival date range
     */
    public function reorderData():array {
        $dates = [];
        $stationdata = $this->climatedata->stationdata;
        $secondDates = [];
        $festivalInterval = [];
        $festival_start = "2015-07-09";
        $festival_end = "2015-07-19";

        for ($k = 0; $k < count($stationdata);$k++){
            $data= (array) $stationdata[$k];

            /*
             * Build Date like key for daily temperature
             */

            if ( $data['@attributes']['day'] < 10){
                $data['@attributes']['day'] = "0".$data['@attributes']['day'];
            }

            if ($data['@attributes']['month'] < 10){
                $data['@attributes']['month'] = "0".$data['@attributes']['month'];
            }

            $key =
                $data['@attributes']['year']."-"
                .$data['@attributes']['month']."-"
                .$data['@attributes']['day']
            ;

            /*
            * Build Date like key for show outdoor musical and best period
            */
            $secondkey =
                $data['@attributes']['year']."-"
                .$data['@attributes']['month']."-"
                .$data['@attributes']['day'].'##'
                .$data['@attributes']['hour']
            ;

            /*
             *   Build value for daily temperature
             */
            if (array_key_exists($key,$dates)){
                $mydate = $dates[$key];
                if (!is_object($data['temp'])){
                    if ($mydate[0] < $data['temp'])  $mydate[0] = $data['temp'];
                    if ($mydate[1] > $data['temp'])  $mydate[1] = $data['temp'];
                }
                $mydate[2] += $data['temp'];

//               Set value to key
                $dates[$key] = $mydate;
            }
            else{
                $dates[$key] = [$data['temp'],$data['temp'],$data['temp']];
            }


            /*
        *   Build value for best period
        */
            if (array_key_exists($secondkey,$secondDates)){
                $mydate = $secondDates[$secondkey];
                if (is_object($data['temp'])) $data['temp'] = 0;
                    if ($mydate[0] < $data['temp'])  $mydate[0] = $data['temp'];
//               Set value to key
                $secondDates[$secondkey] = $mydate;
            }
            else{
                $secondDates[$secondkey] = $data['temp'];
            }


            /*
             * Festival  regroup Date
             */

           if (
               strtotime(explode('##',$secondkey)[0]) >= strtotime($festival_start)
               AND strtotime(explode('##',$secondkey)[0])<= strtotime($festival_end)
           ){
               if (array_key_exists($secondkey,$festivalInterval)){
                   $mydate = $festivalInterval[$secondkey];
                   if (is_object($data['temp'])) $data['temp'] = 0;
                   if ($mydate[0] < $data['temp'])  $mydate[0] = $data['temp'];
//               Set value to key
                   $festivalInterval[$secondkey] = $mydate;
               }
               else{
                   $festivalInterval[$secondkey] = $data['temp'];
               }
           }

        }

        return [$dates,$secondDates,$festivalInterval];
    }



    /** Array look like  date = [max,min,average]
     * @return array
     */
     public function calendarData():array
     {

       //  Get average from orderDate
        $firstcalendarData = [];
         $data = $this->reorderData()[0];
        foreach ($data as $key => $value){
            $firstcalendarData[$key] =[0 => $value[0], 1 => $value[1], 2 => number_format(($value[2] / 24), 1,'.','.')];
        }

        return $firstcalendarData;
    }


    /**
     *  return the auspicious date for an outdoor festival
     * Considering the human body temperature which is 37°,
     * the best time for outdoor festival is when temperature is high
     * @return string
     */
    public function bestDayForFestival():string
    {
            $data = $this->reorderData()[2];
             $evening = [];
           foreach ($data as $key => $value){
            if (explode('##',$key)[1] < 18 AND explode('##',$key)[1] >11 ){
                $evening += [$key => $value];
            }
        }
            $maxtemp = max($evening);
           $maxtempKey = array_search($maxtemp,$evening);

    return    explode('##',$maxtempKey)[0];
    }

    /**
     *  return morning,afternoon,evening and night average temperature
     * @return object
     */
    public function  daysAverage():object
    {
        $morning = 0;
        $afternoon = 0;
        $evening = 0;
        $night = 0;
        $hour_count = 0;

        $data = $this->reorderData()[1];
        foreach ($data as $key => $value){
            if (explode('##',$key)[1] < 6){
                $morning +=  $value;
            }

            if (explode('##',$key)[1] < 12 AND explode('##',$key)[1] >5 ){
                $afternoon += $value;
            }

            if (explode('##',$key)[1] < 18 AND explode('##',$key)[1] >11 ){
                $evening += $value;
            }

            if (explode('##',$key)[1] < 24 AND explode('##',$key)[1] >17 ){
                $night += $value;
                $hour_count++;
            }
        }

        $morning_average = ($morning /  $hour_count);
        $afternoon_average = ($afternoon / $hour_count);
        $evening_average =  ($evening / $hour_count);
        $night_average =  ($night / $hour_count);


        return (object)([
            'morning' => number_format($morning_average,1,'.','.'),
            'afternoon' => number_format($afternoon_average,1,'.','.'),
            'evening' => number_format($evening_average,1,'.','.'),
            'night' => number_format($night_average,1,'.','.'),
        ]);
    }




}