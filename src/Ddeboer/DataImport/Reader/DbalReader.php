<?php
namespace Ddeboer\DataImport\Reader;

use Doctrine\DBAL\Connection;

/**
 * Reads data through the Doctrine DBAL
 */
class DbalReader implements ReaderInterface
{
    protected $connection;
    protected $schemaManager;
    protected $table;
    protected $data;

    /**
     * Constructor
     *
     * @param Connection $connection Database connection
     * @param string     $table      Database table name
     */
    public function __construct(Connection $connection, $table)
    {
        $this->connection = $connection;
        $this->schemaManager = $connection->getSchemaManager();
        $this->table = $table;
    }

    protected function loadData()
    {
        $this->data = $this->connection->fetchAll('SELECT * FROM ' . $this->table);
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        $fields = array();

        foreach ($this->schemaManager->listTableColumns($this->table) as $column) {
            $fields[] = $column->getName();
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        $key = key($this->data);

        return ($key !== null && $key !== false);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->data);
    }
}
