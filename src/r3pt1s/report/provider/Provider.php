<?php

namespace r3pt1s\report\provider;

use pocketmine\promise\Promise;
use r3pt1s\report\manager\Report;

interface Provider {

    public function submitReport(Report $report): void;

    public function removeReport(Report $report): void;

    public function getReport(string $target): Promise;

    public function getReports(): Promise;
}