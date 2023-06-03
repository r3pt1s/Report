<?php

namespace r3pt1s\report\command;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use r3pt1s\report\form\ReportsForm;
use r3pt1s\report\manager\ReportManager;
use r3pt1s\report\ReportSystem;
use r3pt1s\report\util\Utils;

class ReportsCommand extends Command implements PluginOwned {

    public function __construct() {
        parent::__construct("reports", Utils::parseMessage("command.reports.description"), "/reports", []);
        $this->setPermission("report.command.reports");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            if ($this->testPermissionSilent($sender)) {
                if (count(ReportManager::getInstance()->getReports()) > 0) {
                    $sender->sendForm(new ReportsForm());
                } else {
                    $sender->sendMessage(Utils::parseMessage("no.reports.available"));
                }
            } else {
                $sender->sendMessage(Utils::parseMessage("no.permissions"));
            }
        }
        return true;
    }

    #[Pure] public function getOwningPlugin(): ReportSystem {
        return ReportSystem::getInstance();
    }
}