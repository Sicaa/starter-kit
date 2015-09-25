<?php

/**
* All requests have this file as endpoint. It simply pass all routes to the
* FrontController which process the routing work and call the right controller.
*/

require __DIR__.'/Config/loader.php';
require __DIR__.'/Config/routing.php';

use Controller\Core as Core;

$router = new Core\Router($routes, ROOT_URL);

$frontController = new Core\FrontController($router);
$frontController->run();
