<?php

namespace Ddeboer\DataImport\ValueConverter;

/**
 * Convert an input value to a PHP \DateTime object
 *
 */
class DateTimeValueConverter implements ValueConverterInterface
{
    /**
     * Date time format
     *
     * @var string
     * @see http://php.net/manual/en/datetime.createfromformat.php
     */
    protected $format;

    /**
     * Construct a DateTime converter
     *
     * @param string $format Optional
     */
    public function __construct($format = null)
    {
        $this->format = $format;
    }

    /**
     * Convert string to date time object
     *
     * @param string $input
     *
     * @return \DateTime
     */
    public function convert($input)
    {
        if (!$input) {
            return;
        }

        if ($this->format) {
            $date = \DateTime::createFromFormat($this->format, $input);
            if (false === $date) {
                throw new \UnexpectedValueException(
                    $input . ' is not a valid date/time according to format ' . $this->format);
            }

            return $date;
        }

        return new \DateTime($input);
    }
}
