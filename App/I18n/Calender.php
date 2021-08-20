<?php

namespace App\I18n;

use App\Notifiable\Components\Components;
use IntlCalendar;

class Calender
{
    const DAY_1 = 'EEEEE'; // T
    const DAY_2 = 'EEEEEE'; // Tu
    const DAY_3 = 'EEE'; // Tue
    const DAY_FULL = 'EEEE'; // Tuesday
    const MONTH_1 = 'MMMMM'; // M
    const MONTH_3 = 'MMM'; // Mar
    const MONTH_FULL = 'MMMM'; // March
    const DEFAULT_ACROSS = 3;
    const HEIGHT_FULL = '150px';
    const HEIGHT_SMALL = '60px';

    protected $locale;
    protected $dateFormatter;
    protected $yearArray;
    protected $height;
    protected $events = array();

    public function __construct(Local $locale)
    {
        $this->locale = $locale;
    }

    protected function getDateFormatter()
    {
        if (!$this->dateFormatter) {
            $this->dateFormatter =
                $this->locale->getDateFormatter(Local::DATE_TYPE_FULL);
        }
        return $this->dateFormatter;
    }


    public function buildMonthArray($year, $month, $timeZone =
    NULL)
    {
        $month -= 1;
        //IntlCalendar months are 0 based; Jan==0, Feb==1 and so on
        $day = 1;
        $first = TRUE;
        $value = 0;
        $monthArray = array();
        $cal = IntlCalendar::createInstance(
            $timeZone,
            $this->locale->getLocaleCode()
        );
        $cal->set($year, $month, $day);
        $maxDaysInMonth = $cal
            ->getActualMaximum(IntlCalendar::FIELD_DAY_OF_MONTH);
        $formatter = $this->getDateFormatter();
        $formatter->setPattern('e');
        $firstDayIsWhatDow = $formatter->format($cal);
        while ($day <= $maxDaysInMonth) {
            for ($dow = 1; $dow <= 7; $dow++) {
                $cal->set($year, $month, $day);
                $weekOfYear = $cal
                    ->get(IntlCalendar::FIELD_WEEK_OF_YEAR);

                if ($weekOfYear > 52) $weekOfYear = 0;
                if ($first) {
                    if ($dow == $firstDayIsWhatDow) {
                        $first = FALSE;
                        $value = $day++;
                    } else {
                        $value = NULL;
                    }
                } else {
                    if ($day <= $maxDaysInMonth) {
                        $value = $day++;
                    } else {
                        $value = NULL;
                    }
                }
                $monthArray[$weekOfYear][$dow] = new Day($value);
                $dayObj = $this->processEvents(new Day($value), $cal);
                $monthArray[$weekOfYear][$dow] = $dayObj;
            }
        }
        return $monthArray;
    }

    protected function getDay($type, $cal)
    {
        $formatter = $this->getDateFormatter();
        $formatter->setPattern($type);
        return $formatter->format($cal);
    }

    protected function getWeekHeaderRow(
        $type,
        $cal,
        $year,
        $month,
        $week
    ) {
        $output = '<tr>';
        $width = (int) (100 / 7);
        foreach ($week as $day) {
            $cal->set($year, $month, $day());
            $output .= '<th class="p-3" style="vertical-align:top;"
width="' . $width . '%">'
                . $this->getDay($type, $cal) . '</th>';
        }
        $output .= '</tr>' . PHP_EOL;
        return $output;
    }

    protected function getWeekDaysRow($type, $week)
    {
        $output = '<tr class="text-github" style="height:' . $this->height . ';">';
        $width = (int) (100 / 7);
        foreach ($week as $day) {
            $events = '';
            if ($day->events) {
                foreach ($day->events as $single) {

                    $events .= '<br><span class="text-small text-info">' . $single->title . '</span><br>';

                    if ($type == self::DAY_FULL) {
                        $events .= '<br><i style="font-size: 7px">' . $single->description . '<br></i>';
                    }
                }
            }
            $output .= '<td style="vertical-align:top;"
width="' . $width . '%">'
                . $day() . '<br>' . $events . '</td>';
        }
        $output .= '</tr>' . PHP_EOL;
        return $output;
    }

