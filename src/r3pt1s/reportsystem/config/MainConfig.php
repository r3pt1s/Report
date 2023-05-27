<?php

namespace r3pt1s\reportsystem\config;

use r3pt1s\configlib\Configuration;
use r3pt1s\reportsystem\ReportSystem;

class MainConfig extends Configuration {

    public const PROVIDERS = ["json", "yml", "mysql"];

    /** @ignored */
    private static ?self $instance = null;
    private string $provider = "json";
    private array $mysql = [
        "host" => "localhost",
        "port" => 3306,
        "username" => "root",
        "password" => "your_password",
        "database" => "your_database"
    ];
    private array $webhook = [
        "enabled" => false,
        "webhook_url" => "your_url"
    ];
    private array $reasons = [
        "behavior" => [
            "command" => "/mute %user% Behavior",
            "display_name" => "§cBehavior"
        ],
        "spamming" => [
            "command" => "/mute %user% Spamming",
            "display_name" => "§cSpamming"
        ],
        "cheating" => [
            "command" => "/ban %user% Cheating",
            "display_name" => "§cCheating"
        ]
    ];

    public function __construct() {
        self::$instance = $this;
        parent::__construct(ReportSystem::getInstance()->getDataFolder() . "config.yml", self::TYPE_YAML);
        try {
            if (!$this->load()) $this->save();
        } catch (\JsonException $e) {
            ReportSystem::getInstance()->getLogger()->logException($e);
        }
    }

    public function load(): bool {
        $success = parent::load();
        if ($success) {
            if (!in_array(strtolower($this->provider), self::PROVIDERS)) {
                ReportSystem::getInstance()->getLogger()->warning("§cThe provided Provider §e" . strtolower($this->provider) . " §cdoesn't exists, using §eJSON §cinstead...");
                $this->provider = "json";
            }

            if ($this->webhook["enabled"] && !filter_var($this->webhook["webhook_url"], FILTER_VALIDATE_URL)) {
                ReportSystem::getInstance()->getLogger()->warning("§cThe given Webhook-Url is invalid, disabling Webhook-Support...");
                $this->webhook["enabled"] = false;
            }

            foreach ($this->reasons as $key => $data) {
                if (!isset($data["command"]) || !isset($data["display_name"])) {
                    ReportSystem::getInstance()->getLogger()->warning("§cThe reason §e" . $key . " §cis invalid, remove...");
                    unset($this->reasons[$key]);
                }
            }
        }
        return $success;
    }

    public function getProvider(): string {
        return $this->provider;
    }

    public function getMysql(): array {
        return $this->mysql;
    }

    public function getWebhook(): array {
        return $this->webhook;
    }

    public function getReasons(): array {
        return $this->reasons;
    }

    public static function getInstance(): self {
        return self::$instance ??= new self;
    }
}