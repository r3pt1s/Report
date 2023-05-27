<?php

namespace r3pt1s\reportsystem\provider;

use pocketmine\promise\Promise;
use r3pt1s\reportsystem\manager\Report;

interface Provider {

    public function submitReport(Report $report): void;

    public function removeReport(Report $report): void;

    public function getReport(string $target): Promise;

    public function getReports(): Promise;
}