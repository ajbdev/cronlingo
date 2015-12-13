<?php


require_once 'vendor/autoload.php';

use CronLingo\Cron;

class CronTest extends \PHPUnit_Framework_TestCase
{
    public function testFromString()
    {
        $cron = new Cron();

        $cron->fromString('25 2-6 */2 * 1,2,3,4,5');

        $this->assertEquals(
            array(25),
            $cron->minute->getSpecific()
        );

        $this->assertEquals(
            2,
            $cron->hour->getRangeMin()
        );

        $this->assertEquals(
            6,
            $cron->hour->getRangeMax()
        );

        $this->assertEquals(
            2,
            $cron->dayOfMonth->getRepeats()
        );

        $this->assertEquals(
            array(1,2,3,4,5),
            $cron->dayOfWeek->getSpecific()
        );

        // From constructor
        $cron = new Cron('0 30  * * *');

        $this->assertEquals(
            '0 30 * * *',
            (string) $cron
        );
    }

    public function testSetWhitespace()
    {
        $cron = new Cron();

        $cron->setWhitespace('    ');

        $this->assertEquals(
            '*    *    *    *    *',
            (string) $cron
        );
    }
}