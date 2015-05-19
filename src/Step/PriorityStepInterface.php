<?php

namespace Ddeboer\DataImport\Step;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
interface PriorityStepInterface extends StepInterface
{
    /**
     * @return integer
     */
    public function getPriority();
}
