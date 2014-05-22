<?php

namespace Ddeboer\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\MappingItemConverter;

class NestedMappingItemConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        $input = array(
            'foo'   => 'bar',
            'baz' => array(
                array(
                    'another'   => 'thing'
                )
            )
        );

        $mappings = array(
            'foo'   => 'foobar',
            'baz' => array(
                array(
                    'another'   => 'different_thing',
                )
            )
        );

        $converter = new NestedMappingItemConverter($mappings, 'baz');
        $output = $converter->convert($input);

        $expected = array(
            'foobar'   => 'bar',
            'baz' => array(
                array('different_thing' => 'thing'),
            )
        );
        $this->assertEquals($expected, $output);
    }
}
 