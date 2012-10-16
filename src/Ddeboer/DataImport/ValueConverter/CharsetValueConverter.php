<?php

namespace Ddeboer\DataImport\ValueConverter;

use Ddeboer\DataImport\Exception\UnexpectedTypeException;

/**
 * Convert a value in a specific charset
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class CharsetValueConverter implements ValueConverterInterface
{
    /**
     * @var string
     */
    private $charset;

    /**
     * Constructor
     *
     * @param string $charset
     */
    public function __construct($charset)
    {
        $this->charset = $charset;
    }

    /**
     * {@inheritDoc}
     */
    public function convert($input)
    {
        if (!is_string($input)) {
            throw new UnexpectedTypeException($input, 'string');
        }

        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($input, $this->charset);
        }
        if (function_exists('iconv')) {
            return iconv('UTF-8', $this->charset, $input);
        }

        throw new \RuntimeException('Could not convert the charset. Please install mbstring or iconv!');
    }
}
