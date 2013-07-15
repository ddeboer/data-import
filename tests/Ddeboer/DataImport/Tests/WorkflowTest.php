<?php

namespace Ddeboer\DataImport\Tests;

use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Tests\Fixtures\TestWriter;
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

		$writer = new TestWriter($ouputTestData);
		$reader = new ArrayReader($originalData);

		$workflow = new Workflow($reader);

		$workflow->addMapping('foo', 'bar')
			->addWriter($writer)
			->process()
		;

		$this->assertArrayHasKey('bar', $ouputTestData[0]);
	}

    protected function getWorkflow()
    {
        $reader = $this->getMockBuilder('\Ddeboer\DataImport\Reader\CsvReader')
            ->disableOriginalConstructor()
            ->getMock();

        return new Workflow($reader);
    }
}
