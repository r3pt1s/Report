<?php

namespace r3pt1s\report\form;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use r3pt1s\report\config\MainConfig;
use r3pt1s\report\util\Utils;

class ReportForm extends CustomForm {

    public function __construct(Player $player) {
        $players = array_map(fn(Player $player) => $player->getName(), array_filter(array_values(Server::getInstance()->getOnlinePlayers()), fn(Player $online) => $online !== $player));
        sort($players);
        $reasons = array_map(fn(array $data) => $data["display_name"], array_values(MainConfig::getInstance()->getReasons()));
        sort($reasons);
        parent::__construct(
            Utils::parseMessage("form.report.title"),
            [
                new Dropdown("player", Utils::parseMessage("form.report.player.text"), $players),
                new Dropdown("reason", Utils::parseMessage("form.report.reason.text"), $reasons)
            ],
            function(Player $player, CustomFormResponse $response) use($players, $reasons): void {
                $player->chat("/report " . $players[$response->getInt("player")] . " " . TextFormat::clean($reasons[$response->getInt("reason")], true));
            }
        );
    }
}