<?php
/*
 * ColorApp
 */

$appFile = "config/Colors.ini";
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

function error_handler($code, $message, $file, $line) {
	$error = "code: {$code} message: {$message} file: {$file} line: {$line}\r\n";
	error_log($error);
	echo "Sorry, we have an Error. Please contact Support at (800)555-1212 \r\n.";
}

function exception_handler($exception) {
	error_log(print_r($exception,1));
	echo "Sorry, we have a problem. Please contact Support at (800)555-1212 \r\n";
}

//okay now do the app.  we're treating this like a front-end controller
$colorApp = new ColorApp("Colors");
$colorApp->start();

