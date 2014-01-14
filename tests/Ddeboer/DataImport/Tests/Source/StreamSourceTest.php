<?php

namespace Ddeboer\DataImport\Tests\Source;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Source\StreamSource;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StreamSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testCsv()
    {
        $fixture = __DIR__.'/../Fixtures/data_column_headers.csv';
        $source = new StreamSource($fixture);
        $reader = new CsvReader($source);

        $this->assertCount(4, $reader);
    }

    /**
     * @expectedException \Ddeboer\DataImport\Exception\SourceNotFoundException
     */
    public function testInvalidFilename()
    {
        $fixture = 'notworking://test.csv';

        $source = new StreamSource($fixture);
        $source->rewind();
    }
}
