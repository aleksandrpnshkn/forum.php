<?php
declare(strict_types=1);

use Src\Controllers\BoardController;

require_once __DIR__ . '/../../src/bootstrap.php';

global $app;
$controller = new BoardController($app);

if ($controller->isGet()) {
    $controller->create();
}
else {
    $controller->store();
}
