<?php
declare(strict_types=1);

use Src\Controllers\CategoryController;

require_once __DIR__ . '/../../src/bootstrap.php';

global $app;
$controller = new CategoryController($app);

if ($controller->isGet()) {
    $controller->edit();
}
elseif ($controller->isPost()) {
    $controller->update();
}
