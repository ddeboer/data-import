<?php

namespace Ddeboer\DataImport\Writer;

/**
 * Writes using a callback or closure
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class CallbackWriter extends AbstractWriter
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * Constructor
     *
     * @param callable $callback
     * @throws \RuntimeException
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new \RuntimeException('$callback must be callable');
        }

        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function writeItem(array $item)
    {
        call_user_func($this->callback, $item);

        return $this;
    }
}
