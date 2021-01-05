<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

global $app;
$app->view->display('home', ['username' => $app->auth->isLoggedIn() ? $app->auth->getUser()->username : 'Guest' ]);
