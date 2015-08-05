<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 29/07/15
 * Time: 12:09 PM
 */

namespace Ddeboer\DataImport;


interface ReporterInterface
{
    const INFO    = 0;
    const WARNING = 1;
    const ERROR   = 2;

    public function hasMessage();
    public function getMessage();
    public function getSeverity();
}