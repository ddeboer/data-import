<?php

namespace Ddeboer\DataImport\Tests\Source;

use Ddeboer\DataImport\Source\StreamSource;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StreamSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFilename()
    {
        $fixture = __DIR__.'/../Fixtures/data_cr_breaks.csv';

        $source = new StreamSource($fixture);
        $this->assertEquals(new \SplFileObject($fixture), $source->getFile());
    }

    /**
     * @expectedException Ddeboer\DataImport\Exception\SourceNotFoundException
     */
    public function testInvalidFilename()
    {
        $fixture = 'notworking://test.csv';

        $source = new StreamSource($fixture);
        $source->getFile();
    }
}
