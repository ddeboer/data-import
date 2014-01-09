<?php
namespace Ddeboer\DataImport\Tests\Source;

use Ddeboer\DataImport\Reader\ExcelReader;
use Ddeboer\DataImport\Source\StringSource;
use Ddeboer\DataImport\Reader\CsvReader;

class StringSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testCsv()
    {
        $data = \file_get_contents(__DIR__.'/../Fixtures/data_column_headers.csv');
        $source = new StringSource($data);

        $this->assertInstanceOf('\SplFileObject', $source);

        $reader = new CsvReader($source);
        $this->assertCount(4, $reader);
    }

    public function testExcel()
    {
        $data = \file_get_contents(__DIR__.'/../Fixtures/data_column_headers.xlsx');
        $source = new StringSource($data);

        $reader = new ExcelReader($source);
        $this->assertCount(4, $reader);
    }
}