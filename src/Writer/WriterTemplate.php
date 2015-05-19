<?php

namespace Ddeboer\DataImport\Writer;

/**
 * This template can be overridden in concrete implementations
 *
 * @author David de Boer <david@ddeboer.nl>
 */
trait WriterTemplate
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function finish()
    {
        return $this;
    }
}
