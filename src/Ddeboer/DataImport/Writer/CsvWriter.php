<?php

namespace Ddeboer\DataImport\Writer;

/**
 * Writes to a CSV file
 */
class CsvWriter extends AbstractStreamWriter
{
    private $delimiter = ';';
    private $enclosure = '"';

    /**
     * Constructor
     *
     * @param string   $delimiter The delimiter
     * @param string   $enclosure The enclosure
     * @param resource $stream
     */
    public function __construct($delimiter = ';', $enclosure = '"', $stream = null)
    {
        parent::__construct($stream);

        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        fputcsv($this->getStream(), $item, $this->delimiter, $this->enclosure);

        return $this;
    }
}
