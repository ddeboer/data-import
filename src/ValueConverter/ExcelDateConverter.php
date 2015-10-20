<?php

namespace Ddeboer\DataImport\ValueConverter;

/**
 * Class ExcelDateConverter
 * @package Ddeboer\DataImport\ValueConverter
 */
class ExcelDateConverter extends AbstractExcelDateTimeConverter
{
    protected $format = 'Y-m-d 00:00:00';
}
