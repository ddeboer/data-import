<?php

namespace Ddeboer\DataImport\Tests\Source;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Reader\ExcelReader;
use Ddeboer\DataImport\Source\HttpSource;

class HttpSourceTest extends \PHPUnit_Framework_TestCase
{
    protected $source;
    protected $fixturesUrl = 'https://raw2.github.com/ddeboer/data-import/master/tests/Ddeboer/DataImport/Tests/Fixtures/';

    public function testCsv()
    {
        $reader = new CsvReader(new HttpSource($this->getCsvFile()));
        $this->assertCount(4, $reader);
    }

    public function testExcel()
    {
        $reader = new ExcelReader(new HttpSource($this->getExcelFile()));
        $this->assertCount(4, $reader);
    }

    protected function getCsvFile()
    {
        return $this->getFile($this->fixturesUrl . 'data_column_headers.csv');
    }

    protected function getExcelFile()
    {
        return $this->getFile($this->fixturesUrl . 'data_column_headers.xlsx');
    }

    protected function getFile($original)
    {
        $info = pathinfo($original);
        $cached = sys_get_temp_dir() . '/' . $info['basename'];

        if (!file_exists($cached)) {
            file_put_contents(
                $cached,
                file_get_contents($original)
            );
        }

        return $cached;
    }
}