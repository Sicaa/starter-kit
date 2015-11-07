<?php

namespace Controller\Controller;
use Controller\Core as Core;

class IndexController 
{
	protected $router;
	protected $request;
	protected $response;

	protected $templateEngine;
	protected $auth;

	public function __construct(Core\Route $route_, Core\Router $router_, Core\Request $request_, Core\Response $response_, $auth_)
	{
		$this->route    = $route_;
		$this->router   = $router_;
		$this->request  = $request_;
		$this->response = $response_;
		$this->auth		= $auth_;

		switch (TEMPLATE_ENGINE) {
			case 'smarty':
				$this->templateEngine = new \Smarty();
				$this->templateEngine->setTemplateDir(VIEWS_DIR);
				$this->templateEngine->setCompileDir(sys_get_temp_dir());
				$this->templateEngine->assign('R', $this->request->getRelativePath());
				$this->templateEngine->registerPlugin('function', 'path', array($this, 'generateSmartyUrl'));
				break;
			case 'twig':
			default:
				$loader = new \Twig_Loader_Filesystem(VIEWS_DIR);
				$this->templateEngine = new \Twig_Environment($loader, array(
					// Prod only
					// 'cache' => VIEWS_DIR.'/cache',
				));

				// Use it to load content (CSS, JS, etc.) with a relative path
				$this->templateEngine->addGlobal('R', $this->request->getRelativePath());

				$pathFunction = new \Twig_SimpleFunction('path', function($routeName_, $params_ = array()) {
					return $this->generateUrl($routeName_, $params_);
				});
				$this->templateEngine->addFunction($pathFunction);
				break;
		}
	}

	public function generateUrl($routeName_, array $params_ = array())
	{
		return $this->router->path($routeName_, $params_);
	}

	/**
	* Dirty hack to fit Smarty rules...
	* Usage: {path route='routeName', param1='yourParam', param2='yourParam',...}
	*/
	public function generateSmartyUrl(array $params_ = array())
	{
		return $this->router->path(array_shift($params_), $params_);
	}

	public function checkPermissions()
	{
		if ($this->route->isAuthRequired()) {
			$permissions = $this->route->getPermissions();
			if (!empty($permissions)) { 
				foreach ($permissions as $key => $authorizedGroup) {
					if ($this->auth->getGroup() === $authorizedGroup)
						return true;
				}

				// Unauthorized
				$this->response->addHeader('HTTP/1.1 403 Forbidden')->send();
				echo $this->render('./errors/403.html.twig', array());
				exit;
			}
		}
		return true;
	}

	public function error404Action()
	{
		$this->response->addHeader($_SERVER['SERVER_PROTOCOL'].' 404 Not Found')->send();
		echo $this->render('./errors/404.html.twig', array());
		exit;
	}

	public function redirect($routeName_)
	{
		$this->response->addHeader('Location: '.$this->generateUrl($routeName_));
		$this->response->send();
		exit;
	}

	protected function render($templatePath_, array $params_ = array())
	{
		switch (TEMPLATE_ENGINE) {
			case 'smarty':
				return $this->templateEngine->display($templatePath_);
				break;
			case 'twig':
			default:
				return $this->templateEngine->render($templatePath_, $params_);
				break;
		}
	}
}
