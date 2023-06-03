<?php

namespace r3pt1s\report\event;

use pocketmine\player\Player;
use r3pt1s\report\manager\Report;

class ReportAcceptEvent extends ReportEvent {

    public function __construct(
        private Player $moderator,
        Report $report
    ) {
        parent::__construct($report);
    }

    public function getModerator(): Player {
        return $this->moderator;
    }
}