<?php

/**
* Handle triggered errors and uncaught exceptions. You may not want to exit
* the process for certain type of errors (E_NOTICE). Here it's a personnal
* choice.
*/

function exceptionHandler(\Exception $e)
{
	if (ERROR_NOTIFICATION_DISPLAY === true) {
		$message = '<div style="text-align:left;padding:10px;border:1px solid #000;background-color:#FFB6B6;">';
		$message .= '<strong>'.get_class($e).'</strong><br>';
		$message .= '('.$e->getCode().') '.$e->getMessage().'<br>';
		$message .= '<strong>in '.$e->getFile().':'.$e->getLine().'</strong>';
		$message .= '<pre>'.print_r(debug_backtrace(), true).'</pre></div>';
		echo $message;
	}

	if (ERROR_NOTIFICATION_MAIL === true) {
		$message = date('r')."\n";
		$message .= get_class($e)."\n";
		$message .= '('.$e->getCode().') '.$e->getMessage()."\n";
		$message .= 'in '.$e->getFile().':'.$e->getLine()."\n";
		$message .= '$_REQUEST = '."\n";
		$message .= print_r($_REQUEST, true);
		if (isset($_SERVER['HTTP_REFERER']))
			$message .= 'REFERER = '.$_SERVER['HTTP_REFERER']."\n";

		error_log($message, 1, ADMIN_MAIL, 'Subject: [ERROR '.$_SERVER['SERVER_NAME'].']');
	}

	if (ERROR_NOTIFICATION_LOG === true) {
		$log = '---------------------------------------------------'."\n";
		$log .= date('r')."\n";
		$log .= get_class($e)."\n";
		$log .= '('.$e->getCode().') '.$e->getMessage()."\n";
		$log .= 'in '.$e->getFile().':'.$e->getLine()."\n";

		if (isset($_SERVER['HTTP_REFERER']))
			$log .= 'REFERER = '.$_SERVER['HTTP_REFERER']."\n";

		$log .= 'REQUEST : '.print_r($_SERVER['REQUEST_URI'], true).print_r($_SERVER['QUERY_STRING'], true)."\n";

		$logFolderPath = ERROR_NOTIFICATION_LOG_DIR;
		$logFilePath = $logFolderPath.DIRECTORY_SEPARATOR.$_SERVER['SERVER_NAME'].'_error.log';
		clearstatcache();

		if (!file_exists($logFolderPath)) {
			mkdir($logFolderPath);
			chmod($logFolderPath, 0777);
		}

		if (file_exists($logFilePath) && filesize($logFilePath) > 524288000) // 500Mo
			unlink($logFilePath);
		error_log($log, 3, $logFilePath);
	}
	exit;
}
