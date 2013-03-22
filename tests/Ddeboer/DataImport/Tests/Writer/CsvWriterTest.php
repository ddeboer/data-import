<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\CsvWriter;

class CsvWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteItem()
    {
        $outputFile = new \SplFileObject(tempnam('/tmp', null));
        $writer = new CsvWriter($outputFile);

        $writer->writeItem(array(
            'firstProperty:', 'secondProperty:'
        ));

        $writer->writeItem(array(
            'firstProperty' => 'some value',
            'secondProperty' => 'some other value'
        ));

        $fileContents = file_get_contents($outputFile->getPathname());
        $this->assertEquals("firstProperty:;secondProperty:\n\"some value\";\"some other value\"\n",
            $fileContents);

        $writer->finish();
    }
}
