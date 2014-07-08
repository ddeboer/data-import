<?php
namespace Ddeboer\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\MappingItemConverter;

/**
 * An item converter that takes an input containing nested arrays from a reader, and returns a modified item based on
 * mapped keys.
 *
 * @author Adam Paterson <hello@adampaterson.co.uk>
 */
class NestedMappingItemConverter extends MappingItemConverter
{
    /**
     * @var string
     */
    protected $nestKey;

    /**
     * @param array $mappings
     * @param string $nestKey
     */
    public function __construct($nestKey, array $mappings = array())
    {
        parent::__construct($mappings);
        $this->nestKey = $nestKey;
    }

    /**
     * @param array $item
     * @param string $from
     * @param string $to
     * @return array
     */
    protected function applyMapping(array $item, $from, $to)
    {
        $item = parent::applyMapping($item, $from, $to);

        if(!is_array($to)) {
            return $item;
        }

        foreach ($to as $nestedFrom => $nestedTo) {
            if (!array_key_exists($this->nestKey, $item)) {
                return $item;
            }

            foreach ($item[$this->nestKey] as $nestedItem) {
                $this->applyMapping($nestedItem, $from, $to);
            }
        }
        return $item;
    }
}