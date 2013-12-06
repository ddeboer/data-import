<?php
namespace Ddeboer\DataImport\Reader;

use Doctrine\DBAL\Connection;

/**
 * Reads data through the Doctrine DBAL
 */
class DbalReader implements ReaderInterface
{
    /***
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $data;

    /**
     * @var \Doctrine\DBAL\Driver\Statement
     */
    private $stmt;

    /**
     * Constructor
     *
     * @param Connection $connection Database connection
     * @param string     $sql        SQL statement
     * @param array      $params     SQL statement parameters
     */
    public function __construct(Connection $connection, $sql, array $params = array())
    {
        $this->connection = $connection;
        $this->stmt = $this->connection->prepare($sql);

        foreach ($params as $key => $value) {
            $this->stmt->bindValue($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        $this->stmt->execute();

        return array_keys($this->stmt->fetch(\PDO::FETCH_ASSOC));
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
        $this->loadData();
        reset($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $this->loadData();

        return count($this->data);
    }

    /**
     * Load data if it hasn't been loaded yet
     */
    protected function loadData()
    {
        if (null === $this->data) {
            $this->stmt->execute();
            $this->data = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
}
