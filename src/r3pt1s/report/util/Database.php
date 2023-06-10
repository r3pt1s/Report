<?php

namespace r3pt1s\report\util;

class Database extends Medoo {

    public function __construct(private array $data) {
        parent::__construct(array_merge(["type" => "mysql"], $this->data));
    }

    public function exec(string $statement, array $map = [], callable $callback = null): ?\PDOStatement {
        try {
            return parent::exec($statement, $map, $callback);
        } catch (\Exception $exception) {
            if (str_contains("gone away", $exception->getMessage())) {
                parent::__construct(array_merge(["type" => "mysql"], $this->data));
                return parent::exec($statement, $map, $callback);
            } else \GlobalLogger::get()->logException($exception);
        }
        return null;
    }

    public function initializeTable() {
        $this->create("reports", [
            "target" => "VARCHAR(12) PRIMARY KEY",
            "player" => "VARCHAR(12)",
            "reason" => "VARCHAR(100)"
        ]);
    }
}