<?php

namespace r3pt1s\report\config;

use pocketmine\utils\Config;
use r3pt1s\report\ReportSystem;

class MainConfig extends Config {

    public const PROVIDERS = ["json", "yml", "mysql"];

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
        parent::__construct(ReportSystem::getInstance()->getDataFolder() . "config.yml", self::YAML, [
            "provider" => $this->provider,
            "mysql" => $this->mysql,
            "webhook" => $this->webhook,
            "reasons" => $this->reasons
        ]);

        $this->provider = $this->get("provider", $this->provider);
        try {
            $this->mysql = $this->get("mysql", $this->mysql);
        } catch (\Exception $exception) {}

        try {
            $this->webhook = $this->get("webhook", $this->webhook);
        } catch (\Exception $exception) {}

        try {
            $this->reasons = $this->get("reasons", $this->reasons);
        } catch (\Exception $exception) {}

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