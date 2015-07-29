<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 29/07/15
 * Time: 12:01 PM
 */

namespace Ddeboer\DataImport;


class ReportMessage
{
    private $column;
    private $message;

    public function __construct($message, $column = null)
    {
        $this->message = $message;
        $this->column  = $column;
    }

    /**
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param mixed $column
     * @return ReportMessage
     */
    public function setColumn($column)
    {
        $this->column = $column;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return ReportMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isException()
    {
        return ($this->message instanceof Exception);
    }
}