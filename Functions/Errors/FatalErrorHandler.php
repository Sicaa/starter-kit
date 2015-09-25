<?php

require_once 'ErrorHandler.php';

function fatalErrorHandler()
{
	$err = error_get_last();
	if ($err['type'] == E_ERROR)
		errorHandler($err['type'], $err['message'], $err['file'], $err['line']);
}
