<?php

namespace Ddeboer\DataImport\ValueConverter;


abstract class AbstractExcelDateTimeConverter
{
    protected $format = 'Y-m-d H:i:s';

    /**
     * AbstractExcelDateTimeConverter constructor.
     * @param string $format
     */
    public function __construct($format = null)
    {
        if($format) {
            $this->format = $format;
        }
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return AbstractExcelDateTimeConverter
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param $input
     * @return \DateTime|null
     */
    public function __invoke($input)
    {
        if($input > 0) {
            $timestamp = ($input - 25569) * 86400;
            return new \DateTime(gmdate($this->getFormat(), $timestamp));
        }

        return null;
    }
}
