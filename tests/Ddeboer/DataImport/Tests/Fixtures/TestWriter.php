<?php

namespace Ddeboer\DataImport\Tests\Fixtures;

use Ddeboer\DataImport\Writer\WriterInterface;

/**
 * This class is used to test output from Workflow->process class.
 * The data array is passed as reference so the modifications can be tested.
 *
 * Class TestWriter
 */
class TestWriter implements WriterInterface
{
	protected $data;

	public function __construct(&$data)
	{
		$this->data = &$data;
	}

	/**
	 * Prepare the writer before writing the items
	 *
	 * @return Writer
	 */
	public function prepare()
	{
		$this->data = array();
	}

	/**
	 * Write one data item
	 *
	 * @param array $item         The data item with converted values
	 * @param array $originalItem The data item with its original values
	 *
	 * @return Writer
	 */
	public function writeItem(array $item, array $originalItem = array())
	{
		$this->data[] = $item;
	}

	/**
	 * Wrap up the writer after all items have been written
	 *
	 * @return Writer
	 */
	public function finish()
	{
		// TODO: Implement finish() method.
	}
}
