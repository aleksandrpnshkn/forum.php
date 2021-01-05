<?php
declare(strict_types=1);

namespace Src\Core;

use Src\Repositories\UserRepository;

final class App
{
    public Database $db;

    public string $tablesPath;
    public string $rollbackPath;

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->db = new Database();
        $this->tablesPath = realpath(__DIR__ . '/../../database/migrate.sql');
        $this->rollbackPath = realpath(__DIR__ . '/../../database/rollback.sql');

    }

    public function getUserRepository() : UserRepository
    {
        if (! isset($this->userRepository)) {
            $this->userRepository = new UserRepository($this->db);
        }

        return $this->userRepository;
    }

    public function view(string $viewName, array $vars = []) : string
    {
        extract($vars, EXTR_OVERWRITE);
        ob_start();
        require __DIR__ . '/../../resources/views/' . rtrim(trim($viewName, '/'), '.htm') . '.htm';
        return ob_get_clean();
    }
}
