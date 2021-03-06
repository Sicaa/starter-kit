<?php

require_once 'Functions/Errors/ExceptionHandler.php';
require_once 'Functions/Errors/ErrorHandler.php';
require_once 'Functions/Errors/FatalErrorHandler.php';

set_error_handler('errorHandler', E_ALL);
set_exception_handler('exceptionHandler');
register_shutdown_function('fatalErrorHandler');
ini_set('display_errors', 'off');

require_once __DIR__.'/config.php';
require_once 'Vendor/autoload.php';

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

StarterKit\ORM\SimpleORM::setCachingRules(MEMCACHED_HOST, MEMCACHED_PORT, MEMCACHED_PREFIX, MEMCACHED_EXPIRE);
