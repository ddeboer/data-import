<?php

namespace Ddeboer\DataImport\Writer;

/**
 * Persists data in a storage medium, such as a database, CSV or XML file, etc.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
interface WriterInterface
{
    /**
     * Prepare the writer before writing the items
     *
     * @return Writer
     */
    function prepare();

    /**
     * Write one data item
     *
     * @param array $item         The data item with converted values
     * @param array $originalItem The data item with its original values
     *
     * @return Writer
     */
    function writeItem(array $item, array $originalItem = array());

    /**
     * Wrap up the writer after all items have been written
     *
     * @return Writer
     */
    function finish();
}
