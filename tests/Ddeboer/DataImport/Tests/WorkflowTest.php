<?php

namespace Ddeboer\DataImport\Tests;

use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Filter\CallbackFilter;
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;
use Ddeboer\DataImport\Writer\CallbackWriter;

class WorkflowTest extends \PHPUnit_Framework_TestCase
{
    public function testAddCallbackFilter()
    {
        $this->getWorkflow()->addFilter(new CallbackFilter(function($input) {
            return true;
        }));
    }

    public function testAddCallbackValueConverter()
    {
        $this->getWorkflow()->addValueConverter('someField', new CallbackValueConverter(function($input) {
            return str_replace('-', '', $input);
        }));
    }

    public function testAddCallbackItemConverter()
    {
        $this->getWorkflow()->addItemConverter(new CallbackItemConverter(function(array $input) {
            foreach ($input as $k=>$v) {
                if (!$v) {
                    unset($input[$k]);
                }
            }

            return $input;
        }));
    }

    public function testAddCallbackWriter()
    {
        $this->getWorkflow()->addWriter(new CallbackWriter(function($item) {
//            var_dump($item);
        }));
    }

    public function testWriterIsPreparedAndFinished()
    {
        $writer = $this->getMockBuilder('\Ddeboer\DataImport\Writer\CallbackWriter')
            ->disableOriginalConstructor()
            ->getMock();

        $writer->expects($this->once())
            ->method('prepare');

        $writer->expects($this->once())
            ->method('finish');

        $this->getWorkflow()->addWriter($writer)
            ->process();
    }

    public function testMappingAnItem()
    {
        $originalData = array(array('foo' => 'bar'));

        $ouputTestData = array();

        $writer = new ArrayWriter($ouputTestData);
        $reader = new ArrayReader($originalData);

        $workflow = new Workflow($reader);

        $workflow->addMapping('foo', 'bar')
            ->addWriter($writer)
            ->process()
        ;

        $this->assertArrayHasKey('bar', $ouputTestData[0]);
    }

    public function testGlobalMappingAnItem()
    {
        $originalData = array(array(
            'foo' => 'bar',
            'baz' => array('another' => 'thing')
        ));

        $ouputTestData = array();

        $writer = new ArrayWriter($ouputTestData);
        $reader = new ArrayReader($originalData);

        $workflow = new Workflow($reader);

        $mapping = array(
            'foo' => 'bazinga',
            'baz' => array('another' => 'somethingelse')
        );

        $workflow->setGlobalMapping($mapping)
            ->addWriter($writer)
            ->process()
        ;

        $this->assertArrayHasKey('bazinga', $ouputTestData[0]);
        $this->assertArrayHasKey('somethingelse', $ouputTestData[0]['baz']);
    }

    public function testGlobalMappingAnItemInCombinationWithMappingAnItem()
    {
        $originalData = array(array(
            'foo' => 'bar',
            'baz' => array('another' => 'thing')
        ));

        $ouputTestData = array();

        $writer = new ArrayWriter($ouputTestData);
        $reader = new ArrayReader($originalData);

        $workflow = new Workflow($reader);

        $mapping = array(
            'foo' => 'bazinga',
            'bazoo' => array('another' => 'somethingelse')
        );

        $workflow->setGlobalMapping($mapping)
            ->addMapping('baz', 'bazoo')
            ->addWriter($writer)
            ->process()
        ;

        $this->assertArrayHasKey('bazinga', $ouputTestData[0]);
        $this->assertArrayHasKey('bazoo', $ouputTestData[0]);
        $this->assertArrayHasKey('somethingelse', $ouputTestData[0]['bazoo']);
    }

    public function testWorkflowWithObjects()
    {
        $reader = new ArrayReader(array(
            new Dummy('foo'),
            new Dummy('bar'),
            new Dummy('foobar'),
        ));

        $data = array();
        $writer = new ArrayWriter($data);

        $workflow = new Workflow($reader);
        $workflow->addWriter($writer);
        $workflow->addItemConverter(new CallbackItemConverter(function($item) {
            return array('name' => $item->name);
        }));
        $workflow->addValueConverter('name', new CallbackValueConverter(function($name) {
            return strrev($name);
        }));
        $workflow->process();

        $this->assertEquals(array(
            array('name' => 'oof'),
            array('name' => 'rab'),
            array('name' => 'raboof')
        ), $data);
    }

    /**
     * @expectedException \Ddeboer\DataImport\Exception\UnexpectedTypeException
     */
    public function testItemConverterWhichReturnObjects()
    {
        $reader = new ArrayReader(array(
            new Dummy('foo'),
            new Dummy('bar'),
            new Dummy('foobar'),
        ));

        $data = array();
        $writer = new ArrayWriter($data);

        $workflow = new Workflow($reader);
        $workflow->addWriter($writer);
        $workflow->addItemConverter(new CallbackItemConverter(function($item) {
            return $item;
        }));

        $workflow->process();
    }

    protected function getWorkflow()
    {
        $reader = $this->getMockBuilder('\Ddeboer\DataImport\Reader\CsvReader')
            ->disableOriginalConstructor()
            ->getMock();

        return new Workflow($reader);
    }
}

class Dummy
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}
