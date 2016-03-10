<?php

/**
* All requests have this file as endpoint. It simply pass all routes to the
* FrontController which process the routing work and call the right controller.
*/

use Symfony\Component\Yaml\Parser;
use Controller\Core\Route;

require __DIR__.'/Config/loader.php';

if (file_exists(__DIR__.'/Config/routing.yml')) {
	$yaml = new Parser();
	$raw = $yaml->parse(file_get_contents(__DIR__.'/Config/routing.yml'));
	$routes = array();
	foreach ($raw as $k => $v) {
		$https = $auth = false;
		$permissions = array();
		extract($v);

		if (is_null($v['url'])) {
			$v['url'] = '';
		}

		$routes[] = new Route((string) $k, (string) $v['url'], (string) $v['controller'], (string) $v['method'], $https, $auth, $permissions);
	}
} else {
	require __DIR__.'/Config/routing.php';
}

use Controller\Core as Core;

$router = new Core\Router($routes, ROOT_URL);

$frontController = new Core\FrontController($router);
$frontController->run();
