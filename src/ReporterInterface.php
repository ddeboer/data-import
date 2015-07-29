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
    public function hasMessage();
    public function getMessage();
}