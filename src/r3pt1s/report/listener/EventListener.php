<?php

namespace r3pt1s\report\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use r3pt1s\report\manager\ReportManager;
use r3pt1s\report\util\Utils;

class EventListener implements Listener {

    /** @priority MONITOR */
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        if ($player->hasPermission("report.notify")) {
            if (count(ReportManager::getInstance()->getReports()) > 0) {
                $player->sendMessage(Utils::parseMessage("join.notification", [
                    "%count%" => count(ReportManager::getInstance()->getReports())
                ]));
            }
        }
    }
}