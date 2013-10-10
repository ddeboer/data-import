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

        $this->assertEquals(
            array(
                'id', 'number', 'description'
            ),
            $csvReader->getFields()
        );

        foreach ($csvReader as $row) {
            $this->assertNotNull($row['id']);
            $this->assertNotNull($row['number']);
            $this->assertNotNull($row['description']);
        }

        $this->assertEquals(
            array(
                'id'        => 6,
                'number'    => '456',
                'description' => 'Another description'
            ),
            $csvReader->getRow(2)
        );
    }

    public function testReadCsvFileWithoutColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_no_column_headers.csv');
        $csvReader = new CsvReader($file);

        $this->assertNull($csvReader->getColumnHeaders());
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

    public function testReadCsvFileWithTrailingBlankLines()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_blank_lines.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setColumnHeaders(array('id', 'number', 'description'));

        foreach ($csvReader as $row) {
            $this->assertNotNull($row['id']);
            $this->assertNotNull($row['number']);
            $this->assertNotNull($row['description']);
        }
    }

    public function testCountWithoutHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_no_column_headers.csv');
        $reader = new CsvReader($file);
        $this->assertEquals(3, $reader->count());
    }

    public function testCountWithHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers.csv');
        $reader = new CsvReader($file);
        $reader->setHeaderRowNumber(0);
        $this->assertEquals(3, $reader->count(), 'Row count should not include header');
    }

    public function testCountWithFewerElementsThanColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_fewer_elements_than_column_headers.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setHeaderRowNumber(0);

        $this->assertEquals(3, $csvReader->count());
    }

    public function testCountWithMoreElementsThanColumnHeadersInvalid()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_more_elements_than_column_headers_invalid.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setHeaderRowNumber(0);

        $errors = $csvReader->getErrors();
        $this->assertEquals(2, key($errors));
        $this->assertEquals(array ('6', '456', 'Another description', 'Some more info'), current($errors));
    }

    public function testVaryingElementCountWithColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers_varying_element_count.csv');
        $reader = new CsvReader($file);
        $reader->setHeaderRowNumber(0);

        $this->assertTrue($reader->hasErrors());

        $this->assertCount(1, $reader->getErrors());

        $errors = $reader->getErrors();
        $this->assertEquals(3, key($errors));
        $this->assertEquals(array('7', '7890', 'Some more info', 'too many columns'), current($errors));
    }

    public function testVaryingElementCountWithoutColumnHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_no_column_headers_varying_element_count.csv');
        $reader = new CsvReader($file);
        $reader->setColumnHeaders(array('id', 'number', 'description'));

        $this->assertTrue($reader->hasErrors());
        $this->assertCount(1, $reader->getErrors());

        $errors = $reader->getErrors();
        $this->assertEquals(3, key($errors));
        $this->assertEquals(array(3, 230, 'Yet more info', 'Even more info'), current($errors));
    }
}
