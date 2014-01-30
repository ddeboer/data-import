<?php

namespace Ddeboer\DataImport\Exception;

class DuplicateHeadersException extends ReaderException
{
    public function __construct(array $duplicates)
    {
        parent::__construct(sprintf('File contains duplicate headers: %s', implode($duplicates, ', ')));
    }
}
