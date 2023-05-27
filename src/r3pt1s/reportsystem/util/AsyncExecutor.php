<?php

namespace r3pt1s\reportsystem\util;

use Closure;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use r3pt1s\reportsystem\config\MainConfig;

class AsyncExecutor {

    public static array $taskClosures = [];

    public static function execute(Closure $closure, ?Closure $onCompletion = null): void {
        $id = null;
        if ($onCompletion !== null) {
            $id = uniqid("", true);
            self::$taskClosures[$id] = $onCompletion;
        }

        Server::getInstance()->getAsyncPool()->submitTask(new class($closure, $id) extends AsyncTask {

            private string $mysqlData;

            public function __construct(
                private Closure $closure,
                private ?string $id = null
            ) {
                $this->mysqlData = json_encode(MainConfig::getInstance()->getMysql());
            }

            public function onRun(): void {
                $this->setResult(($this->closure)(new Database(json_decode($this->mysqlData, true))));
            }

            public function onCompletion(): void {
                if ($this->id !== null) {
                    $closure = AsyncExecutor::$taskClosures[$this->id];
                    ($closure)($this->getResult());
                    unset(AsyncExecutor::$taskClosures[$this->id]);
                }
            }
        });
    }
}