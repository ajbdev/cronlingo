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
        $parser = new Parser();

        return $parser->parse($string);
    }
}