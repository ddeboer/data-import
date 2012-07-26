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
     * @param \SplFileObject $file CSV file
     */
    public function __construct(\SplFileObject $file)
    {
        $this->fp = fopen($file->getPathname(), 'w');
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item, array $originalItem = array())
    {
//        foreach ($cells as &$cell) {
//            $cell = iconv('UTF-8', 'Windows-1252', $cell);
//        }
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