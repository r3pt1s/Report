<?php

namespace r3pt1s\report\task;

use pocketmine\scheduler\AsyncTask;
use r3pt1s\report\config\MainConfig;
use r3pt1s\report\util\AsyncExecutor;
use r3pt1s\report\util\Database;

class AsyncExecutorTask extends AsyncTask {

    private string $mysqlData;

    public function __construct(
        private \Closure $closure,
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
}