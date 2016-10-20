<?php

namespace Ddeboer\DataImport\Tests\Step;

use Ddeboer\DataImport\Step\ValueConverterStep;

class ValueConverterStepTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->step = new ValueConverterStep();
    }

    public function testProcess()
    {
        $this->step->add('[foo]', function($v) { return 'barfoo'; });

        $data = ['foo' => 'foobar'];
        $this->step->process($data);

        $this->assertEquals(['foo' => 'barfoo'], $data);
    }
}
