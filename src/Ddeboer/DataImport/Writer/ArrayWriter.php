<?php

namespace Ddeboer\DataImport\Writer;

/**
 * This class writes an item into an array that was passed by reference
 *
 * Class ArrayWriter
 */
class ArrayWriter implements WriterInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array &$data)
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
    public function writeItem(array $item, $originalItem = array())
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

    }
}
