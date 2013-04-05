<?php
namespace Ddeboer\DataImport\Tests\Source;

use Ddeboer\DataImport\Source\StringSource;
use Ddeboer\DataImport\Reader\CsvReader;

class StringSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFilename()
    {
        $data = \file_get_contents(__DIR__.'/../Fixtures/data_column_headers.csv');
        $source = new StringSource($data);
        
        $file = $source->getFile();
        $this->assertInstanceOf('SplFileObject', $file);

        $reader = new CsvReader($file);
        $this->assertCount(4, $reader);
    }
}
