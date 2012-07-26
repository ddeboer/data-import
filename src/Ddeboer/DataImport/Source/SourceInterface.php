<?php

namespace Ddeboer\DataImport\Source;

interface SourceInterface
{
    /**
     * @return \SplFileObject
     */
    function getFile();
}