<?php

namespace Ddeboer\DataImport\Filter;

use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Ddeboer\DataImport\Exception\ValidationException;

class ValidatorFilter implements FilterInterface
{
    private $validator;

    private $throwExceptions = false;

    private $line = 1;

    private $constraints = array();

    private $violations = array();

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function add($field, Constraint $constraint)
    {
        if (!isset($this->constraints[$field])) {
            $this->constraints[$field] = array();
        }

        $this->constraints[$field][] = $constraint;
    }

    public function throwExceptions($flag = true)
    {
        $this->throwExceptions = $flag;
    }

    public function getViolations()
    {
        return $this->violations;
    }

    public function filter(array $item)
    {
        $constraints = new Constraints\Collection($this->constraints);
        $list = $this->validator->validateValue($item, $constraints);

        if (count($list) > 0) {
            $this->violations[$this->line] = $list;

            if ($this->throwExceptions) {
                throw new ValidationException($list, $this->line);
            }
        }

        $this->line++;

        return 0 === count($list);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 256;
    }
}
