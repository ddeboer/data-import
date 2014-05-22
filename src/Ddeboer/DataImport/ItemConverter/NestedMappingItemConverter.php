<?php
namespace Ddeboer\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\MappingItemConverter;

/**
 * Class NestedMappingItemConverter
 * @package Ddeboer\DataImport\ItemConverter
 * @author Adam Paterson <hello@adampaterson.co.uk>
 */
class NestedMappingItemConverter extends MappingItemConverter
{
    /**
     * @var string
     */
    protected $nestedKey;

    /**
     * @param array $mappings
     * @param $nestedKey
     */
    public function __construct(array $mappings, $nestedKey)
    {
        parent::__construct($mappings);
        $this->nestedKey = $nestedKey;
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
            if (!array_key_exists($this->nestedKey, $item)) {
                return $item;
            }

            foreach ($item[$this->nestedKey] as $nestedItem) {
                $this->applyMapping($nestedItem, $from, $to);
            }
        }
        return $item;
    }
}