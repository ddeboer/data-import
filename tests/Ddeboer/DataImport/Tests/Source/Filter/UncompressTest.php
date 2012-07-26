<?php

namespace Ddeboer\DataImport\Tests\Source\Filter;

use Ddeboer\DataImport\Source\Filter\Uncompress;

class UncompressTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $uncompress = new Uncompress();
        
        $file = $uncompress->filter($this->getTempFile());

        $this->assertInstanceOf('\SplFileObject', $file);
        $this->assertEquals('This is a test file', file_get_contents($file));
    }

    protected function getTempFile()
    {
        $tempFile = tempnam(null, null) . '.Z';
        copy(__DIR__ . '/../../Fixtures/uncompress.txt.Z', $tempFile);
        return new \SplFileObject($tempFile);
    }
}