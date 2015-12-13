<?php


namespace CronLingo;

/**
 * Represents a field within a CRON expression
 *
 * Class Field
 * @package CronLingo
 */
class Field
{
    /**
     * @var int
     */
    protected $repeats;

    /**
     * @var array
     */
    protected $specific = [];

    /**
     * @var int
     */
    protected $rangeMin;

    /**
     * @var int
     */
    protected $rangeMax;

    /**
     * Build CRON expression part based on set values
     *
     * @return string
     */
    public function __toString()
    {
        $value = '';
        if ($this->repeats) {
            $value = '*/' . $this->repeats;
        }

        if (count($this->specific) > 0) {
            if (strlen($value) > 0) {
                $value .= ',';
            }
            $value .= implode(',', $this->specific);
        }

        if (!is_null($this->rangeMin) && !is_null($this->rangeMax)
            && $this->rangeMin >= 0 && $this->rangeMax >= 0) {
            $value = intval($this->rangeMin) . '-' . intval($this->rangeMax);
        }

        if (strlen($value) == 0) {
            $value = '*';
        }

        return $value;
    }

    /**
     * @param $val
     */
    public function fromCronValue($val)
    {
        $parts = explode(',',$val);

        foreach ($parts as $part) {
            if (false !== strstr($part, '/')) {
                preg_match('/\*\/(\d+)/', $part, $matches);

                if (isset($matches[1])) {
                    $this->repeatsOn(intval($matches[1]));
                }
            } else if (false !== strstr($part, '-')) {
                $ranges = explode('-', $part);
                $this->setRange(intval($ranges[0]), intval($ranges[1]));
            } else if (is_numeric($part)) {
                $this->addSpecific(intval($part));
            }
        }
    }

    /**
     * @return bool
     */
    public function isDirty()
    {
        return !is_null($this->repeats) || !is_null($this->rangeMin)
            || !is_null($this->rangeMax) || count($this->specific) > 0;
    }

    /**
     * @param $min
     * @param $max
     * @return $this
     */
    public function setRange($min, $max)
    {
        $this->rangeMin = $min;
        $this->rangeMax = $max;

        return $this;
    }

    /**
     * @param $rangeMin
     * @return $this
     */
    public function setRangeMin($rangeMin)
    {
        $this->rangeMin = $rangeMin;

        return $this;
    }

    /**
     * @param $rangeMax
     * @return $this
     */
    public function setRangeMax($rangeMax)
    {
        $this->rangeMax = $rangeMax;

        return $this;
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setSpecific(array $value)
    {
        $this->specific = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function addSpecific($value)
    {
        $this->specific[] = $value;
        $this->specific = array_unique($this->specific);

        return $this;
    }

    /**
     * @param $interval
     * @return $this
     */
    public function repeatsOn($interval)
    {
        $this->repeats = intval($interval);

        return $this;
    }

    /**
     * @return int
     */
    public function getRepeats()
    {
        return $this->repeats;
    }

    /**
     * @return array
     */
    public function getSpecific()
    {
        return $this->specific;
    }

    /**
     * @return int
     */
    public function getRangeMin()
    {
        return $this->rangeMin;
    }

    /**
     * @return int
     */
    public function getRangeMax()
    {
        return $this->rangeMax;
    }

}