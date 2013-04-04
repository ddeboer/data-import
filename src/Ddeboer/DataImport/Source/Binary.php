<?php

namespace Ddeboer\DataImport\Source;

use Ddeboer\DataImport\Exception\SourceNotFoundException;

class Binary implements SourceInterface
{
    /**
     * @var string
     */
    private $tmp_file_path;

    /**
     * @var resource
     */
    private $handle;    

    /**
     * Constructor
     *
     * @param string $path
     */
    public function __construct( $binary_data )
    {
        $this->tmp_file_path = tempnam( null, null );
        $this->handle = fopen($this->tmp_file_path, "w");
        fwrite($this->handle, $binary_data);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        fclose($this->handle);
    }

    /**
     * {@inheritDoc}
     */
    public function getFile()
    {
        try {
            return new \SplFileObject( $this->tmp_file_path );
        } catch (\RuntimeException $e) {
            throw new SourceNotFoundException(sprintf('The path "%s" is invalid', $this->tmp_file_path), null, $e);
        }
    }
}