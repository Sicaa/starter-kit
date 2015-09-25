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
	public function path($testedPathName_) 
	{
		foreach ($this->routes as $pathName => $route) {
			if ($testedPathName_ == $pathName) {
				return $this->getBasePath().$route->getPath();
			}
		}
		throw new \OutOfRangeException('No route matched the given path.');
	}
}
