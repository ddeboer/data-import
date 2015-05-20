<?php


namespace Ddeboer\DataImport\Writer;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
interface FlushableWriter extends WriterInterface
{
    public function flush();
} 