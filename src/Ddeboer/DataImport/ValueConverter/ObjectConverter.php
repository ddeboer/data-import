<?php

namespace Ddeboer\DataImport\ValueConverter;

use Ddeboer\DataImport\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Util\PropertyPath;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz
 */
class ObjectConverter implements ValueConverterInterface
{
    /**
     * @var string|null
     */
    protected $propertyPath;

    /**
     * Constructor
     *
     * @param string|null $propertyPath
     */
    public function __construct($propertyPath = null)
    {
        $this->propertyPath = $propertyPath;
    }

    /**
     * Sets the property
     *
     * @param string $propertyPath
     */
    public function setPropertyPath($propertyPath)
    {
        $this->propertyPath = $propertyPath;
    }

    /**
     * Gets the property
     *
     * @return null|string
     */
    public function getPropertyPath()
    {
        return $this->propertyPath;
    }

    /**
     * {@inheritDoc}
     */
    public function convert($input)
    {
        if (!is_object($input)) {
            throw new UnexpectedTypeException($input, 'object');
        }

        if (null === $this->propertyPath && !method_exists($input, '__toString')) {
            throw new \RuntimeException;
        }

        if (null === $this->propertyPath) {
            return (string) $input;
        }

        $path = new PropertyPath($this->propertyPath);
        return $path->getValue($input);
    }
}
