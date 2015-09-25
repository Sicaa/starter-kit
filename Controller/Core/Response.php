<?php

namespace Controller\Core;

class Response {
	private $version;
	
	public function __construct($version) 
	{
		$this->version = $version;
	}

	public function getVersion() 
	{
		return $this->version;
	}

	public function addHeader($header) 
	{
		$this->headers[] = $header;
		return $this;
	}

	public function addHeaders(array $headers) 
	{
		foreach ($headers as $header) {
			$this->addHeader($header);
		}
		return $this;
	}

	public function getHeaders() 
	{
		return $this->headers;
	}

	public function send() 
	{
		if (!headers_sent()) {
			foreach ($this->headers as $header) {
				header($header, true);
				// header("$this->version $header", true);
			}
		}
	}
}
