<?php

/**
* This file is overwritten by the local configuration file if exists
* (config.php.local). You can use this configuration file to push your prod
* configuration, or simply leave default values and place by hand the local
* configuration file in production to allow more security.
*/

$config = array(
	// Error handling
	'ERROR_NOTIFICATION_DISPLAY' => false,
	'ERROR_NOTIFICATION_LOG' => true,
	'ERROR_NOTIFICATION_MAIL' => true,

	'ERROR_NOTIFICATION_LOG_DIR' => 'Logs',

	'ADMIN_MAIL' => 'email@domain.com',

	// Used to properly route requests
	'BASE_URL' => 'http://domain.com',
	'ROOT_URL' => '/',

	// Cache using Memcached PHP extension
	'CACHING' => false,
	'MEMCACHED_HOST' => 'localhost',
	'MEMCACHED_PORT' => 11211,
	'MEMCACHED_PREFIX' => 'custom_prefix_',
	'MEMCACHED_EXPIRE' => 300,

	// Database info
	'DB_SERVER' => 'localhost',
	'DB_USER' => 'root',
	'DB_PWD' => '',

	// Twig
	'TWIG_VIEWS_DIR' => 'Views'
);

if (file_exists(__DIR__.'/config.php.local')) {
	require_once(__DIR__.'/config.php.local');
}

foreach ($config as $k => $v) {
	define($k, $v);
}
