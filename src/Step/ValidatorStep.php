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
    /**
     * @var array
     */
    private $constraints = [];

    /**
     * @var array
     */
    private $violations = [];

    /**
     * @var boolean
     */
    private $throwExceptions = false;

    /**
     * @var integer
     */
    private $line = 1;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string     $field
     * @param Constraint $constraint
     *
     * @return $this
     */
    public function add($field, Constraint $constraint)
    {
        if (!isset($this->constraints[$field])) {
            $this->constraints[$field] = array();
        }

        $this->constraints[$field][] = $constraint;

        return $this;
    }

    /**
     * @param boolean $flag
     *
     * @return $this
     */
    public function throwExceptions($flag = true)
    {
        $this->throwExceptions = $flag;

        return $this;
    }

    /**
     * @return array
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 128;
    }
}
