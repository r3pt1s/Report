<?php

namespace r3pt1s\reportsystem\util;

use pocketmine\utils\Config;
use r3pt1s\reportsystem\config\MainConfig;
use r3pt1s\reportsystem\ReportSystem;

class Utils {

    private static array $messages = [];

    public static function fetchMessages() {
        $file = new Config(ReportSystem::getInstance()->getDataFolder() . "messages.yml", Config::YAML);
        foreach ($file->getAll() as $key => $message) {
            if (is_string($message)) {
                self::$messages[$key] = $message;
            }
        }
    }

    public static function parseMessage(string $key, array $parameters = []): string {
        $message = str_replace("{PREFIX}", self::$messages["prefix"] ?? "", self::$messages[$key] ?? $key);
        foreach ($parameters as $parameterKey => $parameter) {
            $message = str_replace($parameterKey, $parameter, $message);
        }
        return $message;
    }

    public static function sendWebhookMessage(array $data) {
        if (MainConfig::getInstance()->getWebhook()["enabled"]) {
            $url = MainConfig::getInstance()->getWebhook()["webhook_url"];
            AsyncExecutor::execute(function() use($data, $url): array {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_POST,true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
                $result = [curl_exec($ch), curl_getinfo($ch, CURLINFO_RESPONSE_CODE)];
                curl_close($ch);
                return $result;
            }, function(array $data): void {
                if(!in_array($data[1], [200, 204])) ReportSystem::getInstance()->getLogger()->error("Discord answered with code " . $data[1] . ": " . $data[0]);
            });
        }
    }
}