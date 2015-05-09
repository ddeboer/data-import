<?php

namespace Ddeboer\DataImport\Writer;

/**
 * Writes using a callback or closure
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class CallbackWriter implements WriterInterface
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        call_user_func($this->callback, $item);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function finish()
    {
        return $this;
    }
}
