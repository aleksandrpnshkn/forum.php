<?php
declare(strict_types=1);

use Src\Controllers\CategoryController;

require_once __DIR__ . '/../src/bootstrap.php';

global $app;
$controller = new CategoryController($app);
$controller->index();
