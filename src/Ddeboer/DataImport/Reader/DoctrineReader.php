<?php

namespace Ddeboer\DataImport\Reader;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Query;

class DoctrineReader implements ReaderInterface
{
    protected $objectManager;
    protected $objectName;

    /**
     * @var IterableResult
     */
    protected $iterableResult;

    public function __construct(ObjectManager $objectManager, $objectName)
    {
        $this->objectManager = $objectManager;
        $this->objectName = $objectName;

        $query = $this->objectManager->createQuery(
            sprintf('select o from %s o', $objectName)
        );
        $this->iterableResult = $query->iterate(array(), Query::HYDRATE_ARRAY);
    }

    public function getFields()
    {
        return $this->objectManager->getClassMetadata($this->objectName)
                 ->getFieldNames();
    }

    public function current()
    {
        return current($this->iterableResult->current());
    }

    public function next()
    {
        $this->iterableResult->next();
    }

    public function key()
    {
        return $this->iterableResult->key();
    }

    public function valid()
    {
        return $this->iterableResult->valid();
    }

    public function rewind()
    {
        $this->iterableResult->rewind();
    }
}