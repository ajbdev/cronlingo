<?php

require_once 'vendor/autoload.php';

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $field = new \CronLingo\Field();

        $this->assertEquals('*', (string) $field);
    }

    public function testRepeat()
    {
        $field = new \CronLingo\Field();

        $field->repeatsOn(2);

        $this->assertEquals('*/2', (string) $field);
    }

    public function testSpecific()
    {
        $field = new \CronLingo\Field();

        $field->addSpecific(5)
              ->addSpecific(6);

        $this->assertEquals('5,6', (string) $field);

        $field->setSpecific([1,2,3,4]);

        $this->assertEquals('1,2,3,4', (string) $field);
    }

    public function testRange()
    {
        $field = new \CronLingo\Field();
        $field->setRange(0,15);

        $this->assertEquals('0-15', (string) $field );

        $field->setRangeMin(3);
        $field->setRangeMax(20);

        $this->assertEquals('3-20', (string) $field);
    }
}