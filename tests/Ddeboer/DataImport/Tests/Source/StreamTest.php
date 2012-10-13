<?php

namespace Ddeboer\DataImport\Tests\Source;

use Ddeboer\DataImport\Source\Stream;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFilename()
    {
        $fixture = __DIR__.'/../Fixtures/data_cr_breaks.csv';

        $source = new Stream($fixture);
        $this->assertEquals(new \SplFileObject($fixture), $source->getFile());
    }

    /**
     * @expectedException Ddeboer\DataImport\Exception\SourceNotFoundException
     */
    public function testInvalidFilename()
    {
        $fixture = 'notworing://test.csv';

        $source = new Stream($fixture);
        $source->getFile();
    }
}
