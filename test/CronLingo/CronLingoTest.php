<?php

require_once 'vendor/autoload.php';

class CronLingoTest extends \PHPUnit_Framework_TestCase
{
    public function testCron()
    {
        $this->assertInternalType('string', \CronLingo\CronLingo::fromExpression('Every day at midnight'));
    }

    public function testGetParser()
    {
        $this->assertInstanceOf('CronLingo\Parser',\CronLingo\CronLingo::getParser());
    }
}