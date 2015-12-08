<?php

namespace CronLingo;




class Parser
{




    protected $tokenMap = [
        'every|daily|weekly|monthly' => 'T_EVERY',
        '\d{1,2}:\d{2}(?:am|pm)?'   =>  'T_EXACTTIME',
        '\d{1,2}(?:am|pm)'          =>  'T_EXACTTIME',
        '(?:am|pm)'             =>  'T_MERIDIEM',
        '\d+[st|th|rd|nd]?[^:]?|other|second|third|fourth|fifth|sixth|seventh|eighth|ninth|tenth' => 'T_INTERVAL',
        'second|minute|hour|day|month|year?' => 'T_FIELD',
        'sunday|monday|tuesday|wednesday|thursday|friday|saturday' => 'T_DAYOFWEEK',
        'noon|midnight' =>  'T_TIMEOFDAY',
        'on|at' =>  'T_ONAT',
        'in' =>  'T_IN',
        'to' =>  'T_TO',
        'january|february|march|april|may|june|july|august|september|october|november|december' =>  'T_MONTH',
        'weekend|weekday?' =>  'T_WEEKDAYWEEKEND'
    ];

    protected $fieldMap = [
        'day'       =>  'dayOfMonth'
    ];
    protected $dayOfWeekMap = [
        'sunday'    =>  0,'monday' => 1,'tuesday' => 2,'wednesday' => 3,
        'thursday' => 4, 'friday' => 5, 'saturday' => 6
    ];
    protected $monthMap = [
        'january'   =>  1, 'february' => 2, 'march' => 3, 'april' => 4, 'may' => 5, 'june' => 6,
        'july' => 7, 'august' => 8, 'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
    ];
    protected $intervalMap = [
        'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5, 'sixth' => 6, 'seventh' => 7,
        'eighth' => 8, 'ninth' => 9, 'tenth' => 10
    ];
    protected $timeOfDayMap = [
        'noon'  =>  12, 'midnight'  =>  0
    ];

    protected $weekdayWeekendMap = [
        'weekday'   =>  array(1,2,3,4,5),
        'weekend'   =>  array(0,6)
    ];

    protected $position;

    /**
     * @var Cron
     */
    protected $cron;

    /**
     * Array of lexed tokens
     *
     * @var array
     */
    protected $tokens = [];

    public function parse($value)
    {
        $this->tokens = $this->lex($value);

        $this->cron = new Cron();

        $this->position = 0;
        $this->evaluate();

        return (string) $this->cron;
    }

    protected function evaluate()
    {
        if ($this->position >= count($this->tokens)) {
            return; // Finished parsing
        }

        $token = $this->current()['token'];
        $value = $this->current()['value'];

        switch ($token) {
            case 'T_EVERY':
                $this->expects($this->next(),array('T_INTERVAL', 'T_FIELD', 'T_DAYOFWEEK','T_ONAT'));
                break;
            case 'T_INTERVAL':
                $this->expects($this->next(),array('T_FIELD','T_TO'));
                break;
            case 'T_EXACTTIME':
                $meridiem = '';

                if ($this->is($this->next(), array('T_MERIDIEM'))) {
                    $meridiem = $this->next()['value'];
                }


                @list($hours, $minutes) = explode(':', $value);
                if (!$minutes) $minutes = '0';

                if ($meridiem == 'pm' || strpos($value, 'pm') !== false) {
                    $hours += 12;
                }

                $this->cron->hour->addSpecific(intval($hours));
                $this->cron->minute->addSpecific(intval($minutes));
                break;
            case 'T_WEEKDAYWEEKEND':
                $this->expects($this->previous(),array('T_ONAT'));
                $this->cron->dayOfWeek->setSpecific($this->weekdayWeekendMap[$value]);
                break;
            case 'T_DAYOFWEEK':
                $this->expects($this->previous(),array('T_ONAT','T_INTERVAL','T_EVERY','T_DAYOFWEEK'));
                $this->cron->dayOfWeek->addSpecific($this->dayOfWeekMap[$value]);
                break;
            case 'T_TO':
                $this->expects($this->next(),'T_INTERVAL');
                $this->expects($this->previous(),'T_INTERVAL');
                break;
            case 'T_TIMEOFDAY':
                $this->expects($this->previous(),array('T_ONAT'));

                $this->cron->hour->addSpecific($this->timeOfDayMap[$value]);
                break;
            case 'T_MONTH':
                $this->expects($this->previous(),array('T_ONAT','T_IN'));

                $this->cron->month->addSpecific($this->monthMap[$value]);
                break;
            case 'T_FIELD':
                $this->expects($this->previous(),array('T_INTERVAL','T_EVERY'));

                if (isset($this->fieldMap[$value])) {
                    $value = $this->fieldMap[$value];
                }

                $field = $this->cron->{$value};

                if ($this->is($this->previous(2),'T_TO')) {
                    $this->expects($this->previous(3),array('T_INTERVAL'));
                    // Range
                    $field->setRange($this->previous(3)['value'], $this->previous()['value']);
                } else if ($this->is($this->previous(), 'T_INTERVAL')) {
                    $previous = $this->previous()['value'];
                    if ($previous === 'other') {
                        $field->repeatsOn(2);
                    } else if (isset($this->intervalMap[$previous])) {
                        $field->addSpecific($this->intervalMap[$previous]);
                    } else {
                        $field->addSpecific((int) $previous);
                    }
                }

                break;
            default:
                break;
        }

        $this->position++;

        $this->evaluate();
    }

    protected function is($token, $types)
    {
        if (!is_array($types)) $types = array($types);

        if (false !== $token) {
            return in_array($token['token'],$types);
        }

        return false;
    }

    protected function expects($token, $types)
    {
        if (!is_array($types)) $types = array($types);

        if (!$this->is($token, $types)) {
            $t = isset($token['token']) ? $token['token'] : 'NULL';
            throw new ParseException('Expected ' . implode(',',$types) . ' but got ' . $t);
        }
    }

    protected function current()
    {
        return $this->tokens[$this->position];
    }

    protected function next($skip = 1)
    {
        if (isset($this->tokens[$this->position+$skip])) {
            return $this->tokens[$this->position+$skip];
        }

        return false;
    }

    protected function previous($skip = 1)
    {
        if (isset($this->tokens[$this->position-$skip])) {
            return $this->tokens[$this->position-$skip];
        }

        return false;
    }


    protected function compileRegex()
    {
        $regex = '~(' . implode(')|(', array_keys($this->tokenMap)) . ')~iA';
        return $regex;
    }

    protected function lex($string) {
        $delimiter = ' ';
        $fragment = strtok($string, $delimiter);
        $regex = $this->compileRegex();

        $tokens = array();

        while (false !== $fragment) {
            if (preg_match($regex, $fragment, $matches)) {
                foreach ($matches as $offset => $val) {
                    if (!empty($val) && $offset > 0) {
                        $token = array_values($this->tokenMap)[$offset-1];

                        $tokens[] = array(
                            'token'     =>  $token,
                            'value'     =>  strtolower($matches[0])
                        );
                    }
                }

            }
            $fragment = strtok($delimiter);
        }

        return $tokens;
    }
}