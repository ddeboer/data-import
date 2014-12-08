<?php

namespace Ddeboer\DataImport\Exception;

class ValueConversionException extends \UnexpectedValueException implements ExceptionInterface
{
    public function __construct($property, $previousException)
    {
        parent::__construct(sprintf('Unable to convert value for "%s"', $property),null,$previousException);
    }
}
