<?php
declare(strict_types=1);

namespace Src\Core;

use Src\Models\User;
use Src\Repositories\UserRepository;

final class App
{
    public Database $db;

    public string $tablesPath;
    public string $rollbackPath;

    public View $view;
    public Auth $auth;

    private UserRepository $userRepository;

    public function __construct()
    {
        $this->db = new Database();
        $this->tablesPath = realpath(__DIR__ . '/../../database/migrate.sql');
        $this->rollbackPath = realpath(__DIR__ . '/../../database/rollback.sql');

        // I don't want to pass everytime App or UserRepository to User constructor
        User::$userRepository = $this->getUserRepository();

        $this->auth = new Auth($this->getUserRepository());
        $this->view = new View($this->auth);
    }

    public function getUserRepository() : UserRepository
    {
        if (! isset($this->userRepository)) {
            $this->userRepository = new UserRepository($this->db);
        }

        return $this->userRepository;
    }
}
