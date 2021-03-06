<?php

require_once 'vendor/autoload.php';

class ParserTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;

    protected function getParser() {
        if ($this->parser === null) {
            $this->parser = new \CronLingo\Parser();
        }
        $this->parser->reset();
        return $this->parser;
    }

    public function testParseException()
    {
        $this->setExpectedException('CronLingo\ParseException');

        $parser = $this->getParser();
        $token = array('token' => 'T_EVERY');

        $parser->expects($token, 'T_ONAT');
    }


    public function testEvery()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '0 0 * * *',
            $parser->parse('Every day at midnight')
        );
    }

    public function testExactTime()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '20 3 * * *',
            $parser->parse('Every day at 3:20')
        );

        $this->assertEquals(
            '0 13 * * *',
            $parser->parse('Every day at 1p')
        );

        $this->assertEquals(
            '0 14,15,5 * * *',
            $parser->parse('Every day at 2p, 3pm, and 5am')
        );
    }

    public function testMeridiem()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '0 15 * * *',
            $parser->parse('Every day at 3:00 PM')
        );


        $this->assertEquals(
            '0 10 * * *',
            $parser->parse('Every day at 10:00 AM')
        );
    }

    public function testInterval()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '*/2 * * * *',
            $parser->parse('Every other minute')
        );

        $this->assertEquals(
            '0 */3 * * *',
            $parser->parse('Every 3 hours')
        );
    }

    public function testField()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '0 0 * */3 *',
            $parser->parse('Every 3rd month')
        );

        $this->assertEquals(
            '0 3-6 * * *',
            $parser->parse('Every 3 to 6 hours')
        );
    }

    public function testWeekday()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '0 0 * * 2',
            $parser->parse('Every tuesday')
        );
    }

    public function testTimeOfDay()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '0 12 * * *',
            $parser->parse('Every day at noon')
        );

        $this->assertEquals(
            '0 0 * * *',
            $parser->parse('Every day at midnight')
        );
    }

    public function testOnAt()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '0 0 * * 0,6',
            $parser->parse('Every day on the weekend')
        );

        $this->assertEquals(
            '0 0 * * 1,2,3,4,5',
            $parser->parse('Every day on a weekday')
        );
    }

    public function testIn()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '0 0 * 2 *',
            $parser->parse('Every day in February')
        );
    }

    public function testTo()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '5-12 * * * *',
            $parser->parse('Every 5 to 12 minutes')
        );
    }

    public function testMonth()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '0 * * 1 *',
            $parser->parse('Every hour in January')
        );
    }

    public function testWeekdayWeekend()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '0 0 * * 0,6',
            $parser->parse('Every day on the weekend')
        );

        $this->assertEquals(
            '0 0 * * 1,2,3,4,5',
            $parser->parse('Every weekday')
        );
    }

    public function testComprehensiveStrings()
    {
        $parser = $this->getParser();

        $this->assertEquals(
            '*/15 0 * * 0,6',
            $parser->parse('Every 15 minutes at midnight on the weekend')
        );

        $this->assertEquals(
            '*/2 12 * 8 1,2,3,4,5',
            $parser->parse('Every other minute in August at noon on the weekday')
        );

        $this->assertEquals(
            '0 0 1 4 *',
            $parser->parse('The 1st day in April at midnight')
        );

        $this->assertEquals(
            '25 14 * * 1,2,3,4,5',
            $parser->parse('Every day on the weekday at 2:25pm')
        );
    }


}