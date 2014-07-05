<?php

namespace Ddeboer\DataImport\Writer;

abstract class AbstractStreamWriter implements WriterInterface
{
    private $stream;
    private $closeStreamOnFinish = true;

    /** @var string */
    private $eol = "\n";

    /**
     * Constructor
     *
     * @param resource $stream
     */
    public function __construct($stream = null)
    {
        if (null !== $stream) {
            $this->setStream($stream);
        }
    }

    /**
     * Set Stream Resource
     *
     * @param $stream
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setStream($stream)
    {
        if (! is_resource($stream) || ! 'stream' == get_resource_type($stream)) {
            throw new \InvalidArgumentException(sprintf(
                'Expects argument to be a stream resource, got %s',
                is_resource($stream) ? get_resource_type($stream) : gettype($stream)
            ));
        }

        $this->stream = $stream;

        return $this;
    }

    /**
     * Get underlying stream resource
     *
     * @return resource
     */
    public function getStream()
    {
        if (null === $this->stream) {
            $this->setStream(fopen('php://temp', 'r+'));
            $this->closeStreamOnFinish(false);
        }

        return $this->stream;
    }

    /**
     * Set End Of Line string
     * @param string $eol
     * @return $this
     */
    public function setEol($eol)
    {
        $this->eol = (string) $eol;

        return $this;
    }

    /**
     * Get End Of Line string
     *
     * @return string
     */
    public function getEol()
    {
        return $this->eol;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function finish()
    {
        if (is_resource($this->stream) && $this->closeStreamOnFinish()) {
            fclose($this->stream);
        }

        return $this;
    }

    /**
     * Should underlying stream be closed on finish?
     *
     * @param null|bool $closeStreamOnFinish
     *
     * @return bool
     */
    public function closeStreamOnFinish($closeStreamOnFinish = null)
    {
        if (null !== $closeStreamOnFinish) {
            $this->closeStreamOnFinish = (bool) $closeStreamOnFinish;
        }

        return $this->closeStreamOnFinish;
    }
}
