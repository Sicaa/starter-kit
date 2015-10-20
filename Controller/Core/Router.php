<?php

namespace Controller\Core;

class Router {
	protected $basePath = NULL;

	public function __construct($routes_, $basePath_)
	{
		$this->addRoutes($routes_);
		$this->basePath = $basePath_;
	}

	public function getBasePath()
	{
		return $this->basePath;
	}

	public function addRoute($pathName_, Route $route_) 
	{
		$this->routes[$pathName_] = $route_;
		return $this;
	}

	public function addRoutes(array $routes_) 
	{
		foreach ($routes_ as $route) {
			$this->addRoute($route->getPathName(), $route);
		}
		return $this;
	}

	public function getRoutes() 
	{
		return $this->routes;
	}

	public function getRoute($pathName_)
	{
		if (array_key_exists($pathName_, $this->routes))
			return $this->routes[$pathName_];
		return false;
	}

	/**
	* Route and match the request with a set of routes
	* @return \Controller\Core\Route $route
	*/
	public function route(Request $request_, Response $response_)
	{
		foreach ($this->routes as $pathName => $route) {
			if ($route->match($request_)) {
				return $route;
			}
		}
		$response_->addHeader('Location: '.$this->path('404'))->send();
		die();
	}

	/**
	* Return the matched route path
	* @return string $route
	*/
	public function path($testedPathName_, array $urlParams_ = array())
	{
		foreach ($this->routes as $pathName => $route) {
			if ($testedPathName_ == $pathName) {
				$path = $route->getPath();
				$paramsToFill = $route->getPathVariables();

				if (!empty($paramsToFill)) { // There is some mandatory params to fill in the URL path
					if (empty($urlParams_)) { // Missing parameters
						throw new \Exception(sprintf('Route pattern %s needs some mandatory parameters to be correctly constructed', $path));
					}

					foreach ($paramsToFill as $k => $v) {
						$name = substr($v, 1, -1); // Removing brackets

						if (!array_key_exists($name, $urlParams_)) {
							throw new \Exception(sprintf('Route pattern %s needs "%s" parameter defined to be correctly constructed', $path, $name));
						}

						$path = str_replace($v, $urlParams_[$name], $path);
						unset($urlParams_[$name]);
					}
				}

				if (!empty($urlParams_)) { // Add simple query string to the path for the rest of parameters given
					$supUrl = '?';
					$i = 0;
					foreach ($urlParams_ as $k => $v) {
						$supUrl .= ($i > 0) ? '&' : '';
						$supUrl .= $k.'='.$v;
						$i++;
					}
					$path .= $supUrl;
				}

				return $this->getBasePath().$path;
			}
		}
		throw new \OutOfRangeException('No route matched the given path.');
	}
}
