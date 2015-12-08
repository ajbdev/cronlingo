<?php


namespace CronLingo;


class Field
{
    public $value;

    protected $repeats;

    protected $specific = [];

    protected $rangeMin;

    protected $rangeMax;

    public function __construct($defaultValue = '*')
    {
        $this->value = $defaultValue;
    }

    public function __toString()
    {
        $value = '';
        if ($this->repeats) {
            $value = '*/' . $this->repeats;
        }

        if (count($this->specific) > 0) {
            if (strlen($value) > 0) {
                $value = ',';
            }
            $value .= implode(',', $this->specific);
        }

        if ($this->rangeMin && $this->rangeMax) {
            $value = $this->rangeMin .'-'.$this->rangeMax;
        }

        if (strlen($value) == 0) {
            $value = '*';
        }

        return $value;
    }

    public function setRange($min, $max)
    {
        $this->rangeMin = $min;
        $this->rangeMax = $max;
    }

    public function setRangeMin($rangeMin)
    {
        $this->rangeMin = $rangeMin;
    }

    public function setRangeMax($rangeMax)
    {
        $this->rangeMax = $rangeMax;
    }

    public function setSpecific(array $value)
    {
        $this->specific = $value;
    }

    public function addSpecific($value)
    {
        $this->specific[] = $value;
        $this->specific = array_unique($this->specific);
    }

    public function repeatsOn($interval)
    {
        $this->repeats = intval($interval);
    }
}