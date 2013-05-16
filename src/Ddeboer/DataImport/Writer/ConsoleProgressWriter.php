<?php

namespace Ddeboer\DataImport\Writer;

use Ddeboer\DataImport\Reader\ReaderInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

/**
 * Writes output to the Symfony2 console
 *
 */
class ConsoleProgressWriter extends AbstractWriter
{
    protected $output;
    protected $progress;
    protected $workflow;

    public function __construct(OutputInterface $output, ReaderInterface $reader)
    {
        $this->output = $output;
        $this->progress = new ProgressHelper();
        $this->reader = $reader;
    }

    public function prepare()
    {
        $this->progress->start($this->output, $this->reader->count());
    }

    public function writeItem(array $item, array $originalItem = array())
    {
        $this->progress->advance();
    }

    public function finish()
    {
        $this->progress->finish();
    }
}
