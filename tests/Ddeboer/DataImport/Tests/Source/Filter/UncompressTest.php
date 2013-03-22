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
        $this->assertEquals('This is a test file', \file_get_contents($file->getPathname()));
    }

    protected function getTempFile()
    {
        return new \SplFileObject(__DIR__ . '/../../Fixtures/uncompress.txt.Z');
    }
}
