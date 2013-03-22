<?php

namespace Ddeboer\DataImport\Source;

interface SourceInterface
{
    /**
     * @return \SplFileObject
     */
    public function getFile();
}
