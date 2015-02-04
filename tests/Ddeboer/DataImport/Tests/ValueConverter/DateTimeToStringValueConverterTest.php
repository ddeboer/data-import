<?php

namespace Ddeboer\DataImport\Tests\ValueConverter;

use Ddeboer\DataImport\ValueConverter\DateTimeToStringValueConverter;

class DateTimeToStringValueConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertWithoutOutputFormatReturnsString()
    {
        $value = new \DateTime('2010-01-01 01:00:00');
        $converter = new DateTimeToStringValueConverter;
        $output = $converter->convert($value);
        $this->assertEquals('2010-01-01 01:00:00', $value->format('Y-m-d H:i:s'));
    }

    public function testInvalidInputFormatThrowsException()
    {
        $value = '14/10/2008 09:40:20';
        $converter = new DateTimeToStringValueConverter;
        $this->setExpectedException("UnexpectedValueException", "Input must be DateTime object.");
        $converter->convert($value);
    }
}
