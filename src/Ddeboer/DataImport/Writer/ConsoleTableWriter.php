<?php

namespace Ddeboer\DataImport\Writer;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * @author Igor Mukhin <igor.mukhin@gmail.com>
 */
class ConsoleTableWriter implements WriterInterface
{
    private $output = null;
    private $table = null;

    /**
     * @param OutputInterface $output
     * @param Table $table
     */
    public function __construct(OutputInterface $output, Table $table) {
        $this->output = $output;
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare() {

    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item) {
        $this->table->addRow($item);
    }

    /**
     * {@inheritdoc}
     */
    public function finish() {
        $this->table->render();
    }

    /**
     * You can get Table object to apply extra
     */
    public function getTable()
    {
        return $this->table;
    }
}
