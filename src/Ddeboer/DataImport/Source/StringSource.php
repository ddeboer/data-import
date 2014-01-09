<?php

namespace Ddeboer\DataImport\Source;
use Ddeboer\DataImport\Util\TempFile;

/**
 * Source that holds data as a string
 */
class StringSource extends TempFile
{
    /**
     * Constructor
     * 
     * @param string $data Data as string
     */
    public function __construct($data)
    {
        parent::__construct();

        $this->fwrite($data);
        $this->fseek(0);
    }
}
