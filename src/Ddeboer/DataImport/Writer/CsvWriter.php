<?php

namespace Ddeboer\DataImport\Writer;

/**
 * Writes to a CSV file
 *
 */
class CsvWriter extends AbstractWriter
{
    private $delimiter = ';';
    private $enclosure = '"';

    /**
     * Constructor
     *
     * @param \SplFileObject $file      CSV file
     * @param string         $mode      See http://php.net/manual/en/function.fopen.php
     * @param string         $delimiter The delimiter
     * @param string         $enclosure The enclosure
     */
    public function __construct(\SplFileObject $file, $mode = 'w', $delimiter = ';', $enclosure = '"')
    {
        $this->fp = fopen($file->getPathname(), $mode);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item, array $originalItem = array())
    {
        fputcsv($this->fp, $item, $this->delimiter, $this->enclosure);
    }

    /**
     * {@inheritdoc}
     */
    public function finish()
    {
        fclose($this->fp);
    }
}