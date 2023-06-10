<?php

namespace r3pt1s\report\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\thread\NonThreadSafeValue;
use r3pt1s\report\config\MainConfig;
use r3pt1s\report\util\Database;

class AsyncExecutorTask extends AsyncTask {

    private NonThreadSafeValue $mysql;

    public function __construct(private \Closure $closure, private ?\Closure $completion = null) {
        $this->mysql = new NonThreadSafeValue(MainConfig::getInstance()->getMysql());
    }

    public function onRun(): void {
        $this->setResult(($this->closure)($this, new Database($this->mysql->deserialize())));
    }

    public function onCompletion(): void {
        if ($this->completion !== null) {
            ($this->completion)($this->getResult());
        }
    }

    public static function new(\Closure $closure, ?\Closure $completion = null): self {
        return new self($closure, $completion);
    }
}