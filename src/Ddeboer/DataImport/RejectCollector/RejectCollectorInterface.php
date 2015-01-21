<?php

namespace Ddeboer\DataImport\RejectCollector;

use Ddeboer\DataImport\Filter\FilterInterface;

/**
 * A reject collector is fed rejected items and does whatever it wants with it
 */
interface RejectCollectorInterface
{
    /**
     * Handle rejected items
     *
     * @param array           $rejectedItem    a rejected item
     * @param FilterInterface $rejectingFilter the filter responsible for the item rejection
     */
    public function collect(array $rejectedItem, FilterInterface $rejectingFilter);

    /**
     * Get filter priority (higher number means higher priority)
     *
     * @return int
     */
    public function getPriority();
}
