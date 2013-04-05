<?php
namespace Ddeboer\DataImport\Source;

/**
 * Source that holds data as a string
 * 
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
     * {@inheritdoc}
     */
    public function getFile()
    {
        $file = new \SplTempFileObject();
        $file->fwrite($this->data);
        
        return $file;
    }
}
