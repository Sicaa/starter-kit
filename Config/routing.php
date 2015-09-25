<?php

/**
* Routing configuration file. Each route must be an instance of the Route
* class.
*/

use Controller\Core\Route as Route;

$routes = array(
	// new Route(string $name, string $url, string $ClassController, string $methodName[, bool $https, bool $authRequired , array $permissions]),

	// Hello World example
	new Route('404', '404', 'Controller\Controller\IndexController', 'error404'),

	new Route('hello_world', '', 'Controller\Controller\ExampleController', 'helloWorld'),
	new Route('hello_anyone', 'hello/{anyone}', 'Controller\Controller\ExampleController', 'helloAnyone')
);
