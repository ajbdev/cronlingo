<?php


namespace CronLingo;

/**
 * Represents a CRON expression
 *
 * Class Cron
 * @package CronLingo
 */
class Cron
{
    /**
     * @var Field
     */
    public $dayOfWeek;
    /**
     * @var Field
     */
    public $month;
    /**
     * @var Field
     */
    public $dayOfMonth;
    /**
     * @var Field
     */
    public $hour;
    /**
     * @var Field
     */
    public $minute;

    public function __construct()
    {
        $this->dayOfWeek = new Field();
        $this->month = new Field();
        $this->dayOfMonth = new Field();
        $this->hour = new Field();
        $this->minute = new Field();
    }

    /**
     * Get CRON expression field order in structured format
     *
     * @return array
     */
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

    /**
     * @return string
     */
    public function __toString()
    {
        return trim(implode(' ', $this->ordered()));
    }
}