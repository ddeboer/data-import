<?php

namespace Ddeboer\DataImport\Step;

use Ddeboer\DataImport\Exception\ValidationException;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ValidatorStep implements PriorityStep
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
     * @var ValidatorInterface
     */
    private $validator;

    private $possibleOptions = [ 'groups', 'allowExtraFields', 'allowMissingFields', 'extraFieldsMessage', 'missingFieldsMessage' ];

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
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
            $this->constraints['fields'][$field] = [];
        }

        $this->constraints['fields'][$field][] = $constraint;

        return $this;
    }

    /**
     * @param boolean $flag
     */
    public function throwExceptions($flag = true)
    {
        $this->throwExceptions = $flag;
    }

    /**
     * @return array
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * Add additional options for the constraints
     * @param string $option
     * @param $optionValue
     */
    public function addOption($option, $optionValue)
    {
        if (!isset($this->possibleOptions[$option])) {
            return;
        }

        $this->constraints[$option] = $optionValue;
    }

    /**
     * {@inheritdoc}
     */
    public function process(&$item)
    {
        $constraints = new Constraints\Collection($this->constraints);
        $list = $this->validator->validate($item, $constraints);

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
