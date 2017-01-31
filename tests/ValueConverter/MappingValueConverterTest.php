<?php
namespace Ddeboer\DataImport\Tests\ValueConverter;

use Ddeboer\DataImport\ValueConverter\MappingValueConverter;

class MappingValueConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Ddeboer\DataImport\Exception\UnexpectedValueException
     * @expectedExceptionMessage Cannot find mapping for value "unexpected value"
     */
    public function testConvert()
    {
        $converter = new MappingValueConverter(array(
            'something' => null,
            'source' => 'destination'
        ));

        $this->assertSame('destination', call_user_func($converter, 'source'));
        $this->assertSame(null, call_user_func($converter, 'something'));
        call_user_func($converter, 'unexpected value');
    }
}
