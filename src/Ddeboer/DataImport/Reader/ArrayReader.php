<?php

namespace Ddeboer\DataImport\Reader;

/**
 * Reads an array
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class ArrayReader implements ReaderInterface
{
    /**
     * Construt array reader
     *
     * @param array $data Data array
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        // Examine first row
        if ($this->count() > 0) {
            return array_keys($this->data[0]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->data[$this->key()]);
    }
}
