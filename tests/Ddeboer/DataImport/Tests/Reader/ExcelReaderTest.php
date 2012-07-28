<?php

namespace Ddeboer\DataImport\Tests\Reader;

use Ddeboer\DataImport\Reader\ExcelReader;

class ExcelReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFields()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers.xlsx');
        $reader = new ExcelReader($file, 0);
        $this->assertEquals(array('id', 'number', 'description'), $reader->getFields());
    }

    public function testCountWithoutHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_no_column_headers.xls');
        $reader = new ExcelReader($file);
        $this->assertEquals(3, $reader->count());
    }

    public function testCountWithHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers.xlsx');
        $reader = new ExcelReader($file, 0);
        $this->assertEquals(3, $reader->count());
    }

    public function testIterate()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers.xlsx');
        $reader = new ExcelReader($file, 0);
        foreach ($reader as $row) {
            $this->assertInternalType('array', $row);
            $this->assertEquals(array('id', 'number', 'description'), array_keys($row));
        }
    }
}