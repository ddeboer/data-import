<?php

namespace Ddeboer\DataImport\Reader;

use Ddeboer\DataImport\Source\SourceInterface;

/**
 * Reads Excel files with the help of PHPExcel
 *
 * PHPExcel must be installed.
 *
 * @author David de Boer <david@ddeboer.nl>
 * @link   http://phpexcel.codeplex.com/
 * @link   https://github.com/logiQ/PHPExcel
 */
class ExcelReader implements ReaderInterface
{
    protected $worksheet;
    protected $headerRowNumber;
    protected $pointer = 0;

    /**
     * Total number of rows
     *
     * @var int
     */
    protected $count;

    /**
     * Construct CSV reader
     *
     * @param SourceInterface|\SplFileObject $source The source: can be either a source
     *                                      or file object
     * @param int $headerRowNumber          Optional number of header row
     */
    public function __construct($source, $headerRowNumber = null, $activeSheet = -1)
    {
        if ($source instanceof SourceInterface) {
            $source = $source->getFile();
        }

        $excel = \PHPExcel_IOFactory::load($source->getPathname());

        if ($activeSheet > -1) {
            $excel->setActiveSheetIndex($activeSheet);
        }

        $this->worksheet = $excel->getActiveSheet()->toArray();

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
        $row = $this->worksheet[$this->pointer];

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
     *
     * @return $this
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
        if (null === $this->headerRowNumber) {
            $this->pointer = 0;
        } else {
            $this->pointer = $this->headerRowNumber + 1;
        }
    }

    /**
     * Set header row number
     *
     * @param int $rowNumber Number of the row that contains column header names
     *
     * @return $this
     */
    public function setHeaderRowNumber($rowNumber)
    {
        $this->headerRowNumber = $rowNumber;
        $this->columnHeaders = $this->worksheet[$rowNumber];

        return $this;
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
         return isset($this->worksheet[$this->pointer]);
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
        $count = count($this->worksheet);
        if (null !== $this->headerRowNumber) {
            $count--;
        }

        return $count;
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
}
