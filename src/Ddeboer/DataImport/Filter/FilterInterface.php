<?php

namespace Ddeboer\DataImport\Filter;

/**
 * A filter decides whether an item is accepted into the import workflow
 */
interface FilterInterface
{
    /**
     * @return boolean
     */
    function filter(array $item);
}