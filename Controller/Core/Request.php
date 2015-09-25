<?php

namespace Controller\Core;

class Request {
	private $uri = NULL;
	private $params = NULL;
	private $relativePath = NULL;
	private $basePath = NULL;

	public function __construct($uri_, $R_ = './', $basePath_ = NULL, $params_ = array()) 
	{
		$this->uri          = $uri_;
		$this->params       = $params_;
		$this->relativePath = $R_;
		$this->basePath     = $basePath_;
	}

	public function getUri() 
	{
		return $this->uri;
	}

	public function getRelativePath() 
	{
		return $this->relativePath;
	}

	public function getBasePath() 
	{
		return $this->basePath;
	}

	public function setParam($key_, $value_) 
	{
		$this->params[$key_] = $value_;
		return $this;
	}

	public function setParams($params_) 
	{
		$this->params = $params_;
		return $this;
	}

	public function getParam($key_) 
	{
		if (!isset($this->params[$key_])) {
			throw new \Exception("The request parameter with key '$key_' is invalid.");
		}
		return $this->params[$key_];
	}

	public function getParams() 
	{
		return $this->params;
	}
}
