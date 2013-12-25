<?php
/*
 * ColorApp
 */

$paths = array( 
	realpath('.'),
	realpath('./class'),
	get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $paths));

/**
 * register an autoloader
 */
function __autoload ($class) {
	if(!file_exists($class . '.php') ) {
		$parts = explode('_', $class);
		$class = implode(DIRECTORY_SEPARATOR, $parts ). '.php'; 
	}
	include_once $class; 
}

//okay now do the app.  we're treating this like a front-end controller
$colorApp = new ColorApp();
$colorApp->start();

