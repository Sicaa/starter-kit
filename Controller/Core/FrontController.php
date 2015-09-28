<?php

namespace Controller\Core;

class FrontController {
	protected $request;
	protected $response;
	protected $router;

	protected $auth = NULL;
	protected $authCheckerMethod = NULL;

	public function __construct(Router $router_, $auth_ = NULL, $authCheckerMethod_ = NULL)
	{
		$this->router = $router_;
		$this->response = new Response($_SERVER['SERVER_PROTOCOL']);
		$this->request = $this->parseUri($this->router->getBasePath());

		if (is_object($auth_) && !is_null($authCheckerMethod_)) {
			$this->auth = $auth_;
			$this->authCheckerMethod = $authCheckerMethod_;
		}
	}

	protected function parseUri($basePath_ = '')
	{
		$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		if (strpos($path, $basePath_) === 0) {
			$path = substr($path, strlen($basePath_));
		}
		$path = ltrim($path, '/');

		if ($path != '' && strpos($_SERVER['PHP_SELF'], $path) !== false) { // index.php directly called
			$this->response->addHeader('Location: '.BASE_URL.ROOT_URL)->send();
			die();
		}

		$arrUri = explode('/', $path, 4);
		$R = '';
		foreach ($arrUri as $key => $value) {
			$R .= ($key === 0) ? './' : '../';
		}

		return new Request($path, $R, $basePath_);
	}

	public function run()
	{
		$route = $this->router->route($this->request, $this->response);
		$redirect = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		// Https redirection
		if ($route->isHttpsRequired() && $_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_PORT'] != 443) {
			$redirect = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$this->response->addHeader('HTTP/1.1 301 Moved Permanently');
			$this->response->addHeader('Location: '.$redirect)->send();
			exit;
		}

		if ($route->isAuthRequired()) {
			$methodToCall = $this->authCheckerMethod;
			if (!$this->auth->$methodToCall()) {
				$_SESSION['target'] = $redirect;
				$this->response->addHeader('Location: '.$this->router->path('login'))->send();
				exit;
			}
		}

		return $this->dispatch($route);
	}

	private function dispatch(Route $route_)
	{
		$controller = $route_->createController($this->router, $this->request, $this->response, $this->auth);
		$action = $route_->getAction().'Action';

		if (!method_exists($controller, $action))
			throw new \Exception('Unknow method '.$action.' in '.get_class($controller), 1);

		echo $controller->$action();
		return;
	}
}
