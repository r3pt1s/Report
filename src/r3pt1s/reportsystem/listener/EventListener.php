<?php

namespace r3pt1s\reportsystem\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use r3pt1s\reportsystem\manager\ReportManager;
use r3pt1s\reportsystem\util\Utils;

class EventListener implements Listener {

    /** @priority MONITOR */
    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        if ($player->hasPermission("reportsystem.notify")) {
            if (count(ReportManager::getInstance()->getReports()) > 0) {
                $player->sendMessage(Utils::parseMessage("join.notification", [
                    "%count%" => count(ReportManager::getInstance()->getReports())
                ]));
            }
        }
    }
}