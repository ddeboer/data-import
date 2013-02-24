<?php

namespace Ddeboer\DataImport\Reader;

/**
 * Iterator that reads data to be imported
 *
 * @author David de Boer <david@ddeboer.nl>
 */
interface ReaderInterface extends \Iterator, \Countable
{
    /**
     * Get the field (column, property) names
     *
     * @return array
     */
    public function getFields();

    /**
     * Get the number of data items (rows, elements)
     *
     * @return int
     */
    public function count();
}
