<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\CsvWriter;

class CsvWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteItem()
    {
        $outputFile = new \SplFileObject(tempnam('/tmp', null));
        $writer = new CsvWriter($outputFile);

        $writer->writeItem(array('first', 'last'));

        $writer->writeItem(
            array(
                'first' => 'James',
                'last'  => 'Bond'
            )
        )->writeItem(
            array(
                'first' => '',
                'last'  => 'Dr. No'
            )
        );

        $fileContents = file_get_contents($outputFile->getPathname());
        $this->assertEquals(
            "first;last\nJames;Bond\n;\"Dr. No\"\n",
            $fileContents
        );

        $writer->finish();
    }
}
