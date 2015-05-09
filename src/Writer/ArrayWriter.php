<?php

namespace Ddeboer\DataImport\Writer;

/**
 * This class writes an item into an array that was passed by reference
 *
 * @author David de Boer <david@ddeboer.nl>
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
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->data = array();
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        $this->data[] = $item;
    }

    /**
     * {@inheritdoc}
     */
    public function finish()
    {

    }
}
