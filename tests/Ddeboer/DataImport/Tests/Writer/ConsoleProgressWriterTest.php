<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\ConsoleProgressWriter;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Symfony\Component\Console\Output\NullOutput;

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

        $output = $this->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->getMock();

        $outputFormatter = $this->getMock('Symfony\Component\Console\Formatter\OutputFormatterInterface');
        $output->expects($this->once())
            ->method('isDecorated')
            ->will($this->returnValue(true));

        $output->expects($this->atLeastOnce())
            ->method('getFormatter')
            ->will($this->returnValue($outputFormatter));

        $output->expects($this->atLeastOnce())
            ->method('write');
        $writer = new ConsoleProgressWriter($output, $reader);

        $workflow = new Workflow($reader);
        $workflow->addWriter($writer)
            ->process();

        $this->assertEquals('debug', $writer->getVerbosity());
    }

    public function testFluentInterface()
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
        $output = new NullOutput();

        $writer = new ConsoleProgressWriter($output, $reader);

        $this->assertSame($writer, $writer->prepare());
        $this->assertSame($writer, $writer->writeItem(array('foo' => 'bar', 'bar' => 'foo')));
        $this->assertSame($writer, $writer->finish());
    }
}
