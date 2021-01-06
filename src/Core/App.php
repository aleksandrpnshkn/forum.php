<?php
declare(strict_types=1);

namespace Src\Core;

use Src\Controllers\Controller;
use Src\Repositories\Repository;

final class App
{
    public Database $db;

    public string $tablesPath;
    public string $rollbackPath;

    public View $view;
    public Auth $auth;

    public function __construct()
    {
        $this->db = new Database();
        $this->tablesPath = realpath(__DIR__ . '/../../database/migrate.sql');
        $this->rollbackPath = realpath(__DIR__ . '/../../database/rollback.sql');

        Repository::$db = $this->db;

        $this->auth = new Auth();
        $this->view = new View($this->auth);

        Controller::$uploadsDirPath = realpath(__DIR__ . '/../../public/uploads');
        Controller::$uploadsDirUrl = '/uploads';
    }
}
