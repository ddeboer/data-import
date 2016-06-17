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
    private $severity;

    public function __construct($message, $severity, $column = null)
    {
        $this->message = $message;
        $this->severity = $severity;
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
     * @return mixed
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param integer $severity
     * @return ReportMessage
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
        return $this;
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