<?php

namespace Ddeboer\DataImport\Tests;

use Ddeboer\DataImport\Util\TempFile;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class TempFileTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $file = new TempFile();
        $this->assertInstanceOf('SplFileObject', $file);
    }

    public function testWrite()
    {
        $file = new TempFile();
        $file->fwrite('foobar');
        $file->fseek(0);

        $this->assertEquals('foobar', $file->fgets());
    }

    public function testIsFile()
    {
        $file = new TempFile();
        $this->assertTrue(is_file($file->getRealPath()));
    }
}
