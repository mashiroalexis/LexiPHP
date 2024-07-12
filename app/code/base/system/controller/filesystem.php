<?php
/**
 * Copyright © Ramon Alexis Celis All rights reserved.
 * See license file for more info.
 */

/**
 *	Filesytem manipulation url
 *	@link http://php.net/manual/en/function.file-get-contents.php
 */
Class System_Controller_Filesystem {

	protected $extension = [
		".php",
		".txt"
	];

	protected $excludeStr = [
		".",
		".."
	];

	/**
	 *	Create directory
	 *	@param string $path
	 *	@return bool $result
	 */
	public function mkdir( $path ) {
		if( $this->dirExist( $path ) ) {
			return false;
		}
		mkdir($path, 0774);
		return true;
	}

	/**
	 *	Check if directory exists
	 *	@param string $path
	 *	@return bool $result
	 */
	public function dirExist( $path ) {
	    // Get canonicalized absolute pathname
		$path = realpath($path);

		// If it exist, check if it's a directory
		return ($path !== false AND is_dir($path)) ? $path : false;
	}

	/**
	 *	Get directory list
	 *	@param string $path
	 *	@return array $result
	 */
	public function getDirList( $path ) {
		if( $this->dirExist( $path ) ) {
			$dirs = [];
			foreach( array_filter(glob($path . '*')) as $dir ) {
				$dirs[] = str_replace($path, "", $dir);
			}
			return $dirs;
		}
		return false;
	}

	/**
	 *	Get directory contents
	 */
	public function getDirContents( $path, $dir = false ) {
		if (is_dir($path)){
			if ($dh = opendir($path)){
				$files = [];
				while (($file = readdir($dh)) !== false){
					if( in_array($file, $this->excludeStr) ) {
						continue;
					}
					if( $dir == false and $this->dirExist( $path . DS . $file ) ) {
						continue;
					}
					$files[] = $file;
				}
				closedir($dh);
				return $files;
			}
		}
		return false;
	}

	/** 
	 *	Create A File
	 */
	public function create( $filename, $content = false, $path = false, $extension = "php" ) {
		if(! $this->isAccessable( $path ) ) {
			return false;
		}

	}

	/**
	 *	Check if we have permission on this path
	 *	@var string $path
	 *	@return bool
	 */
	public function isAccessable( $path ) {
		if( file_exists($path) ) {
			if( is_writable($path) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 *	Check if file or directory exist
	 *	@param $path
	 *	@return bool
	 */
	public function exists( $path ) {

		// check if file exists
		if( file_exists($path) && is_file($path) ) {
			return true;
		}

		// check if directory exist
		if( $this->dirExist( $path ) ) {
			return true;
		}

		return false;
	}
}