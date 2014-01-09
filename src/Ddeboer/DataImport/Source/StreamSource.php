<?php

namespace Ddeboer\DataImport\Source;

use Ddeboer\DataImport\Exception\SourceNotFoundException;
use Ddeboer\DataImport\Util\TempFile;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StreamSource extends TempFile
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var \resource
     */
    protected $context;

    /**
     * Constructor
     *
     * @param string    $path    Stream path
     * @param \resource $context Stream context
     */
    public function __construct($path, $context = null)
    {
        parent::__construct();

        $this->path = $path;
        $this->setContext($context);
    }

    /**
     * Set stream context
     *
     * @param \resource $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * Get stream context
     *
     * @return \resource
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritDoc}
     */
    public function doLazyLoad()
    {
        $contents = @file_get_contents($this->path, null, $this->context);
        if (false === $contents) {
            throw new SourceNotFoundException(sprintf('Invalid path: "%s"', $this->path));
        }

        $this->fwrite($contents);
    }
}
