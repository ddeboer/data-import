<?php

namespace Ddeboer\DataImport\Step;


/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
interface StepInterface 
{
    function process(&$item);
} 