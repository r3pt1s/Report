<?php

namespace r3pt1s\reportsystem\manager;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use r3pt1s\reportsystem\config\MainConfig;

class Report {

    public function __construct(
        private string $target,
        private string $player,
        private string $reason
    ) {}

    public function getTarget(): string {
        return $this->target;
    }

    public function getPlayer(): string {
        return $this->player;
    }

    public function getReason(): string {
        return $this->reason;
    }

    public function getReasonDisplay(): string {
        return MainConfig::getInstance()->getReasons()[$this->reason]["display_name"];
    }

    #[ArrayShape(["target" => "string", "player" => "string", "reason" => "string"])] public function toArray(): array {
        return [
            "target" => $this->target,
            "player" => $this->player,
            "reason" => $this->reason
        ];
    }

    #[Pure] public static function fromArray(array $data): ?self {
        if (isset($data["target"]) && isset($data["player"]) && isset($data["reason"])) {
            return new self($data["target"], $data["player"], $data["reason"]);
        }
        return null;
    }
}