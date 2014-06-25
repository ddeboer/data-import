<?php

namespace Ddeboer\DataImport\Reader;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Reads entities through the Doctrine ORM
 *
 */
class DoctrineReader implements ReaderInterface
{
    protected $objectManager;
    protected $objectName;
    protected $queryBuilder;

    /**
     * @var IterableResult
     */
    protected $iterableResult;

    /**
     * Constuctor
     *
     * @param ObjectManager $objectManager Doctrine object manager
     * @param string        $objectName    Doctrine object name, e.g.
     *                                     YourBundle:YourEntity
     * @param QueryBuilder  $queryBuilder  A custom query builder you may want to use
     *
     * @return void
     */
    public function __construct(ObjectManager $objectManager, $objectName, QueryBuilder $queryBuilder = null)
    {
        $this->objectManager = $objectManager;
        $this->objectName = $objectName;
        if ($queryBuilder == null) {
            $this->queryBuilder = $this->objectManager->createQueryBuilder();
            $this->queryBuilder->select('o')->from($this->objectName, 'o');
        } else {
            $this->queryBuilder = $queryBuilder;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getFields()
    {
        return $this->objectManager->getClassMetadata($this->objectName)
            ->getFieldNames();
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->iterableResult->current());
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function next()
    {
        $this->iterableResult->next();
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function key()
    {
        return $this->iterableResult->key();
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function valid()
    {
        return $this->iterableResult->valid();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function rewind()
    {
        if (!$this->iterableResult) {
            $query = $this->queryBuilder->getQuery();
            $this->iterableResult = $query->iterate(array(), Query::HYDRATE_ARRAY);
        }

        $this->iterableResult->rewind();
    }

    /**
     * {@inheritdoc}
     *
     * @return integer
     */
    public function count()
    {
        $query = $this->objectManager->createQuery(
            sprintf('select count(o) from %s o', $this->objectName)
        );

        return $query->getSingleScalarResult();
    }
}
