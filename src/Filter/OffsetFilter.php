<?php

namespace Ddeboer\DataImport\Filter;
use Ddeboer\DataImport\Exception\StopException;

/**
 * This filter can be used to filter out some items from the beginning and/or
 * end of the items.
 *
 * @author Ville Mattila <ville@eventio.fi>
 */
class OffsetFilter
{
    /**
     * @var integer
     */
    protected $offset = 0;

    /**
     * @var integer|null
     */
    protected $limit = null;

    /**
     * @var integer
     */
    protected $offsetCount = 0;

    /**
     * @var integer
     */
    protected $sliceCount = 0;

    /**
     * @var boolean
     */
    protected $maxLimitHit = false;

    /**
     * @var boolean
     */
    protected $stopOnMaxLimit = false;

    /**
     * @param integer      $offset 0-based index of the item to start read from
     * @param integer|null $limit  Maximum count of items to read. null = no limit
     */
    public function __construct($offset = 0, $limit = null, $stopOnLimit = false)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        $this->stopOnMaxLimit = ($limit>0 && $stopOnLimit);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $item)
    {
        // In case we've already filtered up to limited
        if ($this->maxLimitHit) {
            if($this->stopOnMaxLimit) {
                throw new StopException();
            }

            return false;
        }

        $this->offsetCount++;

        // We have not reached the start offset
        if ($this->offsetCount < $this->offset + 1) {
            return false;
        }

        // There is no maximum limit, so we'll return always true
        if (null === $this->limit) {
            return true;
        }

        $this->sliceCount++;

        if ($this->sliceCount < $this->limit) {
            return true;
        } elseif ($this->sliceCount == $this->limit) {
            $this->maxLimitHit = true;

            return true;
        }

        return false;
    }
}