    public function calendarForMonth(
        $year,
        $month,
        $timeZone = NULL,
        $dayType = self::DAY_3,
        $monthType = self::MONTH_FULL,
        $monthArray = NULL
    ) {
        $first = 0;
        if (!$monthArray)
            $monthArray = $this->yearArray[$year][$month]
                ?? $this->buildMonthArray($year, $month, $timeZone);
        $month--;
        $cal = IntlCalendar::createInstance(
            $timeZone,
            $this->locale->getLocaleCode()
        );
        $cal->set($year, $month, 1);
        $formatter = $this->getDateFormatter();
        $formatter->setPattern($monthType);
        $this->height = ($dayType == self::DAY_FULL)
            ? self::HEIGHT_SMALL : self::HEIGHT_SMALL;
        $html = '<div class="text-center">' . Components::header($formatter->format($cal), "h4") . '</div>';
        $header = '';
        $body
            = '';
        foreach ($monthArray as $weekNum => $week) {
            if ($first++ == 1) {
                $header .= $this->getWeekHeaderRow(
                    $dayType,
                    $cal,
                    $year,
                    $month,
                    $week
                );
            }
            $body .= $this->getWeekDaysRow($dayType, $week);
        }
        $html .= '<div class="table-responsive col-lg-9 mx-auto"><table class="table table-sm table-bordered table-striped">' . $header . $body .
            '</table></div><br>' . PHP_EOL;
        return $html;
    }


    public function buildYearArray($year, $timeZone = NULL)
    {
        $this->yearArray = array();
        for ($month = 1; $month <= 12; $month++) {
            $this->yearArray[$year][$month] =
                $this->buildMonthArray($year, $month, $timeZone);
        }
        return $this->yearArray;
    }

    public function getYearArray()
    {
        return $this->yearArray;
    }

    public function calendarForYear(
        $year,
        $timeZone = NULL,
        $dayType = self::DAY_1,
        $monthType = self::MONTH_3,
        $across = self::DEFAULT_ACROSS
    ) {
        if (!$this->yearArray) $this->buildYearArray(
            $year,
            $timeZone
        );
        $yMax = (int) (12 / $across);
        $width = (int) (100 / $across);
        $output = '<div class="table-responsive col-lg-12"><table class="table table-sm">' . PHP_EOL;
        $month = 1;
        for ($y = 1; $y <= $yMax; $y++) {
            $output .= '<tr>';
            for ($x = 1; $x <= $across; $x++) {
                $output .= '<td style="vertical-align:top;"
width="' . $width . '%">'
                    . $this->calendarForMonth(
                        $year,
                        $month,
                        $timeZone,
                        $dayType,
                        $monthType,
                        $this->yearArray[$year][$month++]
                    ) . '</td>';
            }
            $output .= '</tr>' . PHP_EOL;
        }
        $output .= '</table></div>';
        return $output;
    }

    public function addEvent(Event $event)
    {
        $this->events[$event->id] = $event;
    }

    protected function processEvents($dayObj, $cal)
    {
        if ($this->events && $dayObj()) {
            $calDateTime = $cal->toDateTime();
            foreach ($this->events as $id => $eventObj) {
                $next = $eventObj->getNextDate($eventObj->nextDate);

                if ($next) {
                    if (
                        $calDateTime->format('Y-m-d') ==
                        $eventObj->nextDate->format('Y-m-d')
                    ) {
                        $dayObj->events[$eventObj->id] = $eventObj;
                        $eventObj->nextDate = $next;
                    }
                }
            }
        }
        return $dayObj;
    }
}
