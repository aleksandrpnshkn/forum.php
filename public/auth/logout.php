<?php
declare(strict_types=1);

use Src\Controllers\AuthController;

require_once __DIR__ . '/../../src/bootstrap.php';

global $app;
$controller = new AuthController($app);
$controller->logout();
