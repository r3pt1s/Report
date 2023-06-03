<?php

namespace r3pt1s\report;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use r3pt1s\report\command\ReportCommand;
use r3pt1s\report\command\ReportsCommand;
use r3pt1s\report\config\MainConfig;
use r3pt1s\report\listener\EventListener;
use r3pt1s\report\manager\ReportManager;
use r3pt1s\report\provider\JSONProvider;
use r3pt1s\report\provider\MySQLProvider;
use r3pt1s\report\provider\Provider;
use r3pt1s\report\provider\YAMLProvider;
use r3pt1s\report\util\Utils;

class ReportSystem extends PluginBase {
    use SingletonTrait;

    private Provider $provider;
    private ReportManager $reportManager;

    protected function onEnable(): void {
        self::setInstance($this);
        $this->saveResource("messages.yml");

        Utils::fetchMessages();

        $this->provider = match (strtolower(MainConfig::getInstance()->getProvider())) {
            "yml" => new YAMLProvider(),
            "mysql" => new MySQLProvider(),
            default => new JSONProvider()
        };

        $this->reportManager = new ReportManager();

        DefaultPermissions::registerPermission(new Permission("report.command.reports"), [PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR)]);
        DefaultPermissions::registerPermission(new Permission("report.notify"), [PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR)]);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getServer()->getCommandMap()->registerAll("reportSystem", [
            new ReportCommand(),
            new ReportsCommand()
        ]);
    }

    public function getReportManager(): ReportManager {
        return $this->reportManager;
    }

    public function getProvider(): Provider {
        return $this->provider;
    }

    public static function getInstance(): ?self {
        return self::$instance ?? null;
    }
}