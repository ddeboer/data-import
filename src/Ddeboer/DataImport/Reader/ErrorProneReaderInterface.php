<?php

namespace Ddeboer\DataImport\Reader;

/**
 * Reader that provides feedback for read errors
 *
 * @author buddhaCode <buddhaCode@users.noreply.github.com>
 */
interface ErrorProneReaderInterface extends ReaderInterface
{
    /**
     * Get rows that have an invalid number of columns
     *
     * @return array
     */
    public function getErrors();

    /**
     * Does the reader contain any invalid rows?
     *
     * @return bool
     */
    public function hasErrors();

}
