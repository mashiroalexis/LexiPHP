<?php
/**
 * Copyright © Ramon Alexis Celis All rights reserved.
 * See license file for more info.
 */


/*
|--------------------------------------------------------------------------
| All autoloader
|--------------------------------------------------------------------------
|
|	Commands that are listed in here will be executable on the terminal.
*/
return [

	/*
	|--------------------------------------------------------------------------
	|	Console Autoloader
	|--------------------------------------------------------------------------
	|
	| Autoloader for console script.
	*/
	'console' => "app/code/base/console/autoload.php",

	/*
	|--------------------------------------------------------------------------
	|	Database Autoloader
	|--------------------------------------------------------------------------
	|
	| Autoloader for Migrations and Seeders.
	*/
	'database' => "database/autoload.php",

	/*
	|--------------------------------------------------------------------------
	|	Middleware Autoloader
	|--------------------------------------------------------------------------
	|
	| Autoloader for middleware
	*/
	'middleware' => "app/code/middleware/autoload.php",

	/*
	|--------------------------------------------------------------------------
	|	Composer Support
	|--------------------------------------------------------------------------
	|
	| Autoloader for middleware
	*/
	// 'middleware' => BP . DS . 'vendor' . DS . 'autoload.php',
];