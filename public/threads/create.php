<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/bootstrap.php';

use Src\Controllers\ThreadController;

global $app;
$controller = new ThreadController($app);

if ($controller->isGet()) {
    $controller->create();
}
elseif ($controller->isPost()) {
    $controller->store();
}
