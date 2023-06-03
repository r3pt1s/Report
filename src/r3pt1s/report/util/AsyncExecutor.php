<?php

namespace r3pt1s\report\util;

use pocketmine\Server;
use r3pt1s\report\task\AsyncExecutorTask;

class AsyncExecutor {

    public static array $taskClosures = [];

    public static function execute(\Closure $closure, ?\Closure $onCompletion = null): void {
        $id = null;
        if ($onCompletion !== null) {
            $id = uniqid("", true);
            self::$taskClosures[$id] = $onCompletion;
        }

        Server::getInstance()->getAsyncPool()->submitTask(new AsyncExecutorTask($closure, $id));
    }
}