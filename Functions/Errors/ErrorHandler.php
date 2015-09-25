<?php

require_once 'ExceptionHandler.php';

function errorHandler($errno, $errstr, $errfile, $errline, $errcontext = NULL)
{
	$errortype = array(
		E_ERROR             => 'E_ERROR',
		E_WARNING           => 'E_WARNING',
		E_NOTICE            => 'E_NOTICE',
		E_USER_ERROR        => 'E_USER_ERROR',
		E_USER_WARNING      => 'E_USER_WARNING',
		E_USER_NOTICE       => 'E_USER_NOTICE',
		E_STRICT            => 'E_STRICT',
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		E_DEPRECATED        => 'E_DEPRECATED',
		E_USER_DEPRECATED   => 'E_USER_DEPRECATED'
	);

	exceptionHandler(new \ErrorException($errortype[$errno].' - '.$errstr, 0, $errno, $errfile, $errline));
}
