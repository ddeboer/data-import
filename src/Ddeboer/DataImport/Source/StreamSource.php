<?php

namespace Ddeboer\DataImport\Source;

use Ddeboer\DataImport\Exception\SourceNotFoundException;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StreamSource implements SourceInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * Constructor
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function getFile()
    {
        try {
            return new \SplFileObject($this->path);
        } catch (\RuntimeException $e) {
            throw new SourceNotFoundException(sprintf('The path "%s" is invalid', $this->path), null, $e);
        }
    }
}
