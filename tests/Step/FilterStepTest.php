<?php

namespace Ddeboer\DataImport\Tests\Step;

use Ddeboer\DataImport\Step\FilterStep;

class FilterStepTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->step = new FilterStep();
    }

    public function testProcess()
    {
        $this->step->add(function ($v) { return in_array('bar', $v); });

        $d = ['foo'];
        $this->assertFalse($this->step->process($d));

        $d = ['bar'];
        $this->assertTrue($this->step->process($d));
    }

    public function testClone()
    {
        $reflection = new \ReflectionObject($this->step);
        $property = $reflection->getProperty('steps');
        $property->setAccessible(true);

        $this->step->add(function ($v) { return in_array('bar', $v); });
        $d = ['foo'];

        $this->step->process($d);

        $this->assertCount(1, $property->getValue($this->step));
    }
}
