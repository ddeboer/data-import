<?php

namespace Ddeboer\DataImport\Tests\Fixtures;

class TestEntity
{
    private $firstProperty;

    private $secondProperty;

    public function getFirstProperty()
    {
        return $this->firstProperty;
    }

    public function setFirstProperty($firstProperty)
    {
        $this->firstProperty = $firstProperty;
    }

    public function getSecondProperty()
    {
        return $this->secondProperty;
    }

    public function setSecondProperty($secondProperty)
    {
        $this->secondProperty = $secondProperty;
    }
}
