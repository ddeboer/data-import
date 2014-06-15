<?php

namespace Ddeboer\DataImport\Writer;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

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
     * Doctrine object manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Fully qualified model name
     *
     * @var string
     */
    protected $objectName;

    /**
     * Doctrine object repository
     *
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * @var ClassMetadata
     */
    protected $objectMetadata;

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
     * Index field name.
     *
     * @var null|string
     */
    protected $index;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     * @param string        $objectName
     * @param string        $index         Index to find current entities by
     */
    public function __construct(ObjectManager $objectManager, $objectName, $index = null)
    {
        $this->objectManager = $objectManager;
        $this->objectName = $objectName;
        $this->objectRepository = $objectManager->getRepository($objectName);
        $this->objectMetadata = $objectManager->getClassMetadata($objectName);
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

    protected function getNewInstance($className)
    {
        if (class_exists($className) === false) {
            throw new \Exception('Unable to create new instance of ' . $className);
        }

        return new $className;
    }

    protected function setValue($object, $value, $setter)
    {
        if (method_exists($object, $setter)) {
            $object->$setter($value);
        }
    }

    /**
     * Re-enable Doctrine logging
     *
     * @return DoctrineWriter
     */
    public function finish()
    {
        $this->objectManager->flush();
        $this->objectManager->clear();
        $this->reEnableLogging();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        $this->counter++;
        $object = null;

        // If the table was not truncated to begin with, find current entities
        // first
        if (false === $this->truncate) {
            if ($this->index) {
                $object = $this->objectRepository->findOneBy(
                    array($this->index => $item[$this->index])
                );
            } else {
                //TODO: it's better to set index field explicitly.
                $object = $this->objectRepository->find(current($item));
            }
        }

        if (!$object) {
            $className = $this->objectMetadata->getName();
            $object = $this->getNewInstance($className);
        }

        $fieldNames = array_merge($this->objectMetadata->getFieldNames(), $this->objectMetadata->getAssociationNames());
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
                || $value != $this->objectMetadata->getFieldValue($object, $fieldName)
            ) {
                $setter = 'set' . ucfirst($fieldName);
                $this->setValue($object, $value, $setter);
            }
        }

        $this->objectManager->persist($object);

        if (($this->counter % $this->batchSize) == 0) {
            $this->objectManager->flush();
            $this->objectManager->clear();
        }

        return $this;
    }

    /**
     * Truncate the database table for this writer
     *
     */
    protected function truncateTable()
    {
        if ($this->objectManager instanceof \Doctrine\ORM\EntityManager) {
            $tableName = $this->objectMetadata->table['name'];
            $connection = $this->objectManager->getConnection();
            $query = $connection->getDatabasePlatform()->getTruncateTableSQL($tableName);
            $connection->executeQuery($query);
        } elseif ($this->objectManager instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
            $this->objectManager->getDocumentCollection($this->objectName)->remove(array());
        } else {
            throw new UnsupportedDatabaseTypeException();
        }
    }

    /**
     * Disable Doctrine logging
     */
    protected function disableLogging()
    {
        //TODO: add support for MongoDB logging
        if (!($this->objectManager instanceof \Doctrine\ORM\EntityManager)) return;

        $config = $this->objectManager->getConnection()->getConfiguration();
        $this->originalLogger = $config->getSQLLogger();
        $config->setSQLLogger(null);
    }

    /**
     * Re-enable Doctrine logging
     */
    protected function reEnableLogging()
    {
        //TODO: add support for MongoDB logging
        if (!($this->objectManager instanceof \Doctrine\ORM\EntityManager)) return;

        $config = $this->objectManager->getConnection()->getConfiguration();
        $config->setSQLLogger($this->originalLogger);
    }
}
