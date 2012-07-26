<?php

namespace Ddeboer\DataImport\Tests\ValueConverter;

use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

class DateTimeValueConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertWithoutFormat()
    {
        $value = '2011-10-20 13:05';
        $converter = new DateTimeValueConverter;
        $output = $converter->convert($value);
        $this->assertInstanceOf('\DateTime', $output);
        $this->assertEquals('13', $output->format('H'));
    }

    public function testConvertWithFormat()
    {
        $value = '14/10/2008 09:40:20';
        $converter = new DateTimeValueConverter('d/m/Y H:i:s');
        $output = $converter->convert($value);
        $this->assertInstanceOf('\DateTime', $output);
        $this->assertEquals('20', $output->format('s'));
    }
}