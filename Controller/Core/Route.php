<?php

/**
* The match, findNextSeparator and computeRegexp methods are derived from
* the Symfony Component, distributed under the MIT Licence and available
* here : https://github.com/symfony/symfony. 
*/

namespace Controller\Core;

class Route {
	const DEFAULT_CONTROLLER = 'IndexController';
	const DEFAULT_ACTION     = 'index';
	const SEPARATORS         = '/,;.:-_~+*=@|';
	const REGEX_DELIMITER    = '#';
	
	protected $controllerClass = self::DEFAULT_CONTROLLER;
	protected $action          = self::DEFAULT_ACTION;
	
	protected $httpsRequired   = false;
	protected $authRequired    = false;
	protected $permissions     = array();
 
	public function __construct($pathName_, $path_, $controllerClass_, $action_, $httpsRequired_ = false, $authRequired_ = false, $permissions_ = array()) 
	{
		$this->pathName        = $pathName_;
		$this->path            = $path_;
		$this->controllerClass = $controllerClass_;
		$this->action          = $action_;
		$this->httpsRequired   = $httpsRequired_;
		$this->authRequired    = $authRequired_;
		$this->permissions     = $permissions_;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getPathName()
	{
		return $this->pathName;
	}

	public function getPermissions()
	{
		return $this->permissions;
	}

	public function isAuthRequired()
	{
		return $this->authRequired;
	}

	public function isHttpsRequired()
	{
		return $this->httpsRequired;
	}
 
	/**
	* Thank's Symfony :)
	*/
	public function match(Request $request_) 
	{
		$matches = $variables = $tokens = array();
		$defaultSeparator = '/';
		$uri = $request_->getUri();

		preg_match_all('#\{\w+\}#', $this->path, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

		if (!empty($matches)) { // Use of {} variables
			$pos = 0;
			foreach ($matches as $match) {
				$varName = substr($match[0][0], 1, -1);
				$precedingText = substr($this->path, $pos, $match[0][1] - $pos);
				$pos = $match[0][1] + strlen($match[0][0]);
				$precedingChar = strlen($precedingText) > 0 ? substr($precedingText, -1) : '';
				$isSeparator = $precedingChar !== '' && strpos(static::SEPARATORS, $precedingChar) !== false;
				
				if (is_numeric($varName)) {
					throw new \Exception(sprintf('Variable name "%s" cannot be numeric in route pattern "%s". Please use a different name.', $varName, $this->path));
				}

				if (in_array($varName, $variables)) {
					throw new \Exception(sprintf('Route pattern "%s" cannot reference variable name "%s" more than once.', $this->path, $varName));
				}

				if ($isSeparator && strlen($precedingText) > 1) {
					$tokens[] = array('text', substr($precedingText, 0, -1));
				} else if (!$isSeparator && strlen($precedingText) > 0) {
					$tokens[] = array('text', $precedingText);
				}

				$followingPattern = (string) substr($this->path, $pos);
				$nextSeparator = self::findNextSeparator($followingPattern);
				$regexp = sprintf(
					'[^%s%s]+',
					preg_quote($defaultSeparator, self::REGEX_DELIMITER),
					$defaultSeparator !== $nextSeparator && '' !== $nextSeparator ? preg_quote($nextSeparator, self::REGEX_DELIMITER) : ''
				);

				if (($nextSeparator !== '' && !preg_match('#^\{\w+\}#', $followingPattern)) || $followingPattern === '') {
					$regexp .= '+';
				}
				
				$variables[] = $varName;
				$tokens[] = array('variable', $isSeparator ? $precedingChar : '', $regexp, $varName);
			}

			if ($pos < strlen($this->path)) {
				$tokens[] = array('text', substr($this->path, $pos));
			}

			// compute the matching regexp
			$regexp = '';
			for ($i = 0, $nbToken = count($tokens); $i < $nbToken; ++$i) {
				$regexp .= self::computeRegexp($tokens, $i, PHP_INT_MAX);
			}

			$regex = self::REGEX_DELIMITER.'^'.$regexp.'$'.self::REGEX_DELIMITER.'s';
			$matchingValues = array();
			$res = preg_match($regex, $uri, $matchingValues);
			
			if ($res) {
				foreach ($variables as $k => $v) {
					$request_->setParam($v, $matchingValues[$v]);
				}
				return true;
			}

			return false;
		}

		return $this->path === $request_->getUri();
	}

	public function createController(Router $router_, Request $request_, Response $response_, $auth_)
	{
		return new $this->controllerClass($this, $router_, $request_, $response_, $auth_);
	}

	/**
	 * Returns the next static character in the Route pattern that will serve as a separator.
	 */
	private static function findNextSeparator($pattern_)
	{
		if ($pattern_ == '') {
			// return empty string if pattern is empty or false (false which can be returned by substr)
			return '';
		}
		// first remove all placeholders from the pattern so we can find the next real static character
		$pattern = preg_replace('#\{\w+\}#', '', $pattern_);

		return isset($pattern[0]) && false !== strpos(static::SEPARATORS, $pattern[0]) ? $pattern[0] : '';
	}

	/**
	 * Computes the regexp used to match a specific token. It can be static text or a subpattern.
	 */
	private static function computeRegexp(array $tokens_, $index_, $firstOptional_)
	{
		$token = $tokens_[$index_];
		if ('text' === $token[0]) {
			// Text tokens
			return preg_quote($token[1], self::REGEX_DELIMITER);
		} else {
			// Variable tokens
			if (0 === $index_ && 0 === $firstOptional_) {
				// When the only token is an optional variable token, the separator is required
				return sprintf('%s(?P<%s>%s)?', preg_quote($token[1], self::REGEX_DELIMITER), $token[3], $token[2]);
			} else {
				$regexp = sprintf('%s(?P<%s>%s)', preg_quote($token[1], self::REGEX_DELIMITER), $token[3], $token[2]);
				if ($index_ >= $firstOptional_) {
					// Enclose each optional token in a subpattern to make it optional.
					// "?:" means it is non-capturing, i.e. the portion of the subject string that
					// matched the optional subpattern is not passed back.
					$regexp = "(?:$regexp";
					$nbTokens = count($tokens_);
					if ($nbTokens - 1 == $index_) {
						// Close the optional subpatterns
						$regexp .= str_repeat(')?', $nbTokens - $firstOptional_ - (0 === $firstOptional_ ? 1 : 0));
					}
				}

				return $regexp;
			}
		}
	}
}
