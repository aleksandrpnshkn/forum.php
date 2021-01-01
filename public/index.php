<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

global $app;
echo $app->view('home', ['username' => 'ADMIN']);