<?php

require_once 'ErrorHandler.php';

function fatalErrorHandler()
{
	$types = array(E_ERROR, E_PARSE);
	$err = error_get_last();
	if (in_array($err['type'], $types))
		errorHandler($err['type'], $err['message'], $err['file'], $err['line']);
}
