<?php

namespace r3pt1s\reportsystem\event;

use pocketmine\event\Event;
use r3pt1s\reportsystem\manager\Report;

abstract class ReportEvent extends Event {

    public function __construct(
        private Report $report
    ) {}

    public function getReport(): Report {
        return $this->report;
    }
}