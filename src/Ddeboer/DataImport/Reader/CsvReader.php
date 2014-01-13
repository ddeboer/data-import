<?php

namespace Ddeboer\DataImport\Reader;

/**
 * Reads a CSV file, using as little memory as possible
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class CsvReader implements ReaderInterface, \SeekableIterator
{
    /**
     * Number of the row that contains the column names
     *
     * @var int
     */
    protected $headerRowNumber;

    /**
     * CSV file
     *
     * @var \SplFileObject
     */
    protected $file;

    /**
     * Column headers as read from the CSV file
     *
     * @var array
     */
    protected $columnHeaders;

    /**
     * Total number of rows in the CSV file
     *
     * @var int
     */
    protected $count;

    /**
     * Faulty CSV rows
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Strict parsing - skip any lines mismatching header length
     *
     * @var boolean
     */
    protected $strict = true;

    /**
     * Construct CSV reader
     *
     * @param \SplFileObject $file      CSV file
     * @param string         $delimiter Delimiter
     * @param string         $enclosure Enclosure
     * @param string         $escape    Escape characters
     */
    public function __construct(\SplFileObject $file, $delimiter = ';', $enclosure = '"', $escape = '\\')
    {
        ini_set('auto_detect_line_endings', true);

        $this->file = $file;
        $this->file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );
        $this->file->setCsvControl(
            $delimiter,
            $enclosure,
            $escape
        );
    }

    /**
     * Return the current row as an array
     *
     * If a header row has been set, an associative array will be returned
     *
     * @return array
     */
    public function current()
    {
        $line = $this->file->current();

        // If the CSV has column headers, use them to construct an associative
        // array for the columns in this line
        if (!empty($this->columnHeaders)) {
            $numColumnHeaders = count($this->columnHeaders);
            // In non-strict mode pad/slice the line to match the column headers
            if (!$this->isStrict()) {
                if ($numColumnHeaders > count($line)) {
                    $line = array_pad($line, $numColumnHeaders, null); // Line too short
                } else {
                    $line = array_slice($line, 0, $numColumnHeaders); // Line too long
                }
            }

            // Count the number of elements in both: they must be equal.
            if ($numColumnHeaders == count($line)) {
                return array_combine(
                    array_values($this->columnHeaders),
                    $line
                );
            } else {
                // They are not equal, so log the row as error and skip it.
                if ($this->valid()) {
                    $this->errors[$this->key()] = $line;
                    $this->next();

                    return $this->current();
                }
            }
        } else {
            // Else just return the column values
            return $line;
        }
    }

    /**
     * Get column headers
     *
     * @return array
     */
    public function getColumnHeaders()
    {
        return $this->columnHeaders;
    }

    /**
     * Set column headers
     *
     * @param array $columnHeaders
     *
     * @return CsvReader
     */
    public function setColumnHeaders(array $columnHeaders)
    {
        $this->columnHeaders = $columnHeaders;

        return $this;
    }

    /**
     * Rewind the file pointer
     *
     * If a header row has been set, the pointer is set just below the header
     * row. That way, when you iterate over the rows, that header row is
     * skipped.
     *
     */
    public function rewind()
    {
        $this->file->rewind();
        if (null !== $this->headerRowNumber) {
            $this->file->seek($this->headerRowNumber + 1);
        }
    }

    /**
     * Set header row number
     *
     * @param int $rowNumber Number of the row that contains column header names
     *
     * @return CsvReader
     */
    public function setHeaderRowNumber($rowNumber)
    {
        $this->headerRowNumber = $rowNumber;
        $this->columnHeaders = $this->readHeaderRow($rowNumber);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (null === $this->count) {
            $position = $this->key();

            $this->count = iterator_count($this);

            $this->seek($position);
        }

        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->file->next();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->file->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->file->key();
    }

    public function seek($pointer)
    {
        $this->file->seek($pointer);
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->columnHeaders;
    }

    /**
     * Get a row
     *
     * @param int $number Row number
     *
     * @return array
     */
    public function getRow($number)
    {
        $this->seek($number);

        return $this->current();
    }

    /**
     * Get rows that have an invalid number of columns
     *
     * @return array
     */
    public function getErrors()
    {
        if (0 === $this->key()) {
            // Iterator has not yet been processed, so do that now
            foreach ($this as $row) {
            }
        }

        return $this->errors;
    }

    /**
     * Does the reader contain any invalid rows?
     *
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }


    /**
     * Should the reader use strict parsing?
     *
     * @return bool
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * Set strict parsing
     *
     * @param bool $strict
     *
     * @return CsvReader
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;

        return $this;
    }

    /**
     * Read header row from CSV file
     *
     * @param int $rowNumber Row number
     *
     * @return array Column headers
     */
    protected function readHeaderRow($rowNumber)
    {
        $this->file->seek($rowNumber);
        $headers = $this->file->current();

        return $headers;
    }
}
