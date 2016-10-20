<?php

namespace Ddeboer\DataImport\Tests\Step;

use Ddeboer\DataImport\Step\ValidatorStep;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidatorStepTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->validator = $this->getMock('Symfony\Component\Validator\Validator\ValidatorInterface');

        $this->step = new ValidatorStep($this->validator);
    }

    public function testProcess()
    {
        $data = ['title' => null];

        $this->step->add('title', $constraint = new Constraints\NotNull());

        $list = new ConstraintViolationList();
        $list->add($this->buildConstraintViolation());

        $this->validator->expects($this->once())
                        ->method('validate')
                        ->willReturn($list);

        $this->assertFalse($this->step->process($data));

        $this->assertEquals([1 => $list], $this->step->getViolations());
    }

    /**
     * @expectedException Ddeboer\DataImport\Exception\ValidationException
     */
    public function testProcessWithExceptions()
    {
        $data = ['title' => null];

        $this->step->add('title', $constraint = new Constraints\NotNull());
        $this->step->throwExceptions();

        $list = new ConstraintViolationList();
        $list->add($this->buildConstraintViolation());

        $this->validator->expects($this->once())
                        ->method('validate')
                        ->willReturn($list);

        $this->assertFalse($this->step->process($data));
    }

    public function testPriority()
    {
        $this->assertEquals(128, $this->step->getPriority());
    }

    private function buildConstraintViolation()
    {
        return $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolation')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
