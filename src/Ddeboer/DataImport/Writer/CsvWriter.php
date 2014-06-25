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
    private $headers = array();

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
    public function writeItem(array $item)
    {
        fputcsv($this->fp, $item, $this->delimiter, $this->enclosure);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function finish()
    {
        fclose($this->fp);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        foreach ($this->headers as $header) {
            $this->writeItem($header);
        }
    }

    /**
     * Add header line in the first lines of the csv
     *
     * @param array $header
     *
     * @return \Ddeboer\DataImport\Writer\CsvWriter
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
        return $this;
    }
}
