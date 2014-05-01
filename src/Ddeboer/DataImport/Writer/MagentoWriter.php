<?php
namespace Ddeboer\DataImport\Writer;

/**
 * Class MagentoWriter
 * @author Adam Paterson <hello@adampaterson.co.uk>
 * @package Ddeboer\DataImport\Writer
 */
class MagentoWriter extends AbstractWriter
{
    /**
     * @var
     * @author Adam Paterson <hello@adampaterson.co.uk>
     */
    private $model;

    /**
     * @param Mage_Core_Model_Abstract $model
     */
    public function __construct(Mage_Core_Model_Abstract $model)
    {
        $this->model;
    }

    /**
     * @param array $item
     */
    public function writeItem(array $item)
    {
        $this->model->setData($item)->save();
    }
}