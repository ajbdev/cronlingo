<?php


namespace CronLingo;


class Cron
{
    public $dayOfWeek;
    public $month;
    public $dayOfMonth;
    public $hour;
    public $minute;

    public function __construct()
    {
        $this->dayOfWeek = new Field();
        $this->month = new Field();
        $this->dayOfMonth = new Field();
        $this->hour = new Field();
        $this->minute = new Field();
    }

    public function ordered()
    {
        return [
            $this->minute,
            $this->hour,
            $this->dayOfMonth,
            $this->month,
            $this->dayOfWeek
        ];
    }

    public function __toString()
    {
        return trim(implode(' ', $this->ordered()));
    }
}