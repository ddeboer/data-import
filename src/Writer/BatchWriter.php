<?php

namespace Ddeboer\DataImport\Writer;


/**
 * @author Markus Bachmann <markus.bachmann@digital-connect.de>
 */
class BatchWriter implements WriterInterface
{
    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var \SplQueue
     */
    private $queue;

    /**
     * @var integer
     */
    private $size;

    public function __construct(WriterInterface $writer, $size = 20)
    {
        $this->writer = $writer;
        $this->size = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->writer->prepare();
        $this->queue = new \SplQueue();
        $this->queue->setIteratorMode(\SplQueue::IT_MODE_DELETE);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        $this->queue->enqueue($item);

        if (count($this->queue) >= $this->size) {
            $this->process();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function finish()
    {
        if (0 !== count($this->queue)) {
            $this->process();
        }

        $this->writer->finish();
    }

    private function process()
    {
        foreach ($this->queue as $item) {
            $this->writer->writeItem($item);
        }

        if ($this->writer instanceof FlushableWriter) {
            $this->writer->flush();
        }
    }
}