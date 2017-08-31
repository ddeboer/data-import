<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 29/07/15
 * Time: 3:20 PM
 */

namespace Ddeboer\DataImport\Tests\Fixtures\Entity;


class TestEntityAssociation
{
    private $aProperty;

    private $bProperty;

    public function getAProperty()
    {
        return $this->aProperty;
    }

    public function setAProperty($firstProperty)
    {
        $this->aProperty = $firstProperty;
    }

    public function getBProperty()
    {
        return $this->bProperty;
    }

    public function setBProperty($secondProperty)
    {
        $this->bProperty = $secondProperty;
    }
}