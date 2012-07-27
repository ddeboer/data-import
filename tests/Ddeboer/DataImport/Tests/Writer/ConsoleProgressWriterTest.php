<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\ConsoleProgressWriter;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;

class ConsoleProgressWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWrite()
    {
        $data = array(
            array(
                'first'  => 'The first',
                'second' => 'Second property'
            ), array(
                'first'  => 'Another first',
                'second' => 'Last second'
            )
        );
        $reader = new ArrayReader($data);

        $output = $this->getMockBuilder('\Symfony\Component\Console\Output\ConsoleOutput')
            ->getMock();
        $output->expects($this->atLeastOnce())
            ->method('write');
        $writer = new ConsoleProgressWriter($output, $reader);

        $workflow = new Workflow($reader);
        $workflow->addWriter($writer)
            ->process();
    }
}