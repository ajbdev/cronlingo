<?php

require_once 'vendor/autoload.php';

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $field = new \CronLingo\Field();

        $this->assertEquals( (string) $field, '*');
    }

    public function testRange()
    {
        $field = new \CronLingo\Field();
        $field->setRange(0,15);

        $this->assertEquals( (string) $field, '0-15');
    }
}