<?php

namespace App\I18n;

use DateTime;
use DatePeriod;
use DateInterval;
use InvalidArgumentException;
use Exception;

class Event
{
    const INTERVAL_DAY = 'P%dD';
    const INTERVAL_WEEK = 'P%dW';
    const INTERVAL_MONTH = 'P%dM';
    const FLAG_FIRST = 'FIRST';
    // 1st of the month
    const ERROR_INVALID_END = 'Need to supply either # occurrences or
an end date';
    const ERROR_INVALID_DATE = 'String i.e. YYYY-mm-dd or DateTime
instance only';
    const ERROR_INVALID_INTERVAL = 'Interval must take the form "P\
d+(D | W | M)"';

    public $id;
    public $flag;
    public $value;
    public $title;
    public $locale;
    public $interval;
    public $description;
    public $occurrences;
    public $nextDate;
    protected $endDate;
    protected $startDate;

    public function __construct(
        $id,
        $title,
        $description,
        $startDate,
        $interval,
        $value,
        $occurrences = NULL,
        $endDate = NULL,
        $flag = NULL
    ) {
        $this->id = $id;
        $this->flag = $flag;
        $this->value = $value;
        $this->title = $title;
        $this->description = $description;
        $this->occurrences = $occurrences;


        try {
            $this->interval = new DateInterval(sprintf($interval, $value));
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new InvalidArgumentException(self::ERROR_INVALID_INTERVAL);
        }

        $this->startDate = $this->stringOrDate($startDate);
        if ($endDate) {
            $this->endDate = $this->stringOrDate($endDate);
        } elseif ($occurrences) {
            $this->endDate = $this->calcEndDateFromOccurrences();
        } else {
            throw new InvalidArgumentException(self::ERROR_INVALID_END);
        }
        $this->nextDate = $this->startDate;
    }

    protected function stringOrDate($date)
    {
        if ($date === NULL) {
            $newDate = NULL;
        } elseif ($date instanceof DateTime) {
            $newDate = $date;
        } elseif (is_string($date)) {
            $newDate = new DateTime($date);
        } else {
            throw new InvalidArgumentException(self::ERROR_INVALID_END);
        }
        return $newDate;
    }


    protected function calcEndDateFromOccurrences()
    {
        $endDate = new DateTime('now');
        $period = new DatePeriod(
            $this->startDate,
            $this->interval,
            $this->occurrences
        );
        foreach ($period as $date) {
            $endDate = $date;
        }
        return $endDate;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getNextDate(DateTime $today)
    {
        if ($today > $this->endDate) {
            return FALSE;
        }
        $next = clone $today;
        $next->add($this->interval);
        return $next;
    }
}
