<?php

namespace Ddeboer\DataImport\Source;

use Ddeboer\DataImport\Util\TempFile;

/**
 * Source that holds data as a string
 */
class StringSource implements SourceInterface
{
    /**
     * Data
     * 
     * @var string
     */
    protected $data;
    
    /**
     * Constructor
     * 
     * @param string $data Data as string
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getFile()
    {
        $file = new TempFile();
        $file->fwrite($this->data);
        $file->fseek(0);
        
        return $file;
    }
}
