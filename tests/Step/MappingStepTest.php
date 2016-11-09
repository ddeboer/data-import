<?php

namespace Ddeboer\DataImport\Tests\Step;

use Ddeboer\DataImport\Step\MappingStep;

class MappingStepTest extends \PHPUnit_Framework_TestCase
{
    protected $mapper;

    protected function setUp()
    {
        $this->step = new MappingStep();
    }

    public function testProcess()
    {
        $this->step->map('[foo]', '[bar]');

        $data = [
            'foo' => '1',
        ];

        $this->step->process($data);

        $this->assertEquals([
            'bar' => '1',
        ], $data);
    }
}
