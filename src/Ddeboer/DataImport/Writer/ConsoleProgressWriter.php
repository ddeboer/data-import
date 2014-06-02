<?php

namespace Ddeboer\DataImport\Writer;

use Ddeboer\DataImport\Reader\ReaderInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Writes output to the Symfony2 console
 *
 */
class ConsoleProgressWriter extends AbstractWriter
{
    protected $output;
    protected $progress;
    protected $workflow;

    public function __construct(OutputInterface $output, ReaderInterface $reader, $verbosity = 'debug')
    {
        $this->output       = $output;
        $this->reader       = $reader;
        $this->verbosity    = $verbosity;
    }

    public function prepare()
    {
        $this->progress = new ProgressBar($this->output, $this->reader->count());
        $this->progress->setFormat($this->verbosity);
        $this->progress->start();
    }

    public function writeItem(array $item)
    {
        $this->progress->advance();
    }

    public function finish()
    {
        $this->progress->finish();
    }

    public function getVerbosity()
    {
        return $this->verbosity;
    }
}
