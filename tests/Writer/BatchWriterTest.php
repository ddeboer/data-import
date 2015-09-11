<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\BatchWriter;

class BatchWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteItem()
    {
        $delegate = $this->getMock('Ddeboer\DataImport\Writer');
        $writer = new BatchWriter($delegate);

        $delegate->expects($this->once())
            ->method('prepare');

        $delegate->expects($this->never())
            ->method('writeItem');

        $writer->prepare();
        $writer->writeItem(['Test']);
    }

    public function testFlush()
    {
        $delegate = $this->getMock('Ddeboer\DataImport\Writer');
        $writer = new BatchWriter($delegate);

        $delegate->expects($this->exactly(40))
            ->method('writeItem');

        $delegate->expects($this->once())
            ->method('prepare');

        $writer->prepare();

        for ($i = 0; $i < 40; $i++) {
            $writer->writeItem(['Test']);
        }
    }

    public function testFlushWithFlushableDelegate()
    {
        $prophet = new \Prophecy\Prophet;
        $prophecy = $prophet->prophesize();

        $prophecy->willExtend('stdClass');
        $prophecy->willImplement('Ddeboer\DataImport\Writer');
        $prophecy->willImplement('Ddeboer\DataImport\Writer\FlushableWriter');

        $prophecy->writeItem(\Prophecy\Argument::any())->shouldBeCalledTimes(40);

        $prophecy->prepare()->shouldBeCalledTimes(2);
        $prophecy->flush()->shouldBeCalled();

        $writer = new BatchWriter($prophecy->reveal());

        $writer->prepare();

        for ($i = 0; $i < 40; $i++) {
            $writer->writeItem(['Test']);
        }
    }
}
