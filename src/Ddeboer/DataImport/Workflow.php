<?php

namespace Ddeboer\DataImport;

use Ddeboer\DataImport\Reader\ReaderInterface;
use Ddeboer\DataImport\Writer\WriterInterface;
use Ddeboer\DataImport\Filter\FilterInterface;
use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

/**
 * A mediator between a reader and one or more writers and converters
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class Workflow
{
    /**
     * Reader
     *
     * @var Reader
     */
    protected $reader;

    /**
     * Array of writers
     *
     * @var Writer[]
     */
    protected $writers = array();

    /**
     * Array of value converters
     *
     * @var ValueConverter[]
     */
    protected $valueConverters = array();

    /**
     * Array of item converters
     *
     * @var ValueConverter[]
     */
    protected $itemConverters = array();

    /**
     * Array of filters
     *
     * @var Filter[]
     */
    protected $filters = array();

    /**
     * Array of filters that will be checked after data conversion
     *
     * @var Filter[]
     */
    protected $afterConversionFilters = array();

    /**
     * Array of mappings
     *
     * @var array
     */
    protected $mappings = array();

    /**
     * Construct a workflow
     *
     * @param Reader $reader
     */
    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Add a filter to the workflow
     *
     * A filter decides whether an item is accepted into the import process.
     *
     * @param FilterInterface $filter
     *
     * @return Workflow
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Add after conversion filter
     *
     * @param FilterInterface $filter
     *
     * @return $this
     */
    public function addFilterAfterConversion(FilterInterface $filter)
    {
        $this->afterConversionFilters[] = $filter;

        return $this;
    }

    /**
     * Add a writer to the workflow
     *
     * A writer takes a filtered and converted item, and writes that to, e.g.,
     * a database or CSV file.
     *
     * @param WriterInterface $writer
     *
     * @return $this
     */
    public function addWriter(WriterInterface $writer)
    {
        $this->writers[] = $writer;

        return $this;
    }

    /**
     * Add a value converter to the workflow
     *
     * @param string                  $field     Field
     * @param ValueConverterInterface $converter ValueConverter
     *
     * @return $this
     */
    public function addValueConverter($field, ValueConverterInterface $converter)
    {
        $this->valueConverters[$field][] = $converter;

        return $this;
    }

    /**
     * Add an item converter to the workflow
     *
     * @param ItemConverterInterface $converter Item converter
     *
     * @return $this
     */
    public function addItemConverter(ItemConverterInterface $converter)
    {
        $this->itemConverters[] = $converter;

        return $this;
    }

    /**
     * Add a mapping to the workflow
     *
     * If we can get the field names from the reader, they are just to check the
     * $fromField against.
     *
     * @param string $fromField Field to map from
     * @param string $toField   Field to map to
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addMapping($fromField, $toField)
    {
        if (count($this->reader->getFields()) > 0) {
            if (!in_array($fromField, $this->reader->getFields())) {
                throw new \InvalidArgumentException("$fromField is an invalid field");
            }
        }

        $this->mappings[$fromField] = $toField;

        return $this;
    }

    /**
     * Process the whole import workflow
     *
     * 1. Prepare the added writers.
     * 2. Ask the reader for one item at a time.
     * 3. Filter each item.
     * 4. If the filter succeeds, convert the item’s values using the added
     *    converters.
     * 5. Write the item to each of the writers.
     *
     * @return int Number of items processed
     */
    public function process()
    {
        $count = 0;

        // Prepare writers
        foreach ($this->writers as $writer) {
            $writer->prepare();
        }

        // Read all items
        foreach ($this->reader as $item) {

            // Apply filters before conversion
            if (!$this->filterItem($item, $this->filters)) {
                continue;
            }

            $convertedItem = $this->convertItem($item);
            if (!$convertedItem) {
                continue;
            }

            // Apply filters after conversion
            if (!$this->filterItem($convertedItem, $this->afterConversionFilters)) {
                continue;
            }

            $mappedItem = $this->mapItem($convertedItem);

            foreach ($this->writers as $writer) {
                $writer->writeItem($mappedItem, $item);
            }

            $count++;
        }

        // Finish writers
        foreach ($this->writers as $writer) {
            $writer->finish();
        }

        return $count;
    }

    /**
     * Apply the filter chain to the input; if at least one filter fails, the
     * chain fails
     *
     * @param array $item    Item
     * @param array $filters Array of filters
     *
     * @return boolean
     */
    protected function filterItem(array $item, array $filters)
    {
        foreach ($filters as $filter) {
            if (false == $filter->filter($item)) {
                return false;
            }
        }

        // Return true if no filters failed
        return true;
    }

    /**
     * Convert the item
     *
     * @param string $item Original item values
     *
     * @return array Converted item values
     */
    protected function convertItem(array $item)
    {
        foreach ($this->itemConverters as $converter) {
            $item = $converter->convert($item);
            if (!$item) {
                return $item;
            }
        }
        foreach ($this->valueConverters as $property => $converters) {
            if (isset($item[$property])) {
                foreach ($converters as $converter) {
                    $item[$property] = $converter->convert($item[$property]);
                }
            }
        }

        return $item;
    }

    /**
     * Map an item
     *
     * @param array $item Item values
     *
     * @return array
     */
    protected function mapItem(array $item)
    {
        foreach ($item as $key => $value) {
            if (isset($this->mappings[$key])) {
                $toField = $this->mappings[$key];

                // Skip mappings where field to map from and field to map to
                // are equal. This may not make sense as a mapping, but it can
                // be the result of mappings that are generated automatically.
                if ($toField != $key) {
                    $item[$this->mappings[$key]] = $value;
                    unset($item[$key]);
                }
            }
        }

        return $item;
    }
}
