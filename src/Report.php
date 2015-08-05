<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 29/07/15
 * Time: 10:08 AM
 */

namespace Ddeboer\DataImport;


class Report
{
    private $row;
    private $messages;

    /**
     * @param $row
     */
    public function __construct($row)
    {
        $this->row = $row;
    }

    /**
     * @return integer
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param integer $row
     * @return Report
     */
    public function setRow($row)
    {
        $this->row = $row;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessages($severity = null)
    {
        if($severity === null) {
            return $this->messages;
        }

        $messages = array();
        foreach($this->messages as $message) {
            if($message->getSeverity() == $severity) {
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * @param mixed $messages
     * @return Report
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @param ReportMessage $message
     */
    public function addMessage(ReportMessage $message)
    {
        $this->messages[] = $message;
    }
}