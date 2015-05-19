<?php

namespace Ddeboer\DataImport;

use Ddeboer\DataImport\Exception\ExceptionInterface;
use Ddeboer\DataImport\Exception\UnexpectedTypeException;
use Ddeboer\DataImport\Reader\ReaderInterface;
use Ddeboer\DataImport\Step\PriorityStepInterface;
use Ddeboer\DataImport\Step\StepInterface;
use Ddeboer\DataImport\Writer\WriterInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * A mediator between a reader and one or more writers and converters
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class Workflow implements WorkflowInterface
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * Identifier for the Import/Export
     *
     * @var string|null
     */
    private $name = null;

    /**
     * @var boolean
     */
    private $skipItemOnFailure = false;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \SplPriorityQueue
     */
    private $steps;

    /**
     * @var WriterInterface[]
     */
    private $writers = [];

    /**
     * @var boolean
     */
    protected $shouldStop = false;

    /**
     * @param ReaderInterface $reader
     * @param LoggerInterface $logger
     * @param string          $name
     */
    public function __construct(ReaderInterface $reader, LoggerInterface $logger = null, $name = null)
    {
        $this->name = $name;
        $this->logger = $logger ?: new NullLogger();
        $this->reader = $reader;
        $this->steps = new \SplPriorityQueue();
    }

    /**
     * Add a step to the current workflow
     *
     * @param StepInterface $step
     * @param integer|null  $priority
     *
     * @return Workflow
     */
    public function addStep(StepInterface $step, $priority = null)
    {
        $priority = null === $priority && $step instanceof PriorityStepInterface ? $step->getPriority() : null;
        $priority = null === $priority ? 0 : $priority;

        $this->steps->insert($step, $priority);

        return $this;
    }

    /**
     * Add a new writer to the current workflow
     *
     * @param WriterInterface $writer
     *
     * @return Workflow
     */
    public function addWriter(WriterInterface $writer)
    {
        array_push($this->writers, $writer);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        $count      = 0;
        $exceptions = new \SplObjectStorage();
        $startTime  = new \DateTime;

        foreach ($this->writers as $writer) {
            $writer->prepare();
        }

        if (is_callable('pcntl_signal')) {
            pcntl_signal(SIGTERM, array($this, 'stop'));
            pcntl_signal(SIGINT, array($this, 'stop'));
        }

        // Read all items
        foreach ($this->reader as $index => $item) {

            if (is_callable('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

            if ($this->shouldStop) {
                break;
            }

            try {
                foreach (clone $this->steps as $step) {
                    if (false === $step->process($item)) {
                        continue 2;
                    }
                }

                if (!is_array($item) && !($item instanceof \ArrayAccess && $item instanceof \Traversable)) {
                    throw new UnexpectedTypeException($item, 'array');
                }

                foreach ($this->writers as $writer) {
                    $writer->writeItem($item);
                }
            } catch(ExceptionInterface $e) {
                if (!$this->skipItemOnFailure) {
                    throw $e;
                }

                $exceptions->attach($e, $index);
                $this->logger->error($e->getMessage());
            }

            $count++;
        }

        foreach ($this->writers as $writer) {
            $writer->finish();
        }

        return new Result($this->name, $startTime, new \DateTime, $count, $exceptions);
    }

    /**
     * Stops processing and force return Result from process() function
     */
    public function stop()
    {
        $this->shouldStop = true;
    }

    /**
     * Sets the value which determines whether the item should be skipped when error occures
     *
     * @param boolean $skipItemOnFailure When true skip current item on process exception and log the error
     *
     * @return $this
     */
    public function setSkipItemOnFailure($skipItemOnFailure)
    {
        $this->skipItemOnFailure = $skipItemOnFailure;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
