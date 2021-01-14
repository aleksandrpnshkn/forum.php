<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/bootstrap.php';

use Src\Controllers\MessageController;

global $app;
$controller = new MessageController($app);

if ($controller->isGet()) {
    $controller->create();
}
elseif ($controller->isPost()) {
    $controller->store();
}
