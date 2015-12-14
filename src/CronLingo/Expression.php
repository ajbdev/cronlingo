<?php


namespace CronLingo;

/**
 * Invert a crontab to get a human readable representation
 *
 * Class Expression
 * @package CronLingo
 */
class Expression
{
    protected $cron;

    protected $periodMap = [
        'minute'        =>  array('minute', 'minutes'),
        'month'         =>  array('month', 'months'),
        'hour'          =>  array('hour', 'hours'),
        'dayOfWeek'     =>  array('day of the week','day of the week'),
        'dayOfMonth'    =>  array('day of the month','day of the month'),
    ];

    public function __construct(Cron $cron) {
        $this->cron = $cron;
    }

    public function express()
    {
        $date = $this->date(
            $this->cron->month,
            $this->cron->dayOfWeek,
            $this->cron->dayOfMonth
        );

        $time = $this->time(
            $this->cron->hour,
            $this->cron->minute
        );

        return $date . ' ' . $time;
    }

    public function date(Field $month, Field $dayOfWeek, Field $dayOfMonth)
    {
        if (!$month->isDirty() && !$dayOfWeek->isDirty() && !$dayOfMonth->isDirty()) {
            return 'Every day';
        }
    }

    public function time(Field $hour, Field $minute)
    {
        $fragment = '';

        if (count($hour->getSpecific()) == 1 && count($minute->getSpecific()) == 1) {
            $hr = $hour->getSpecific()[0];
            $min = $minute->getSpecific()[0];
            $min = str_pad($min, 2, 0, STR_PAD_LEFT);

            $meridiem = 'AM';
            if ($hr > 12) {
                $hr-=12;
                $meridiem = 'PM';
            }

            $fragment = 'at ' . $hr . ':' . $min . ' ' . $meridiem;
        }

        return $fragment;
    }

    public function __toString()
    {
        return $this->express();
    }

}