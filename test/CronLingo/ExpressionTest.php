<?php



require_once 'vendor/autoload.php';

use CronLingo\Cron;
use CronLingo\Expression;


class ExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testExpress()
    {
        $cron = new Cron('30 12 * * *');
        $expression = new Expression($cron);

        $this->assertEquals(
            'Every day at 12:30 AM',
            (string) $expression
        );
    }
}