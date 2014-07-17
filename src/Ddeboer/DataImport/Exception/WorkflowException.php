<?php

namespace Ddeboer\DataImport\Exception;
use Exception;

/**
 * WorkflowException
 *
 * @package Ddeboer\DataImport
 * @author  Guillaume Petit <guillaume.petit@sword-group.com>
 */
class WorkflowException extends \Exception implements ExceptionInterface
{
    /**
     * @var integer
     */
    private $itemIndex;

    /**
     * Constructor
     *
     * @param string    $itemIndex index of the item that failed
     * @param string    $message   [optional] The Exception message to throw.
     * @param int       $code      [optional] The Exception code.
     * @param Exception $previous  [optional] The previous exception used for the exception chaining. Since 5.3.0
     */
    public function __construct($itemIndex, $message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->itemIndex = $itemIndex;
    }


}
