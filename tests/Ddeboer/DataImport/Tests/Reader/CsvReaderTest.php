<?php

namespace Ddeboer\DataImport\Tests\Reader;

use Ddeboer\DataImport\Reader\CsvReader;

class CsvReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testReadCsvFileWithColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setHeaderRowNumber(0);

        $this->assertEquals(array('id', 'number', 'description'),
            $csvReader->getColumnHeaders());

        $this->assertEquals(3, $csvReader->count());

        foreach ($csvReader as $row) {
            $this->assertNotNull($row['id']);
            $this->assertNotNull($row['number']);
            $this->assertNotNull($row['description']);
        }

        $this->assertEquals(array(
            'id'        => 6,
            'number'    => '456',
            'description' => 'Another description'
        ), $csvReader->getRow(2));
    }

    public function testReadCsvFileWithoutColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_no_column_headers.csv');
        $csvReader = new CsvReader($file);

        $this->assertNull($csvReader->getColumnHeaders());
        $this->assertEquals(3, $csvReader->count());
    }

    public function testReadCsvFileWithManualColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_no_column_headers.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setColumnHeaders(array('id', 'number', 'description'));

        foreach ($csvReader as $row) {
            $this->assertNotNull($row['id']);
            $this->assertNotNull($row['number']);
            $this->assertNotNull($row['description']);
        }
    }
}