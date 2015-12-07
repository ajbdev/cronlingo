<?php

namespace CronLingo;




class Parser
{


//"Every day at midnight".crons == "0 0 * * *"
//"Every 15 minutes at midnight on the weekend".crons == "*/15 0 * * 0,6"
//"Every other minute in July at noon on the weekday".crons == "*/2 12 * 7 1-5"
//"Every 1st day in April at midnight".crons == "0 0 1 4 *"
//"Every day on the weekday at 3:30".crons == "30 3 * * 1-5"


    protected $tokenMap = [
        'every|daily|weekly|monthly' => 'T_EVERY',
        '\d+[st|th|rd|nd]?[^:]?|other|third|fourth|fifth|sixth|seventh|eighth|ninth' => 'T_INTERVAL',
        'second|minute|hour|day|month|year?' => 'T_FIELD',
        'sunday|monday|tuesday|wednesday|thursday|friday|saturday' => 'T_DAYOFWEEK',
        '(?:[01]?[0-9]|2[0-3]):[0-5][0-9](?:[0-5][0-9])?|(?:[01]?[0-9]am|pm)'  =>  'T_EXACTTIME',
        '[01]?[0-9]am|pm'  =>  'T_EXACTTIME',
        'noon|midnight' =>  'T_TIMEOFDAY',
        'on|at' =>  'T_ONAT',
        'in' =>  'T_IN',
        'to' =>  'T_TO',
        'january|february|march|april|may|june|july|august|september|october|november|december' =>  'T_MONTH',
        'weekend|weekday?' =>  'T_WEEKDAY'
    ];

    protected $fieldMap = [
        'day'       =>  'dayOfMonth'
    ];
    protected $monthMap = [
        'january'   =>  1, 'february' => 2, 'march' => 3, 'april' => 4, 'may' => 5, 'june' => 6,
        'july' => 7, 'august' => 8, 'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
    ];


    protected $position;

    protected $cron;

    protected $tokens = [];

    public function parse($value)
    {
        $this->tokens = $this->lex($value);

        $this->cron = new Cron();

        var_dump($this->tokens);
        $this->position = 0;
        $this->scan();

        echo $this->cron;
    }

    protected function evaluate()
    {

    }

    protected function scan()
    {
        if ($this->position > count($this->tokens)) {
            return; // Finished parsing
        }

        $token = $this->current()['token'];
        $value = $this->current()['value'];

        switch ($token) {
            case 'T_EVERY':
                $this->expects($this->next(),'T_INTERVAL');
                break;
            case 'T_INTERVAL':
                $this->expects($this->next(),array('T_FIELD','T_TO'));
                break;
            case 'T_TO':
                $this->expects($this->next(),'T_INTERVAL');
                $this->expects($this->previous(),'T_INTERVAL');
                break;
            case 'T_MONTH':

                break;
            case 'T_FIELD':
                $this->expects($this->previous(),array('T_INTERVAL'));

                if (isset($this->fieldMap[$value])) {
                    $value = $this->fieldMap[$value];
                }

                $field = $this->cron->{$value};

                if ($this->is($this->previous(2),'T_TO')) {
                    $this->expects($this->previous(3),array('T_INTERVAL'));
                    // Range
                    $field->setRange($this->previous(3)['value'], $this->previous()['value']);
                } else {
                    // Repetition
                }

                break;
            default:
                break;
        }

        $this->position++;

        echo $this->position;
        $this->scan();
    }

    protected function is($token, $types)
    {
        if (!is_array($types)) $types = array($types);

        return in_array($token['token'],$types);
    }

    protected function expects($token, $types)
    {
        if (!is_array($types)) $types = array($types);

        if (!$this->is($token, $types)) {
            throw new \UnexpectedValueException('Expected ' . implode(',',$types) . ' but got ' . $token['token']);
        }
    }

    protected function current()
    {
        return $this->tokens[$this->position];
    }

    protected function next($skip = 1)
    {
        return $this->tokens[$this->position+$skip];
    }

    protected function previous($skip = 1)
    {
        return $this->tokens[$this->position-$skip];
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

        echo $regex;

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