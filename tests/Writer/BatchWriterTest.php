<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\BatchWriter;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class BatchWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWrite()
    {
        $mock = $this->getMock('Ddeboer\DataImport\Writer\FlushableWriter');
        $writer = new BatchWriter($mock);

        $mock->expects($this->never())
            ->method('writeItem');

        $writer->prepare();
        $writer->writeItem(['Test']);
    }

    public function testWriteAndFlush()
    {
        $data = [];
        $mock = new ArrayWriter($data);
        $writer = new BatchWriter($mock);
        $item = ['foo', 'bar'];

        $writer->prepare();

        for ($i = 0; $i < 10; $i++) {
            $writer->writeItem($item);
        }

        $this->assertCount(0, $data);

        for ($i = 0; $i < 10; $i++) {
            $writer->writeItem($item);
        }

        $this->assertCount(20, $data);

        for ($i = 0; $i < 10; $i++) {
            $writer->writeItem($item);
        }

        $writer->finish();

        $this->assertCount(30, $data);
    }
} 