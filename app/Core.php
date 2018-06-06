<?php
/**
 *
 * MIT License
 *
 * Copyright (c) 2017 Ramon Alexis Celis
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
ob_start();
spl_autoload_register(function($class) {
	$class = strtolower(str_replace("_", DIRECTORY_SEPARATOR, $class));
	$paths = Core::$paths;
	
	foreach($paths as $path) {
		$mainpath = $path . $class . ".php";
		if(file_exists($mainpath)) {
			return require_once $mainpath;
		}
	}
});

define("BPcore", "app/code/base/");
define("DS", DIRECTORY_SEPARATOR);
define("PS", PATH_SEPARATOR);
define("BP", dirname(dirname(__FILE__)));
define("US", "_");
define("BS", "/");

$paths = array();
$paths[] = BP . DS . "app" . DS . "code" . DS . "client" . DS;
$paths[] = BP . DS . "app" . DS . "code" . DS . "base" . DS;
$paths[] = BP . DS . "lib" . DS . "lexi" . DS;

$skinPath = array();
$skinPath[] = BP . DS . "skin" . DS . "client" . DS;
$skinPath[] = BP . DS . "skin" . DS . "base" . DS;

Core::regPath( $paths );
Core::regSkinPath( $skinPath );
Core::getSingleton("system/kernel")->autoload();

Class Core {

	/**
	 *	System Kernel
	 */
	protected $kernel;

	/**
	 *	Params
	 */
	public $params = array();

	/**
	 *	Objects Instance
	 */
	public static $objects = array();

 	/**
 	 *	Variable Paths
 	 */
 	public static $paths;

 	/**
 	 *	Skin Paths
 	 */
 	public static $skinPath;

 	/**
 	 *	Url
 	 */
 	public static $url = null;

 	/**
 	 *	Bootsrap
 	 */
 	public function __construct() {
 		// let's check if we need to activate maintenance mode
		if( file_exists(Core::getSingleton("system/config")->getConfig('maintenanceFlagFile')) ) {
			Core::getSingleton("error/error")
				->setMessage("This is under maintenance.<br/>Our service team has been dispatched to bring it back online.")
				->setType(500)
				->new();
			exit();
		}

 		// instantiate the kernel
 		$kernel = Core::getSingleton("system/kernel");

 		// get all the request
 		if(isset($_GET['request'])) {
 			$httpurl = Core::getSingleton("Url/Http");
 			$httpurl->setUrl($_GET['request'])->chkUrl();
 			$this->params = $httpurl->getParams();
 			
 			$dirs = array_filter(glob(BPcore . '*'), 'is_dir');

 			foreach($dirs as $dir) {
 				if($this->params[0] == str_replace(BPcore, "", $dir)) {
 					Core::dispatchError()
 						->setMessage("Sorry this page is reserve for core files only")
 						->setType(403)
 						->new();
 				}
 			}
 		}

 		if(isset($this->params[0])) {
			$kernel->setApp( $this->params[0] );
			unset($this->params[0]);
			if(isset($this->params[1])) {
				$kernel->setController( $this->params[1] ); 
				unset($this->params[1]);
			}

			if(isset($this->params[2])) {
				$kernel->setMethod( $this->params[2] . "Action" );
				unset($this->params[2]);
			}
			$request = Core::getSingleton("url/request");
 			$request->genRequest($this->params);
 		}

 		if(! Core::controllerExist([$kernel->getApp(), $kernel->getController()]) ) {
 			Core::dispatchError()
 				->setMessage("Sorry the page doesn't exist")
 				->setType(404)
 				->exec();
 		}

 		$kernel->setController( Core::getSingleton($kernel->getApp() . "/" . $kernel->getController()) );
 		if(method_exists($kernel->getController(), $kernel->getMethod())) {
 			call_user_func([$kernel->getController(), "loadThemeResource"]);
 			if( method_exists($kernel->getController(), "setup") ) {
 				call_user_func([$kernel->getController(), "setup"]);
 			}
 			call_user_func_array([$kernel->getController(), $kernel->getMethod()], [$this->params]);
 			call_user_func([$kernel->getController(), "render"]);
 		}else {
 			Core::dispatchError()
 				->setMessage("Sorry the page doesn't exist")
 				->setType(404)
 				->exec();
 		}

 	}

 	/**
 	 *	Register paths
 	 *	@var array $path
 	 *	@return
 	 */
 	public static function regPath( $path ) {
 		self::$paths = $path;
 	}

 	/**
 	 *	Register Skin Path
 	 *	@var array $path
 	 *	@return
 	 */
 	public static function regSkinPath( $path ) {
 		self::$skinPath = $path;
 	}

 	/**
 	 *	Get Singleton
 	 *	@var string $controller
 	 *	@return obj $controller
 	 */
 	public static function getSingleton( $controller ) {
 		$controller = explode("/", $controller);
 		$controller = $controller[0] . US . "Controller" . US . $controller[1];
 		if(!array_key_exists($controller, self::$objects)) {
 			self::$objects = self::$objects + array($controller => new $controller);
 		}
 		
 		return self::$objects[$controller];
 	}

 	/**
 	 *	Error Handler
 	 */
 	public static function dispatchError() {
 		return Core::getSingleton("error/error");
 	}

 	/**
 	 *	Get Model
 	 *	@var string $model
 	 *	@return obj $model
 	 */
 	public static function getModel( $model ) {
 		$model = explode("/", $model);
 		$model = $model[0] . "_model_" . $model[1];
 		return new $model;
 	}

 	/**
 	 *	Return the parameters
 	 */
 	public static function getParams() {
 		return self::params;
 	}

 	/**
 	 *	Command 
 	 *	@param string $cmd
 	 *	@return obj $console
 	 */
 	public static function getConsole( $cmd ) {
 		if( strpos($cmd, "/") !== false ) {
 			$cmd = explode("/", $cmd);
 		}else{
 			$cmd = [
 				$cmd,
 				"main"
 			];
 		}
 		$cmd =  "console_" . $cmd[0] . "_" . $cmd[1]; 
 		return new $cmd;
 	}

 	/**
 	 *	Instanciate Migration Object
 	 *	@param string $migration
 	 *	@return obj $migration
 	 */
 	public static function getMigration( $migration ) {
 		$path = BP . DS . "database" . DS . "migration" . DS . $migration . ".php";
 		if(! file_exists($path) ) {
 			return false;
 		}

 		return new $migration;
 	}

 	/**
 	 *	Instanciate Seeder Object
 	 *	@param string $seeder
 	 *	@return obj $seeder
 	 */
 	public static function getSeeder( $seeder ) {
 		$path = BP . DS . "database" . DS . "seeder" . DS . $seeder . ".php";
 		if( file_exists($path) ) {
 			return new $seeder;
 		}
 	}

 	/**
 	 *	Get Base URL
 	 *	@return string $BaseUrl
 	 */
 	public static function getBaseUrl() {
 		$config = Core::getSingleton("system/kernel")->getConfig("system");
 		return $config["baseUrl"];
 	}

 	/**
 	 *	Check if controller exist
 	 *	@param array $cont
 	 *	@return bool
 	 */
 	public static function controllerExist( $cont ){
 		foreach( Core::$paths as $path ){
 			$contPath = $path . $cont[0] . DS . "controller" . DS . $cont[1] . ".php";
 			if( file_exists($contPath) ) {
 				return true;
 			}
 		}
 		return false;
 	}

 	/**
 	 *	Print Variables
 	 *	@param string $str
 	 *	@param bool $file
 	 *	@return void
 	 */
 	public static function log( $str, $string = false, $filename = "system.log" ) {
 		$path = BP . DS . "logs" . DS . $filename;
 		$date = Core::getSingleton("system/date");

 		if( $string || is_object($str) || is_array($str) ) {
 			file_put_contents($path, "====# " . $date->getDate() . " #====" . "\n", FILE_APPEND);
 			file_put_contents($path, print_r($str, true) . "\n", FILE_APPEND);
 			return;
 		}else{
 			file_put_contents($path, $date->getDate() . ": " . $str . "\n", FILE_APPEND);
 			return;
 		}

 		echo "<pre>";
 		print_r($str);
 		echo "</pre>";
 	}

 	/**
 	 *	Instantiate Core Class
 	 *	@return obj Core
 	 */
 	public static function app() {
 		return new Core;
 	}

 	/**
 	 *	Exceptions
 	 */
 	public static function exceptionHandler( $e ) {
 		echo "----- EXCEPTION -----";
 		echo $e->getMessage();
 		return;
 	}

 	/**
 	 *	Errors
 	 */
 	public static function errorHandler( $severity, $message, $filepath = null, $line = 0 ) {
 		echo "----- ERROR -----";
 		echo $message;
 		return;
 	}

 	/**
 	 *	Shutdown
 	 */
 	public static function shutdownHandler() {
 		echo "----- SHUTDOWN -----";
 		self::log( error_get_last() );
 	}

 }