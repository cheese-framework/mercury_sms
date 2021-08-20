<?php

namespace App\I18n;

class Day
{
    public $dayOfMonth;
    public $events;

    public function __construct($dayOfMonth)
    {
        $this->dayOfMonth = $dayOfMonth;
    }

    public function __invoke()
    {
        return $this->dayOfMonth ?? '';
    }
}
