<?php

namespace r3pt1s\reportsystem\provider;

use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use pocketmine\utils\Config;
use r3pt1s\reportsystem\manager\Report;
use r3pt1s\reportsystem\ReportSystem;

final class YAMLProvider implements Provider {

    private Config $file;

    public function __construct() {
        $this->file = new Config(ReportSystem::getInstance()->getDataFolder() . "reports.yml", Config::YAML);
    }

    public function submitReport(Report $report): void {
        if (!$this->file->exists($report->getTarget())) {
            $this->file->set($report->getTarget(), $report->toArray());
            try {
                $this->file->save();
            } catch (\JsonException $e) {
                ReportSystem::getInstance()->getLogger()->logException($e);
            }
        }
    }

    public function removeReport(Report $report): void {
        if ($this->file->exists($report->getTarget())) {
            $this->file->remove($report->getTarget());
            try {
                $this->file->save();
            } catch (\JsonException $e) {
                ReportSystem::getInstance()->getLogger()->logException($e);
            }
        }
    }

    public function getReport(string $target): Promise {
        /** @var PromiseResolver<Report> */
        $resolver = new PromiseResolver();

        if ($this->file->exists($target) && ($report = Report::fromArray($this->file->get($target, []))) !== null) {
            $resolver->resolve($report);
        } else $resolver->reject();

        return $resolver->getPromise();
    }

    public function getReports(): Promise {
        $reports = [];
        /** @var PromiseResolver<array<Report>> */
        $resolver = new PromiseResolver();

        foreach ($this->file->getAll() as $data) {
            if (($report = Report::fromArray($data)) !== null) {
                $reports[] = $report;
            }
        }

        $resolver->resolve($reports);
        return $resolver->getPromise();
    }

    public function getFile(): ?Config {
        return $this->file;
    }
}