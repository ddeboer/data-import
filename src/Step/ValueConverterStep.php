<?php

namespace Ddeboer\DataImport\Step;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ValueConverterStep implements StepInterface
{
    /**
     * @var array
     */
    private $converters = [];

    /**
     * @param string   $property
     * @param callable $converter
     */
    public function add($property, callable $converter)
    {
        if (!isset($this->converters[$property])) {
            $this->converters[$property] = new \SplObjectStorage();
        }

        $this->converters[$property]->attach($converter);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process(&$item)
    {
        $accessor = new PropertyAccessor();

        foreach ($this->converters as $property => $converters) {
            foreach ($converters as $converter) {
                $orgValue = $accessor->getValue($item, $property);
                $value = call_user_func($converter, $orgValue);
                $accessor->setValue($item,$property,$value);
            }
        }

        return true;
    }
}
