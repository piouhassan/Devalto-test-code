<?php

namespace App\Controller;

use App\Calendar\Calendar;
use App\Calendar\XmlDataParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
 private $xmlread;
 private $calendarData;

    public function __construct()
    {
        $this->xmlread = new XmlDataParser('xml/eng-hourly-07012015-07312015.xml');
         $this->calendarData = $this->xmlread->calendarData();
    }
    /**
     * @Route("/", name="home")
     */

    public function home(): Response
    {
         $information = $this->xmlread->getStationInformation();
         $this->xmlread->daysAverage();
        $calendar = new Calendar($this->calendarData);
        $weeks = $calendar->getWeeks();
        $weekdays = $calendar->days;
        $start = $calendar->getStartingDay()->modify('last sunday');
        $temperature = $this->calendarData;
        $average  = $this->xmlread->daysAverage();
        $bestday = $this->xmlread->bestDayForFestival();
        return $this->render('index.html.twig',
            compact('information','weeks','start','weekdays','temperature','average','bestday')
        );
    }


}
