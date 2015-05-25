<?php

namespace Ddeboer\DataImport\Writer;

/**
 * This class writes an item into an array that was passed by reference
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class ArrayWriter implements WriterInterface
{
    use WriterTemplate;

    /**
     * @var array
     */
    protected $data;

    /**
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
        $this->data = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        $this->data[] = $item;
    }
}
