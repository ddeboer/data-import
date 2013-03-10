<?php
namespace Ddeboer\DataImport\Reader;

interface ReaderFactoryInterface
{
    /**
     * Get reader
     *
     * @return ReaderInterface
     */
    public function getReader();
}