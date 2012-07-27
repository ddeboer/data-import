<?php

namespace Ddeboer\DataImport\Reader;

/**
 * Reads an array
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class ArrayReader implements ReaderInterface
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function count()
    {
        return count($this->data);
    }

    public function current()
    {
        return current($this->data);
    }

    public function getFields()
    {
        // Examine first row
        if ($this->count() > 0) {
            return array_keys($this->data[0]);
        }
    }

    public function key()
    {
        return key($this->data);
    }

    public function next()
    {
        return next($this->data);
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function valid()
    {
        return isset($this->data[$this->key()]);
    }
}