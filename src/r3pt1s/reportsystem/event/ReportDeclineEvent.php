<?php

namespace r3pt1s\reportsystem\event;

use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use r3pt1s\reportsystem\manager\Report;

class ReportDeclineEvent extends ReportEvent {

    #[Pure] public function __construct(
        private Player $moderator,
        Report $report
    ) {
        parent::__construct($report);
    }

    public function getModerator(): Player {
        return $this->moderator;
    }
}