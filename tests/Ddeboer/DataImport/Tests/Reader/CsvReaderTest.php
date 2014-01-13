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
        $csvReader = new CsvReader($file);
        $this->assertEquals(3, $csvReader->count());
    }

    public function testCountWithHeaders()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setHeaderRowNumber(0);
        $this->assertEquals(3, $csvReader->count(), 'Row count should not include header');
    }

    public function testCountWithFewerElementsThanColumnHeadersNotStrict()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_fewer_elements_than_column_headers.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setStrict(false);
        $csvReader->setHeaderRowNumber(0);

        $this->assertEquals(3, $csvReader->count());
    }

    public function testCountWithMoreElementsThanColumnHeadersNotStrict()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_more_elements_than_column_headers.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setStrict(false);
        $csvReader->setHeaderRowNumber(0);

        $this->assertEquals(3, $csvReader->count());
        $this->assertFalse($csvReader->hasErrors());
        $this->assertEquals(array(6, 456, 'Another description'), array_values($csvReader->getRow(2)));
    }

    public function testCountDoesNotMoveFilePointer()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setHeaderRowNumber(0);

        $key_before_count = $csvReader->key();
        $csvReader->count();
        $key_after_count = $csvReader->key();

        $this->assertEquals($key_after_count, $key_before_count);
    }

    public function testVaryingElementCountWithColumnHeadersNotStrict()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers_varying_element_count.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setStrict(false);
        $csvReader->setHeaderRowNumber(0);

        $this->assertEquals(4, $csvReader->count());
        $this->assertFalse($csvReader->hasErrors());
    }

    public function testVaryingElementCountWithoutColumnHeadersNotStrict()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_no_column_headers_varying_element_count.csv');
        $csvReader = new CsvReader($file);
        $csvReader->setStrict(false);
        $csvReader->setColumnHeaders(array('id', 'number', 'description'));

        $this->assertEquals(5, $csvReader->count());
        $this->assertFalse($csvReader->hasErrors());
    }

    public function testInvalidCsv()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_column_headers_varying_element_count.csv');
        $reader = new CsvReader($file);
        $reader->setHeaderRowNumber(0);

        $this->assertTrue($reader->hasErrors());

        $this->assertCount(2, $reader->getErrors());

        $errors = $reader->getErrors();
        $this->assertEquals(2, key($errors));
        $this->assertEquals(array('123', 'test'), current($errors));

        next($errors);
        $this->assertEquals(3, key($errors));
        $this->assertEquals(array('7', '7890', 'Some more info', 'too many columns'), current($errors));
    }

    public function testLastRowInvalidCsv()
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/data_no_column_headers_varying_element_count.csv');
        $reader = new CsvReader($file);
        $reader->setColumnHeaders(array('id', 'number', 'description'));

        $this->assertTrue($reader->hasErrors());
        $this->assertCount(3, $reader->getErrors());

        $errors = $reader->getErrors();
        $this->assertEquals(1, key($errors));
        $this->assertEquals(array('6', 'strictly invalid'), current($errors));

        next($errors);
        $this->assertEquals(3, key($errors));
        $this->assertEquals(array('3','230','Yet more info','Even more info'), current($errors));

        next($errors);
        $this->assertEquals(4, key($errors));
        $this->assertEquals(array('strictly invalid'), current($errors));
    }

    public function testLineBreaks()
    {
        $reader = $this->getReader('data_cr_breaks.csv');
        $this->assertCount(3, $reader);
    }

    protected function getReader($filename)
    {
        $file = new \SplFileObject(__DIR__.'/../Fixtures/'.$filename);

        return new CsvReader($file);
    }
}
