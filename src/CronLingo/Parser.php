<?php

namespace CronLingo;

/**
 * Tokenizes a string and parses into a CRON expression
 *
 * Class Parser
 * @package CronLingo
 */
class Parser
{
    /**
     * Regular expressions used to tokenize a string
     *
     * @var array
     */
    protected $tokenMap = [
        'every|daily|weekly|monthly' => 'T_EVERY',
        '\d{1,2}:\d{2}(?:am|pm)?' => 'T_EXACTTIME',
        '\d{1,2}(?:am|pm|a|p)' => 'T_EXACTTIME',
        '(?:am|pm)' => 'T_MERIDIEM',
        '\d+[st|th|rd|nd]?[^:]?|other|second|third|fourth|fifth|sixth|seventh|eighth|ninth|tenth' => 'T_INTERVAL',
        'second|minute|hour|day|month?' => 'T_FIELD',
        'sunday|monday|tuesday|wednesday|thursday|friday|saturday' => 'T_DAYOFWEEK',
        'noon|midnight' => 'T_TIMEOFDAY',
        'on|at' => 'T_ONAT',
        'in' => 'T_IN',
        'to' => 'T_TO',
        'january|february|march|april|may|june|july|august|september|october|november|december' => 'T_MONTH',
        'weekend|weekday?' => 'T_WEEKDAYWEEKEND'
    ];

    /**
     * @var array
     */
    protected $fieldMap = [
        'day' => 'dayOfMonth'
    ];
    /**
     * @var array
     */
    protected $dayOfWeekMap = [
        'sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
        'thursday' => 4, 'friday' => 5, 'saturday' => 6
    ];
    /**
     * @var array
     */
    protected $monthMap = [
        'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4, 'may' => 5, 'june' => 6,
        'july' => 7, 'august' => 8, 'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
    ];
    /**
     * @var array
     */
    protected $intervalMap = [
        'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5, 'sixth' => 6, 'seventh' => 7,
        'eighth' => 8, 'ninth' => 9, 'tenth' => 10, 'other' => 2
    ];
    /**
     * @var array
     */
    protected $timeOfDayMap = [
        'noon' => 12, 'midnight' => 0
    ];
    /**
     * @var array
     */
    protected $weekdayWeekendMap = [
        'weekday' => array(1, 2, 3, 4, 5),
        'weekend' => array(0, 6)
    ];

    /**
     * @var int
     */
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

    /**
     * Parse a string into a CRON expression
     *
     * @param $value
     * @return string
     */
    public function parse($value)
    {
        $this->tokens = $this->lex($value);

        $this->reset();
        $this->evaluate();

        return (string)$this->cron;
    }

    /**
     * Reset parser token position and CRON expression
     */
    public function reset()
    {
        $this->cron = new Cron();

        $this->position = 0;
    }

    /**
     * For simple expressions, zero out the time so the cron
     * matches user expectation and does not execute constantly.
     *
     * E.g., someone would not expect "Every day on Tuesday"
     * to run for every minute and hour on Tuesday.
     *
     * @param $field
     */
    protected function nilTime($field)
    {
        $order = array_search($field, $this->cron->ordered());

        if ($order > 1 && !$this->cron->hour->isDirty()) {
            $this->cron->hour->addSpecific(0);
        }
        if ($order > 0 && !$this->cron->minute->isDirty()) {
            $this->cron->minute->addSpecific(0);
        }
    }

