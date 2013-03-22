<?php

namespace Ddeboer\DataImport\Source\Filter;

/**
 * Convert encoding
 */
class ConvertEncoding implements SourceFilterInterface
{
    protected $toEncoding;
    protected $target;

    public function __construct($toEncoding = 'UTF-8', $target = null)
    {
        $this->toEncoding = $toEncoding;
        $this->target = $target;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(\SplFileObject $file)
    {
        $target = $this->target
            ? $this->target
            : tempnam(null, null) . '.' . $file->getExtension();

        $contents = \file_get_contents($file->getPathname());
        $converted = \mb_convert_encoding($contents, $this->toEncoding);
        file_put_contents($target, $converted);

        return new \SplFileObject($target);
    }
}
