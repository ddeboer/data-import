<?php

namespace Ddeboer\DataImport\Exception;

/**
 * WorkflowException
 *
 * @author  Guillaume Petit <guillaume.petit@sword-group.com>
 * @package Ddeboer\DataImport
 */
class WorkflowException extends \Exception implements ExceptionInterface
{
    /**
     * @var integer
     */
    protected $itemIndex;
    
    /**
     * Get ItemIndex
     *
     * @return integer
     */
    public function getItemIndex()
    {
        return $this->itemIndex;
    }
    
    /**
     * Set ItemIndex
     *
     * @param integer $itemIndex the itemIndex
     *
     * @return void
     */
    public function setItemIndex($itemIndex)
    {
        $this->itemIndex = $itemIndex;
    }
    
}
