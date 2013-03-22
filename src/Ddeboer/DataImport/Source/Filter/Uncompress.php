<?php

namespace Ddeboer\DataImport\Source\Filter;

/**
 * Provide uncompression for LZW-compressed files (.Z files)
 *
 * .Z files cannot be uncompressed using native PHP tools, so we’ll have to
 * resort to the Linux command line. You’ll need to install the zcat binary.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class Uncompress implements SourceFilterInterface
{
    private $target;
    private $zcatBinaryPath = 'zcat';

    public function __construct($target = null)
    {
        $this->target = $target;
    }

    public function getZcatBinaryPath()
    {
        return $this->zcatBinaryPath;
    }

    public function setZcatBinaryPath($zcatBinaryPath)
    {
        $this->zcatBinaryPath = $zcatBinaryPath;
    }

    /**
     *
     * @param  \SplFileObject $file
     * @return \SplFileObject
     */
    public function filter(\SplFileObject $file)
    {
        $target = $this->target ? $this->target : tempnam(null, null);

        // Add -f flag to skip confirmation
        exec(sprintf('%s -f %s > %s 2>/dev/null',
                $this->getZcatBinaryPath(),
                escapeshellarg($file->getPathname()),
                escapeshellarg($target)),
            $output, $returnVar
        );

        if ($returnVar !== 0) {
            throw new \Exception('Error occurred: ' . implode(', ', $output));
        }

        return new \SplFileObject($target);
    }
}
