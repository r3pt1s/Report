<?php

namespace r3pt1s\reportsystem\manager;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use r3pt1s\reportsystem\config\MainConfig;
use r3pt1s\reportsystem\event\PlayerReportEvent;
use r3pt1s\reportsystem\event\ReportAcceptEvent;
use r3pt1s\reportsystem\event\ReportDeclineEvent;
use r3pt1s\reportsystem\ReportSystem;
use r3pt1s\reportsystem\util\Utils;

final class ReportManager {
    use SingletonTrait;

    /** @var array<Report> */
    private array $reports = [];

    public function __construct() {
        self::setInstance($this);

        ReportSystem::getInstance()->getProvider()->getReports()->onCompletion(function(array $reports): void {
            $this->reports = $reports;
        }, function(): void {
            ReportSystem::getInstance()->getLogger()->warning("§cFailed to fetch reports");
        });
    }

    public function addReport(Report $report) {
        $this->reports[] = $report;
        ReportSystem::getInstance()->getProvider()->submitReport($report);
        (new PlayerReportEvent($report))->call();
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            if ($player->hasPermission("reportsystem.notify")) {
                $player->sendMessage(Utils::parseMessage("notify.message", [
                    "%target%" => $report->getTarget(),
                    "%player%" => $report->getPlayer(),
                    "%reason%" => $report->getReasonDisplay()
                ]));
            }
        }

        Utils::sendWebhookMessage(["embeds" => [[
            "color" => 0xFF0000,
            "title" => Utils::parseMessage("webhook.new.embed.title"),
            "fields" => [
                [
                    "name" => Utils::parseMessage("webhook.new.field.target.name"),
                    "value" => Utils::parseMessage("webhook.new.field.target.value", [
                        "%target%" => $report->getTarget()
                    ]),
                    "inline" => true
                ],
                [
                    "name" => Utils::parseMessage("webhook.new.field.player.name"),
                    "value" => Utils::parseMessage("webhook.new.field.player.value", [
                        "%player%" => $report->getPlayer()
                    ]),
                    "inline" => true
                ],
                [
                    "name" => Utils::parseMessage("webhook.new.field.reason.name"),
                    "value" => Utils::parseMessage("webhook.new.field.reason.value", [
                        "%reason%" => TextFormat::clean($report->getReasonDisplay(), true)
                    ]),
                    "inline" => true
                ]
            ]
        ]]]);
    }

    public function removeReport(Report $report, Player $moderator, bool $accept, string $additionalNotes = "") {
        if (in_array($report, $this->reports)) {
            unset($this->reports[array_search($report, $this->reports)]);
            ReportSystem::getInstance()->getProvider()->removeReport($report);
        }

        if ($accept) {
            (new ReportAcceptEvent($moderator, $report))->call();
            if (isset(MainConfig::getInstance()->getReasons()[$report->getReason()]) && isset(MainConfig::getInstance()->getReasons()[$report->getReason()]["command"]) && trim(MainConfig::getInstance()->getReasons()[$report->getReason()]["command"]) !== "") {
                Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), MainConfig::getInstance()->getReasons()[$report->getReason()]["command"]);
            }

            if (($player = Server::getInstance()->getPlayerExact($report->getPlayer())) !== null) {
                if (trim($additionalNotes) == "") {
                    $player->sendMessage(Utils::parseMessage("report.accepted", [
                        "%target%" => $report->getTarget()
                    ]));
                } else {
                    $player->sendMessage(Utils::parseMessage("report.accepted.with_notes", [
                        "%target%" => $report->getTarget(),
                        "%notes%" => trim($additionalNotes)
                    ]));
                }
            }

            Utils::sendWebhookMessage(["embeds" => [[
                "color" => 0x00FF00,
                "title" => Utils::parseMessage("webhook.accepted.embed.title"),
                "fields" => [
                    [
                        "name" => Utils::parseMessage("webhook.accepted.field.target.name"),
                        "value" => Utils::parseMessage("webhook.accepted.field.target.value", [
                            "%target%" => $report->getTarget()
                        ]),
                        "inline" => true
                    ],
                    [
                        "name" => Utils::parseMessage("webhook.accepted.field.player.name"),
                        "value" => Utils::parseMessage("webhook.accepted.field.player.value", [
                            "%player%" => $report->getPlayer()
                        ]),
                        "inline" => true
                    ],
                    [
                        "name" => Utils::parseMessage("webhook.accepted.field.moderator.name"),
                        "value" => Utils::parseMessage("webhook.accepted.field.moderator.value", [
                            "%moderator%" => $moderator->getName()
                        ]),
                        "inline" => true
                    ],
                    [
                        "name" => Utils::parseMessage("webhook.accepted.field.notes.name"),
                        "value" => Utils::parseMessage("webhook.accepted.field.notes.value", [
                            "%notes%" => trim($additionalNotes) == "" ? Utils::parseMessage("webhook.accepted.field.notes.value.empty") : $additionalNotes
                        ]),
                        "inline" => true
                    ]
                ]
            ]]]);
        } else {
            (new ReportDeclineEvent($moderator, $report))->call();
            if (($player = Server::getInstance()->getPlayerExact($report->getPlayer())) !== null) {
                if (trim($additionalNotes) == "") {
                    $player->sendMessage(Utils::parseMessage("report.declined", [
                        "%target%" => $report->getTarget()
                    ]));
                } else {
                    $player->sendMessage(Utils::parseMessage("report.declined.with_notes", [
                        "%target%" => $report->getTarget(),
                        "%notes%" => trim($additionalNotes)
                    ]));
                }
            }

            Utils::sendWebhookMessage(["embeds" => [[
                "color" => 0xFF0000,
                "title" => Utils::parseMessage("webhook.declined.embed.title"),
                "fields" => [
                    [
                        "name" => Utils::parseMessage("webhook.declined.field.target.name"),
                        "value" => Utils::parseMessage("webhook.declined.field.target.value", [
                            "%target%" => $report->getTarget()
                        ]),
                        "inline" => true
                    ],
                    [
                        "name" => Utils::parseMessage("webhook.declined.field.player.name"),
                        "value" => Utils::parseMessage("webhook.declined.field.player.value", [
                            "%player%" => $report->getPlayer()
                        ]),
                        "inline" => true
                    ],
                    [
                        "name" => Utils::parseMessage("webhook.declined.field.moderator.name"),
                        "value" => Utils::parseMessage("webhook.declined.field.moderator.value", [
                            "%moderator%" => $moderator->getName()
                        ]),
                        "inline" => true
                    ],
                    [
                        "name" => Utils::parseMessage("webhook.declined.field.notes.name"),
                        "value" => Utils::parseMessage("webhook.declined.field.notes.value", [
                            "%notes%" => trim($additionalNotes) == "" ? Utils::parseMessage("webhook.declined.field.notes.value.empty") : $additionalNotes
                        ]),
                        "inline" => true
                    ]
                ]
            ]]]);
        }
    }

    public function gotReported(Player|string $target): bool {
        $target = $target instanceof Player ? $target->getName() : $target;
        return count(array_filter($this->reports, fn(Report $report) => $report->getTarget() == $target)) > 0;
    }

    public function getReport(string $target): ?Report {
        foreach ($this->reports as $report) {
            if ($report->getTarget() == $target) return $report;
        }
        return null;
    }

    public function getReports(): array {
        return $this->reports;
    }

    public static function getInstance(): ?self {
        return self::$instance ?? null;
    }
}