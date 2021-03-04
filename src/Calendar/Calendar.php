<?php
/**
 * Created by PhpStorm.
 * User: Hassan PIOU
 * Date: 28/02/2021
 * Time: 08:37
 */

namespace App\Calendar;


class Calendar implements CalendarInterface
{

   public $days = [
       "Dimanche",
          "Lundi",
          "Mardi",
          "Mercredi",
          "Jeudi",
          "Vendredi",
          "Samedi",
        ];

private $months = ['Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Decembre'];

   private  $intervale = [];

   private $date;

    /**
     * Calendar constructor.
     * @param array $date
     * @throws \Exception
     */
    public function __construct(array $date)
    {
        foreach ($date as $key => $value){
            if ((int) date("Y",strtotime($key)) < 1970 ){
                throw new \Exception("L'année est inférieur à 1970");
            }
        }

            $this->date = $date;
           $this->intervale = $this->startAndEndDate();

    }


    /**
     * @return array
     */
    private function startAndEndDate()
    {
        $dates = [];
        $k = 0;
        foreach ($this->date as $key => $value){
            $dates[$k]  = $key;
            $k++;
        }
        $startDate = $dates[0];
        $endDate = $dates[count($this->date) - 1];

        return [$startDate,$endDate];
    }


    /**
     * @return \DateTime of starting date
     */
    public function getStartingDay():\DateTime
    {
        return new \DateTime($this->intervale[0]);
    }

    /**
     * @return int
     * number of week in the month
     */
    public function getWeeks() : int
    {
        $weeks = (count($this->date) / 7);
        return $weeks;
    }


}