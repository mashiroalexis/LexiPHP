<?php
namespace Errors\Controller;

use Errors\Controller\Config;
use Errors\Controller\Block as Block;
use Errors\Controller\Logger;

/**
 *	Modify PHP defualt error handler
 */
Class Handler {

	/**
	 *	Error Types
	 */
	protected $errorTypes = [
		"400" => [
			"title" => "Bad Request",
			"message" => "The server cannot process the request due to something that is perceived to be a client error."
		],
		"401" => [
			"title" => "Unauthorized",
			"message" => "The requested resource requires an authentication."
		],
		"403" => [
			"title" => "Access Denied",
			"message" => "The requested resource requires an authentication."
		],
		"404" => [
			"title" => "Resource not found",
			"message" => "The requested resource could not be found but may be available again in the future."
		],
		"500" => [
			"title" => "Webservice currently unavailable",
			"message" => "An unexpected condition was encountered. Our service team has been dispatched to bring it back online."
		],
		"501" => [
			"title" => "Not Implemented",
			"message" => "The Webserver cannot recognize the request method."
		],
		"502" => [
			"title" => "Webservice currently unavailable",
			"message" => "We've got some trouble with our backend upstream cluster. Our service team has been dispatched to bring it back online."
		],
		"503" => [
			"title" => "Webservice currently unavailable",
			"message" => "We've got some trouble with our backend upstream cluster. Our service team has been dispatched to bring it back online."
		],
		"520" => [
			"title" => "Origin Error - Unknown Host",
			"message" => "The requested hostname is not routed. Use only hostnames to access resources."
		],
		"521" => [
			"title" => "Webservice currently unavailable",
			"message" => "We've got some trouble with our backend upstream cluster. Our service team has been dispatched to bring it back online."
		],
		"e_maintenance" => [
			"title" => "Webservice currently unavailable",
			"message" => "Our site is under maintenance.<br/>Our service team has been dispatched to bring it back online."
		]
	];

	// path to blocks
	private $blockPath;

	// let set the handler
	public function __construct() {
		$this->blockPath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "view" . DIRECTORY_SEPARATOR;
		set_error_handler([$this, 'errorHandler']);
		set_exception_handler([$this, 'exceptionHandler']);
		
		# so sad :( deprecated in php 7.2 major release
		// register_shutdown_function([$this, 'shutdownHandler']);
	}

	/**
	 *	Render the generated error
	 */
	public function render() {
		// get the main block
		echo $this->blockPath . 'main.phtml';
	}

	/**
	 *	Get Current URI
	 */
	public function getUri() {
		return $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}

	/**
	 *	Let set the error endpoints
	 */

	/**
	 *	Error endpoint
	 *	@param int $serverity
	 *	@param string $message
	 *	@param string $filepath
	 *	@param int $line
	 *	@return void
	 */	
	public function errorHandler( $error_level = "", $error_message = "", $error_file = "", $error_line = "", $error_context = "" ) {
		$block = new Block;
		$block->setErrorType("error");
		$block->setError([
			"error_level" => $error_level,
			"error_message" => $error_message,
			"error_file" => $error_file,
			"error_line" => $error_line,
			"error_context" => $error_context
		]);
		$block->setBlock("main");
		$block->render();
		return;
	}

	/**
	 *	Exception Handler
	 *	@param obj $e
	 *	@return void
	 */
	public function exceptionHandler( $e ) {
		$block = new Block;
		$block->setErrorType("exception");
		$block->setError($e);
		$block->setBlock("main");
		$block->render();
		return;
	}

	/**
	 *	Shutdown handler [ PHP 7.2 does support this anymore :( ]
	 *	@param func error_get_last()
	 *	@return void
	 */
	public function shutdownHandler() {
		if(! empty(error_get_last()) ) {
			$error = error_get_last();
			$this->errorHandler( $error['type'], $error['message'], $error['file'], $error['line'], $error['context'] );
			return;
		}
	}

	/**
	 *	Dispath an error without being triggered.
	 *	@param string $type
	 *	@return void
	 */
	public function dispatch( $type ) {
		$block = new Block;
		# check if the error type valid
		if(! isset($this->errorTypes[$type]) ) {
			throw new \Exception("Dispatch type is invalid", 1);
			return;
		}
		$type = $this->errorTypes[$type];
		$block->setErrorType('dispatch');
		$block->setError($type);
		$block->setBlock('main');
		$block->render();
		return;
	}

	/**
	 *	Logger
	 *	@param string $str
	 *	@return void
	 */
	public static function log( $str ) {
		echo "<pre>";
		print_r($str);
		echo "</pre>";
		return;
	}

	/**
	 *	Get the error types
	 */
	public function getErrorTypes() {
		return $this->errorTypes;
	}

	/**
	 *	Let's boot this!
	 */
	public static function boot() {
		return new Handler;
	}
}