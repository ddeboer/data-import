<?php

namespace Ddeboer\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StringToObjectConverter implements ValueConverterInterface
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var string
     */
    private $property;

    public function __construct(ObjectRepository $repository, $property)
    {
        $this->repository = $repository;
        $this->property = $property;
    }

    /**
     * {@inheritDoc}
     */
    public function convert($input)
    {
        $method = 'findOneBy'.ucfirst($this->property);
        return $this->repository->$method($input);
    }
}
