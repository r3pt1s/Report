<?php

namespace r3pt1s\report\provider;

use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use r3pt1s\report\manager\Report;
use r3pt1s\report\util\AsyncExecutor;
use r3pt1s\report\util\Database;

final class MySQLProvider implements Provider {

    public function __construct() {
        AsyncExecutor::execute(fn(Database $database) => $database->initializeTable());
    }

    public function submitReport(Report $report): void {
        $data = $report->toArray();
        AsyncExecutor::execute(function(Database $database) use($data): void {
            if (!$database->has("reports", ["target" => $data["target"]])) {
                $database->insert("reports", $data);
            }
        });
    }

    public function removeReport(Report $report): void {
        $data = $report->toArray();
        AsyncExecutor::execute(function(Database $database) use($data): void {
            if ($database->has("reports", ["target" => $data["target"]])) {
                $database->delete("reports", ["target" => $data["target"]]);
            }
        });
    }

    public function getReport(string $target): Promise {
        /** @var PromiseResolver<Report> */
        $resolver = new PromiseResolver();

        AsyncExecutor::execute(fn(Database $database) => $database->get("reports", ["target", "player", "reason"], ["target" => $target]), function(?array $data) use($resolver): void {
            if (!is_array($data)) {
                $resolver->reject();
                return;
            }

            if (($report = Report::fromArray($data)) !== null) {
                $resolver->resolve($report);
            } else $resolver->reject();
        });

        return $resolver->getPromise();
    }

    public function getReports(): Promise {
        /** @var PromiseResolver<array<Report>> */
        $resolver = new PromiseResolver();

        AsyncExecutor::execute(fn(Database $database) => $database->select("reports", ["target", "player", "reason"], "*"), function(?array $data) use($resolver): void {
            if (!is_array($data)) {
                $resolver->reject();
                return;
            }

            $reports = [];
            foreach ($data as $report) {
                if (($report = Report::fromArray($report)) !== null) {
                    $reports[] = $report;
                }
            }

            $resolver->resolve($reports);
        });

        return $resolver->getPromise();
    }
}