<?php
declare(strict_types=1);

namespace Src\Core;

use JetBrains\PhpStorm\Pure;

class Csrf
{
    private string $token;

    public function __construct()
    {
        if (! isset($_SESSION['csrf'])) {
            $_SESSION['csrf'] = $this->generateToken();
        }

        $this->token = $_SESSION['csrf'];
    }

    public function getToken() : string
    {
        return $this->token;
    }

    public function validateToken(string $token) : bool
    {
        return isset($_SESSION['csrf']) && $_SESSION['csrf'] === $token;
    }

    #[Pure] private function generateToken() : string
    {
        return uniqid('csrf', true);
    }
}
