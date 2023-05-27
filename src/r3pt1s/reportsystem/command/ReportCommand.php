<?php

namespace r3pt1s\reportsystem\command;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;
use r3pt1s\reportsystem\config\MainConfig;
use r3pt1s\reportsystem\form\ReportForm;
use r3pt1s\reportsystem\manager\Report;
use r3pt1s\reportsystem\manager\ReportManager;
use r3pt1s\reportsystem\ReportSystem;
use r3pt1s\reportsystem\util\Utils;

class ReportCommand extends Command implements PluginOwned {

    public function __construct() {
        parent::__construct("report", Utils::parseMessage("command.report.description"), "", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            if (isset($args[0]) && isset($args[1])) {
                if (Server::getInstance()->hasOfflinePlayerData($args[0])) {
                    if ($args[0] == $sender->getName()) {
                        $sender->sendMessage(Utils::parseMessage("player.report.failed"));
                        return true;
                    }

                    if (isset(MainConfig::getInstance()->getReasons()[strtolower($args[1])])) {
                        if (!ReportManager::getInstance()->gotReported($args[0])) {
                            $sender->sendMessage(Utils::parseMessage("player.reported", [
                                "%target%" => $args[0]
                            ]));
                            ReportManager::getInstance()->addReport(new Report($args[0], $sender->getName(), strtolower($args[1])));
                        } else {
                            $sender->sendMessage(Utils::parseMessage("player.already.reported", [
                                "%target%" => $args[0]
                            ]));
                        }
                    } else {
                        $sender->sendMessage(Utils::parseMessage("reason.not.found", [
                            "%reason%" => $args[1]
                        ]));
                    }
                } else {
                    $sender->sendMessage(Utils::parseMessage("player.not.found", [
                        "%target%" => $args[0]
                    ]));
                }
            } else {
                if (count(Server::getInstance()->getOnlinePlayers()) > 1) {
                    $sender->sendForm(new ReportForm($sender));
                } else {
                    $sender->sendMessage(Utils::parseMessage("no.player.online"));
                }
            }
        }
        return true;
    }

    #[Pure] public function getOwningPlugin(): ReportSystem {
        return ReportSystem::getInstance();
    }
}