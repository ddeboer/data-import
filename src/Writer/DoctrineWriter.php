<?php

namespace Ddeboer\DataImport\Writer;

use Ddeboer\DataImport\Writer;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Ddeboer\DataImport\Exception\UnsupportedDatabaseTypeException;

/**
 * A bulk Doctrine writer
 *
 * See also the {@link http://www.doctrine-project.org/docs/orm/2.1/en/reference/batch-processing.html Doctrine documentation}
 * on batch processing.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class DoctrineWriter implements Writer, FlushableWriter
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
     * Original Doctrine logger
     *
     * @var SQLLogger
     */
    protected $originalLogger;

    /**
     * Whether to truncate the table first
     *
     * @var boolean
     */
    protected $truncate = true;

    /**
     * List of fields used to lookup an object
     *
     * @var array
     */
    protected $lookupFields = array();

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     * @param string        $objectName
     * @param string|array        $index         Field or fields to find current entities by
     */
    public function __construct(ObjectManager $objectManager, $objectName, $index = null)
    {
        $this->objectManager = $objectManager;
        $this->objectRepository = $objectManager->getRepository($objectName);
        $this->objectMetadata = $objectManager->getClassMetadata($objectName);
        //translate objectName in case a namespace alias is used
        $this->objectName = $this->objectMetadata->getName();
        if($index) {
            if(is_array($index)) {
                $this->lookupFields = $index;
            } else {
                $this->lookupFields = [$index];
            }
        }
    }

    /**
     * @return boolean
     */
    public function getTruncate()
    {
        return $this->truncate;
    }

    /**
     * Set whether to truncate the table first
     *
     * @param boolean $truncate
     *
     * @return $this
     */
    public function setTruncate($truncate)
    {
        $this->truncate = $truncate;

        return $this;
    }

    /**
     * Disable truncation
     *
     * @return $this
     */
    public function disableTruncate()
    {
        $this->truncate = false;

        return $this;
    }

    /**
     * Disable Doctrine logging
     *
     * @return $this
     */
    public function prepare()
    {
        $this->disableLogging();

        if (true === $this->truncate) {
            $this->truncateTable();
        }
    }

    /**
     * Return a new instance of the object
     *
     * @return object
     */
    protected function getNewInstance()
    {
        $className = $this->objectMetadata->getName();

        if (class_exists($className) === false) {
            throw new \RuntimeException('Unable to create new instance of ' . $className);
        }

        return new $className;
    }

    /**
     * Call a setter of the object
     *
     * @param object $object
     * @param mixed  $value
     * @param string $setter
     */
    protected function setValue($object, $value, $setter)
    {
        if (method_exists($object, $setter)) {
            $object->$setter($value);
        }
    }

    /**
     * Re-enable Doctrine logging
     *
     * @return $this
     */
    public function finish()
    {
        $this->flush();
        $this->reEnableLogging();
    }

    /**
     * {@inheritdoc}
     */
    public function writeItem(array $item)
    {
        $object = $this->findOrCreateItem($item);

        $this->loadAssociationObjectsToObject($item, $object);
        $this->updateObject($item, $object);

        $this->objectManager->persist($object);
    }

    /**
     * @param array  $item
     * @param object $object
     */
    protected function updateObject(array $item, $object)
    {
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
    }

    /**
     * Add the associated objects in case the item have for persist its relation
     *
     * @param array  $item
     * @param object $object
     */
    protected function loadAssociationObjectsToObject(array $item, $object)
    {
        foreach ($this->objectMetadata->getAssociationMappings() as $associationMapping) {

            $value = null;
            if (isset($item[$associationMapping['fieldName']]) && !is_object($item[$associationMapping['fieldName']])) {
                $value = $this->objectManager->getReference($associationMapping['targetEntity'], $item[$associationMapping['fieldName']]);
            }

            if (null === $value) {
                continue;
            }

            $setter = 'set' . ucfirst($associationMapping['fieldName']);
            $this->setValue($object, $value, $setter);
        }
    }

    /**
     * Truncate the database table for this writer
     */
    protected function truncateTable()
    {
        if ($this->objectManager instanceof \Doctrine\ORM\objectManager) {
            $tableName = $this->objectMetadata->table['name'];
            $connection = $this->objectManager->getConnection();
            $query = $connection->getDatabasePlatform()->getTruncateTableSQL($tableName, true);
            $connection->executeQuery($query);
        } elseif ($this->objectManager instanceof \Doctrine\ODM\MongoDB\DocumentManager) {
            $this->objectManager->getDocumentCollection($this->objectName)->remove(array());
        } else {
            $message = sprintf(
                'Unknown Object Manager type. Expected \Doctrine\ORM\objectManager or \Doctrine\ODM\MongoDB\DocumentManager, %s given',
                get_class($this->objectManager)
            );
            throw new UnsupportedDatabaseTypeException($message);
        }
    }

    /**
     * Disable Doctrine logging
     */
    protected function disableLogging()
    {
        if (!($this->objectManager instanceof \Doctrine\ORM\objectManager)) return;

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
        if (!($this->objectManager instanceof \Doctrine\ORM\objectManager)) return;

        $config = $this->objectManager->getConnection()->getConfiguration();
        $config->setSQLLogger($this->originalLogger);
    }

    /**
     * Finds existing object or create a new instance
     *
     * @param array $item
     */
    protected function findOrCreateItem(array $item)
    {
        $object = null;
        // If the table was not truncated to begin with, find current object
        // first
        if (false === $this->truncate) {
            if (!empty($this->lookupFields)) {
                $lookupConditions = array();
                foreach ($this->lookupFields as $fieldName) {
                    $lookupConditions[$fieldName] = $item[$fieldName];
}
                $object = $this->objectRepository->findOneBy(
                    $lookupConditions
                );
            } else {
                $object = $this->objectRepository->find(current($item));
            }
        }

        if (!$object) {
            return $this->getNewInstance();
        }

        return $object;
    }

    /**
     * Flush and clear the object manager
     */
    public function flush()
    {
        $this->objectManager->flush();
        $this->objectManager->clear($this->objectName);
    }
}
