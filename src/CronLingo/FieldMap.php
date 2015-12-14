<?php


namespace CronLingo;


class FieldMap
{
    /**
     * @var array
     */
    public static $fieldMap = [
        'day' => 'dayOfMonth'
    ];
    /**
     * @var array
     */
    public static $dayOfWeekMap = [
        'sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
        'thursday' => 4, 'friday' => 5, 'saturday' => 6
    ];
    /**
     * @var array
     */
    public static $monthMap = [
        'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4, 'may' => 5, 'june' => 6,
        'july' => 7, 'august' => 8, 'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
    ];
    /**
     * @var array
     */
    public static $intervalMap = [
        'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5, 'sixth' => 6, 'seventh' => 7,
        'eighth' => 8, 'ninth' => 9, 'tenth' => 10, 'other' => 2
    ];
    /**
     * @var array
     */
    public static $timeOfDayMap = [
        'noon' => 12, 'midnight' => 0
    ];
    /**
     * @var array
     */
    public static $weekdayWeekendMap = [
        'weekday' => array(1, 2, 3, 4, 5),
        'weekend' => array(0, 6)
    ];
}