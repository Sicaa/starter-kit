<?php

/**
* Routing configuration file. Each route must be an instance of the Route
* class. If routing.yml is present, this file is not taken into account.
*/

use Controller\Core\Route;

$routes = array(
	// new Route(string $name, string $url, string $ClassController, string $methodName[, bool $https, bool $authRequired , array $permissions]),

	new Route('404', '404', 'Controller\Controller\IndexController', 'error404'),

	// Hello World example
	new Route('hello_world', '', 'Controller\Controller\ExampleController', 'helloWorld'),
	new Route('hello_anyone', 'hello/{anyone}', 'Controller\Controller\ExampleController', 'helloAnyone')
);
