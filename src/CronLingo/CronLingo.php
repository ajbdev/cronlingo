<?php


namespace CronLingo;

/**
 * Factory interface for getting CRON expressions
 *
 * Class CronLingo
 * @package CronLingo
 */
class CronLingo
{
    /**
     * @param $string
     * @return string
     */
    public static function cron($string)
    {
        return self::getParser()->parse($string);
    }

    /**
     * @return Parser
     */
    public static function getParser()
    {
        return new Parser();
    }
}