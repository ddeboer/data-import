<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\CsvWriter;

class CsvWriterTest extends StreamWriterTest
{
    public function testWriteItem()
    {
        $writer = new CsvWriter(';', '"', $this->getStream());

        $writer->writeItem(array('first', 'last'));

        $writer
            ->writeItem(array(
                'first' => 'James',
                'last'  => 'Bond'
            ))
            ->writeItem(array(
                'first' => '',
                'last'  => 'Dr. No'
            ));
        $this->assertContentsEquals(
            "first;last\nJames;Bond\n;\"Dr. No\"\n",
            $writer
        );

        $writer->finish();
    }

    public function testFluentInterface()
    {
        $writer = new CsvWriter(';', '"', $this->getStream());

        $this->assertSame($writer, $writer->prepare());
        $this->assertSame($writer, $writer->writeItem(array('foo' => 'bar', 'bar' => 'foo')));
        $this->assertSame($writer, $writer->finish());
    }
}
