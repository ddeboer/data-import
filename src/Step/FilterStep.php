<?php

namespace Ddeboer\DataImport\Step;

use Ddeboer\DataImport\Report;
use Ddeboer\DataImport\Step;
use Ddeboer\DataImport\ReporterInterface;
use Ddeboer\DataImport\ReportMessage;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class FilterStep implements Step
{
    /**
     * @var \SplPriorityQueue
     */
    private $filters;

    public function __construct()
    {
        $this->filters = new \SplPriorityQueue();
    }

    /**
     * @param callable $filter
     * @param integer  $priority
     *
     * @return $this
     */
    public function add(callable $filter, $priority = null)
    {
        $this->filters->insert($filter, $priority);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function process(&$item, Report $report = null)
    {
        foreach (clone $this->filters as $filter) {
            if (false === call_user_func($filter, $item)) {
                if($report !== null && $filter instanceof ReporterInterface && $filter->hasMessage()) {
                    $report->addMessage(new ReportMessage($filter->getMessage(),$filter->getSeverity()));
                }

                return false;
            }
        }

        return true;
    }
}

