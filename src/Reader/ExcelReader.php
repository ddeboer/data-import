<?php

namespace Ddeboer\DataImport\Reader;

/**
 * Reads Excel files with the help of PHPExcel
 *
 * PHPExcel must be installed.
 *
 * @author David de Boer <david@ddeboer.nl>
 *
 * @link http://phpexcel.codeplex.com/
 * @link https://github.com/logiQ/PHPExcel
 */
class ExcelReader implements CountableReader, \SeekableIterator
{
    /**
     * @var \PHPExcel_Worksheet
     */
    protected $worksheet;

    /**
     * @var string
     */
    protected $maxColumn;

    /**
     * @var int
     */
    protected $maxRow;

    /**
     * @var integer
     */
    protected $headerRowNumber;

    /**
     * @var integer
     */
    protected $pointer = 1;

    /**
     * @var array
     */
    protected $columnHeaders;

    /**
     * Total number of rows
     *
     * @var integer
     */
    protected $count;

    /**
     * @param \SplFileObject $file            Excel file
     * @param integer        $headerRowNumber Optional number of header row
     * @param integer        $activeSheet     Index of active sheet to read from
     * @param boolean        $readOnly        If set to false, the reader take care of the excel formatting (slow)
     */
    public function __construct(\SplFileObject $file, $headerRowNumber = null, $activeSheet = null, $readOnly = true)
    {
        $reader = \PHPExcel_IOFactory::createReaderForFile($file->getPathName());
        $reader->setReadDataOnly($readOnly);
        /** @var \PHPExcel $excel */
        $excel = $reader->load($file->getPathname());

        if (null !== $activeSheet) {
            $excel->setActiveSheetIndex($activeSheet);
        }

        $this->worksheet = $excel->getActiveSheet();
        $this->maxColumn = $this->worksheet->getHighestColumn();
        $this->maxRow    = $this->worksheet->getHighestRow();

        if (null !== $headerRowNumber) {
            $this->setHeaderRowNumber($headerRowNumber);
        }
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
        $row = current($this->worksheet->rangeToArray(sprintf('A%d:%s%d',$this->pointer,$this->maxColumn,$this->pointer)));

        // If the CSV has column headers, use them to construct an associative
        // array for the columns in this line
        if (!empty($this->columnHeaders)) {
            // Count the number of elements in both: they must be equal.
            // If not, ignore the row
            if (count($this->columnHeaders) == count($row)) {
                return array_combine(array_values($this->columnHeaders), $row);
            }
        } else {
            // Else just return the column values
            return $row;
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
     */
    public function setColumnHeaders(array $columnHeaders)
    {
        $this->columnHeaders = $columnHeaders;
    }

    /**
     * Rewind the file pointer
     *
     * If a header row has been set, the pointer is set just below the header
     * row. That way, when you iterate over the rows, that header row is
     * skipped.
     */
    public function rewind()
    {
        if (null === $this->headerRowNumber) {
            $this->pointer = 1;
        } else {
            $this->pointer = $this->headerRowNumber + 1;
        }
    }

    /**
     * Set header row number
     *
     * @param integer $rowNumber Number of the row that contains column header names
     */
    public function setHeaderRowNumber($rowNumber)
    {
        $rowNumber++;
        $this->headerRowNumber = $rowNumber;
        $res = $this->worksheet->rangeToArray(sprintf('A%d:%s%d',$rowNumber,$this->maxColumn,$rowNumber));
        $this->columnHeaders = current($res);
        $this->pointer = $rowNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return ($this->pointer < $this->maxRow);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($pointer)
    {
        $this->pointer = $pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $maxRow = $this->maxRow;
        if (null !== $this->headerRowNumber) {
            $maxRow -= $this->headerRowNumber;
        }

        return $maxRow;
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
     * @param integer $number
     *
     * @return array
     */
    public function getRow($number)
    {
        $this->seek($number);

        return $this->current();
    }
}
