<?php

namespace Ddeboer\DataImport\Source\Filter;

/**
 * Normalize line breaks other than LF (i.e., CRLF and CR) to LF
 */
class NormalizeLineBreaks implements SourceFilterInterface
{
    const CR = "\r";
    const LF = "\n";
    const CRLF = "\r\n";

    protected $target;

    public function __construct($target = null)
    {
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
        $normalized = $this->normalizeLineBreaks($contents);
        file_put_contents($target, $normalized);

        return new \SplFileObject($target);
    }

    protected function normalizeLineBreaks($string)
    {
        $string = \str_replace(self::CRLF, self::LF, $string);
        $string = \str_replace(self::CR, self::LF, $string);

        return $string;
    }
}