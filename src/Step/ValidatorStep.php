<?php

namespace Ddeboer\DataImport\Step;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;
use Ddeboer\DataImport\Exception\ValidationException;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ValidatorStep implements PriorityStepInterface
{
    private $constraints = [];

    private $violations = [];

    private $throwExceptions = false;

    private $line = 1;

    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function add($field, Constraint $constraint)
    {
        if (!isset($this->constraints[$field])) {
            $this->constraints[$field] = array();
        }

        $this->constraints[$field][] = $constraint;

        return $this;
    }

    public function throwExceptions($flag = true)
    {
        $this->throwExceptions = $flag;

        return $this;
    }

    public function getViolations()
    {
        return $this->violations;
    }

    public function process(&$item)
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

    public function getPriority()
    {
        return 128;
    }
}
