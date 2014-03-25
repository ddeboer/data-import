<?php

namespace Ddeboer\DataImport\Writer;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * A bulk Doctrine writer
 *
 * See also the {@link http://www.doctrine-project.org/docs/orm/2.1/en/reference/batch-processing.html Doctrine documentation}
 * on batch processing.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class DoctrineWriter extends AbstractWriter
{
    /**
     * Doctrine entity manager
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Fully qualified entity name
     *
     * @var string
     */
    protected $entityName;

    /**
     * Doctrine entity repository
     *
     * @var EntityRepository
     */
    protected $entityRepository;

    /**
     * @var ClassMetadata
     */
    protected $entityMetadata;

    /**
     * Number of entities to be persisted per flush
     *
     * @var int
     */
    protected $batchSize = 20;

    /**
     * Counter for internal use
     *
     * @var int
     */
    protected $counter = 0;

    /**
     * Original Doctrine logger
     *
     * @var \Doctrine\DBAL\Logging\SQLLogger
     */
    protected $originalLogger;

    /**
     * Whether to truncate the table first
     *
     * @var boolean
     */
    protected $truncate = true;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param string        $entityName
     * @param string        $index         Index to find current entities by
     */
    public function __construct(EntityManager $entityManager, $entityName, $index = null)
    {
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;
        $this->entityRepository = $entityManager->getRepository($entityName);
        $this->entityMetadata = $entityManager->getClassMetadata($entityName);
        $this->index = $index;
    }

    public function getBatchSize()
    {
        return $this->batchSize;
    }

    /**
     * Set number of entities that may be persisted before a new flush
     *
     * @param  int            $batchSize
     * @return DoctrineWriter
     */
    public function setBatchSize($batchSize)
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    public function getTruncate()
    {
        return $this->truncate;
    }

    public function setTruncate($truncate)
    {
        $this->truncate = $truncate;

        return $this;
    }

    public function disableTruncate()
    {
        $this->truncate = false;

        return $this;
    }

    /**
     * Disable Doctrine logging
     *
     * @return DoctrineWriter
     */
    public function prepare()
    {
        $this->disableLogging();

        if (true === $this->truncate) {
            $this->truncateTable();
        }

        return $this;
    }

    protected function getNewInstance($className, array $item)
    {
        if (class_exists($className) === false) {
            throw new \Exception('Unable to create new instance of ' . $className);
        }

        return new $className;
    }

    protected function setValue($entity, $value, $setter)
    {
        if (method_exists($entity, $setter)) {
            $entity->$setter($value);
        }
    }

    /**
     * Re-enable Doctrine logging
     *
     * @return DoctrineWriter
     */
    public function finish()
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
        $this->reEnableLogging();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        $this->counter++;
        $entity = null;

        // If the table was not truncated to begin with, find current entities
        // first
        if (false === $this->truncate) {
            if ($this->index) {
                $entity = $this->entityRepository->findOneBy(
                    array($this->index => $item[$this->index])
                );
            } else {
                $entity = $this->entityRepository->find(current($item));
            }
        }

        if (!$entity) {
            $className = $this->entityMetadata->getName();
            $entity = $this->getNewInstance($className, $item);
        }

        $fieldNames = array_merge($this->entityMetadata->getFieldNames(), $this->entityMetadata->getAssociationNames());
        foreach ($fieldNames as $fieldName) {

            $value = null;
            if (isset($item[$fieldName])) {
                $value = $item[$fieldName];
            } elseif (method_exists($item, 'get' . ucfirst($fieldName))) {
                $value = $item->{'get' . ucfirst($fieldName)};
            }

            if (null === $value) {
                continue;
            }

            if (!($value instanceof \DateTime)
                || $value != $this->entityMetadata->getFieldValue($entity, $fieldName)
            ) {
                $setter = 'set' . ucfirst($fieldName);
                $this->setValue($entity, $value, $setter);
            }
        }

        $this->entityManager->persist($entity);

        if (($this->counter % $this->batchSize) == 0) {
            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        return $this;
    }

    /**
     * Truncate the database table for this writer
     *
     */
    protected function truncateTable()
    {
        $tableName = $this->entityMetadata->table['name'];
        $connection = $this->entityManager->getConnection();
        $query = $connection->getDatabasePlatform()->getTruncateTableSQL($tableName);
        $connection->executeQuery($query);
    }

    /**
     * Disable Doctrine logging
     */
    protected function disableLogging()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $this->originalLogger = $config->getSQLLogger();
        $config->setSQLLogger(null);
    }

    /**
     * Re-enable Doctrine logging
     */
    protected function reEnableLogging()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $config->setSQLLogger($this->originalLogger);
    }
}