    /**
     * Evaluate tokens and build CRON expression
     */
    protected function evaluate()
    {
        if ($this->position >= count($this->tokens)) {
            return; // Finished parsing
        }

        $token = $this->current()['token'];
        $value = $this->current()['value'];

        switch ($token) {
            case 'T_EVERY':
                $this->expects($this->next(), array('T_INTERVAL', 'T_FIELD', 'T_DAYOFWEEK', 'T_ONAT', 'T_WEEKDAYWEEKEND'));
                break;
            case 'T_INTERVAL':
                $this->expects($this->next(), array('T_FIELD', 'T_TO'));
                break;
            case 'T_EXACTTIME':
                $meridiem = '';

                if ($this->is($this->next(), array('T_MERIDIEM'))) {
                    $meridiem = $this->next()['value'];
                }

                $hours = $minutes = 0;

                $parts = explode(':', $value);
                if (isset($parts[0])) {
                    $hours = $parts[0];
                }

                if (isset($parts[1])) {
                    $minutes = $parts[1];
                }

                if ($meridiem == 'pm' || strpos($value, 'pm') || strpos($value, 'p') !== false) {
                    $hours += 12;
                }

                if ($this->is($this->previous(), 'T_ONAT')) {
                    $this->cron->hour->setSpecific([intval($hours)]);
                    $this->cron->minute->setSpecific([intval($minutes)]);
                } else {
                    $this->cron->hour->addSpecific(intval($hours));
                    $this->cron->minute->addSpecific(intval($minutes));
                }

                break;
            case 'T_WEEKDAYWEEKEND':
                $this->expects($this->previous(), array('T_ONAT', 'T_EVERY'));
                $this->cron->dayOfWeek->setSpecific($this->weekdayWeekendMap[$value]);
                $this->nilTime($this->cron->dayOfWeek);
                break;
            case 'T_DAYOFWEEK':
                $this->expects($this->previous(), array('T_ONAT', 'T_INTERVAL', 'T_EVERY', 'T_DAYOFWEEK'));
                $this->cron->dayOfWeek->addSpecific($this->dayOfWeekMap[$value]);

                $this->nilTime($this->cron->dayOfWeek);
                break;
            case 'T_TO':
                $this->expects($this->next(), 'T_INTERVAL');
                $this->expects($this->previous(), 'T_INTERVAL');
                break;
            case 'T_TIMEOFDAY':
                $this->expects($this->previous(), array('T_ONAT'));

                $this->cron->hour->setSpecific([$this->timeOfDayMap[$value]]);
                $this->nilTime($this->cron->hour);
                break;
            case 'T_MONTH':
                $this->expects($this->previous(), array('T_ONAT', 'T_IN'));

                $this->cron->month->addSpecific($this->monthMap[$value]);

                $this->nilTime($this->cron->month);
                break;
            case 'T_FIELD':
                $this->expects($this->previous(), array('T_INTERVAL', 'T_EVERY'));

                if (isset($this->fieldMap[$value])) {
                    if ($this->is($this->previous(), 'T_INTERVAL')) {
                        $value = $this->fieldMap[$value];
                    } else {
                        break;
                    }
                }

                $field = $this->cron->{$value};

                if ($this->is($this->previous(2), 'T_TO')) {
                    $this->expects($this->previous(3), array('T_INTERVAL'));
                    // Range
                    $field->setRange($this->previous(3)['value'], $this->previous()['value']);
                } else if ($this->is($this->previous(), array('T_INTERVAL', 'T_EVERY'))) {
                    $previous = $this->previous()['value'];

                    if ($this->is($this->previous(), 'T_EVERY')) {
                        $method = 'addSpecific';
                        $amt = '*';
                    } else {
                        $method = $this->is($this->previous(2), 'T_EVERY') ? 'repeatsOn' : 'addSpecific';

                        $amt = isset($this->intervalMap[$previous]) ? $this->intervalMap[$previous] : intval($previous);
                    }

                    $field->{$method}($amt);
                }

                $this->nilTime($field);

                break;
            default:
                break;
        }

        $this->position++;

        $this->evaluate();
    }

    /**
     * Check if a token is of a type
     *
     * @param $token
     * @param $types
     * @return bool
     */
    protected function is($token, $types)
    {
        if (!is_array($types)) {
            $types = array($types);
        }

        if (false !== $token) {
            return in_array($token['token'], $types);
        }

        return false;
    }

    /**
     * Enforce expectations of a certain token
     *
     * @param $token
     * @param $types
     */
    public function expects($token, $types)
    {
        if (!is_array($types)) {
            $types = array($types);
        }

        if (!$this->is($token, $types)) {
            $t = isset($token['token']) ? $token['token'] : 'NULL';
            throw new ParseException('Expected ' . implode(',', $types) . ' but got ' . $t);
        }
    }

    /**
     * Retrieve current token based on position
     *
     * @return array
     */
    protected function current()
    {
        return $this->tokens[$this->position];
    }

    /**
     * Look ahead in the token array
     *
     * @param int $skip
     * @return bool
     */
    protected function next($skip = 1)
    {
        return $this->seek($this->position + $skip);
    }

    /**
     * Look behind in the token array
     *
     * @param int $skip
     * @return bool
     */
    protected function previous($skip = 1)
    {
        return $this->seek($this->position - $skip);
    }

    /**
     * Seek a specific token
     *
     * @param $index
     * @return bool
     */
    protected function seek($index)
    {
        if (isset($this->tokens[$index])) {
            return $this->tokens[$index];
        }

        return false;
    }

    /**
     * Concatenate regex expressions into a single regex for performance
     *
     * @return string
     */
    protected function compileRegex()
    {
        $regex = '~(' . implode(')|(', array_keys($this->tokenMap)) . ')~iA';
        return $regex;
    }

    /**
     * Lex a string into tokens
     *
     * @param $string
     * @return array
     */
    protected function lex($string)
    {
        $delimiter = ' ';
        $fragment = strtok($string, $delimiter);
        $regex = $this->compileRegex();

        $tokens = array();

        while (false !== $fragment) {
            if (preg_match($regex, $fragment, $matches)) {
                foreach ($matches as $offset => $val) {
                    if (!empty($val) && $offset > 0) {
                        $token = array_values($this->tokenMap)[$offset - 1];

                        $tokens[] = array(
                            'token' => $token,
                            'value' => strtolower($matches[0])
                        );
                    }
                }

            }
            $fragment = strtok($delimiter);
        }

        return $tokens;
    }
}