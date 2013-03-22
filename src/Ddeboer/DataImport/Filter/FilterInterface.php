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
    public function filter(array $item);
}
