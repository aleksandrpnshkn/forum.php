<?php
declare(strict_types=1);

namespace Src\Core;

final class App
{
    public Database $db;

    public string $tablesPath;
    public string $rollbackPath;

    public function __construct()
    {
        $this->db = new Database();
        $this->tablesPath = realpath(__DIR__ . '/../../database/migrate.sql');
        $this->rollbackPath = realpath(__DIR__ . '/../../database/rollback.sql');
    }
}