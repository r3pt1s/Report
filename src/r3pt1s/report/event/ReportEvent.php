<?php

namespace r3pt1s\report\event;

use pocketmine\event\Event;
use r3pt1s\report\manager\Report;

abstract class ReportEvent extends Event {

    public function __construct(
        private Report $report
    ) {}

    public function getReport(): Report {
        return $this->report;
    }
}