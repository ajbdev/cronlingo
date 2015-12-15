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
        $timeParts = [
            $this->field('hour', $this->cron->hour),
            $this->field('minute', $this->cron->minute)
        ];

        return '';
    }

    public function date(Field $dayOfWeek, Field $dayOfMonth, Field $month)
    {
        $parts = array(
            'dayOfMonth' => $this->field($dayOfMonth),
            'dayOfWeek' => $this->field($dayOfWeek, FieldMap::$dayOfWeekMap),
            'month' => $this->field($month, FieldMap::$monthMap),
        );

        $fragment = '';

        foreach ($parts as $part => $piece) {
            
        }


    }

    public function time(Field $hour, Field $minute) {

    }

    public function field(Field $field, $map = array())
    {
        $parts = array();
        if (count($field->getSpecific()) > 0) {
            $parts['specific'] = array();
            $map = array_flip($map);
            foreach ($field->getSpecific() as $spec) {
                $parts['specific'] = isset($map[$spec]) ? $map[$spec] : $spec;
            }
        }
        if ($field->getRangeMin() && $field->getRangeMax()) {
            $parts['range'] = $field->getRangeMin() . ' to ' . $field->getRangeMax();
        }
        if ($field->getRepeats()) {
            $parts['repeats'] = $field->getRepeats();
        }

        return $parts;
    }

    public function __toString()
    {
        return $this->express();
    }

}