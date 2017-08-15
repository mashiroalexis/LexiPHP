<?php
/**
 * Copyright © Ramon Alexis Celis All rights reserved.
 * See license file for more info.
 */


Class Frontend_Controller_Action {

	/**
	 *	Block
	 */
	public $blocks = array();

	/**
	 *	CSS
	 */
	public $css = array();

	/**
	 *	Javascript
	 */
	public $js = array();

	/**
	 *	Path to Blocks
	 */
	public $paths = array();

	/**
	 *	Path to CSS
	 */
	public $pathCss = array();

	/**
	 *	Main Block
	 */
	public $mainBlock = "";

	/**
	 *	Page Title
	 */
	public $pageTitle = "LexiPHP Framework";

	/**
	 *	Load defaults [Deprecated]
	 */
	public function setDefault() {
		$this->linkCss("http://fonts.googleapis.com/icon?family=Material+Icons");
		$this->setCss("default/materialize");
		$this->setCss("default/style");
		$this->setJs("default/jquery-2.1.1.min");
		$this->setJs("default/materialize");
		$this->setJs("default/init");
	}

	/**
	 *	Get Child Block
	 *	@var string $child
	 *	@return $blocks
	 */
	public function getChildBlock( $child ) {
		$key = explode("/", $child);
		$path = Core::$paths[0] . $key[0] . DS . "view" . DS . "blocks" . DS . $key[1] . ".phtml";
		if( file_exists($path) ) {
			return include $path;
		}
		return false;
	}

	/**
	 *	Get Images
	 *	@var string $image
	 *	@return string $image
	 */
	public function getImage( $image ) {
		$img = explode("/", $image);
		$baseUrl = Core::getSingleton("system/config")->getBaseUrl();
		foreach( Core::$skinPath as $path ) {
			$imagePath = $path . $img[0] . DS . "images" . DS . $img[1];
			if( file_exists($imagePath) ) {
				$imagePath = str_replace(BP . DS, $baseUrl, $imagePath);
				return $imagePath;
			}
		}
	}

	/**
	 *	load the resources from theme
	 */
	public function loadThemeResource() {
		$theme = Core::getSingleton("system/kernel")->getConfig("theme");
		if( isset($theme["css"]) ) {
			foreach( $theme["css"] as $css ) {
				$this->setCss( $css );
			}
		}

		if( isset($theme["js"]) ) {
			foreach( $theme["js"] as $js ) {
				$this->setJs( $js );
			}
		}
		return;
	}

	/**
	 *	Set CSS [new Update]
	 */
	public function setCss($varCss) {
		$varCss = explode(BS, $varCss);
		$config = Core::getSingleton("system/config");
		$paths = $config->getSkinPath();
		$baseurl = $config->getBaseUrl();
		foreach($paths as $cssPath) {
			$fileLoc = BP . DS . "skin" . DS . $cssPath . DS . $varCss[0] . DS . "css" . DS . $varCss[1] . ".css";
			if(file_exists($fileLoc)) {
				$this->css[] = "<link rel='stylesheet' href='" . $baseurl . "skin" . BS . $cssPath . BS . $varCss[0] . BS . "css" . BS . $varCss[1] . ".css'>";			
			}
		}
	}

	/**
	 *	Set JS [New Update]
	 */
	public function setJs($varJs) {
		$varJs = explode(BS, $varJs);
		$config = Core::getSingleton("system/config");
		$paths = $config->getSkinPath();
		$baseurl = $config->getBaseUrl();
		foreach($paths as $jsPaths) {
			$fileLoc = BP . DS . "skin" . DS . $jsPaths . DS . $varJs[0] . DS . "js" . DS . $varJs[1] . ".js";
			// if( strpos( $fileLoc, US ) !== false ) {
			// 	$fileLoc = str_replace(US, DS, $fileLoc);
			// 	$varJs[1] = str_replace(US, DS, $varJs[1]);
			// }
			if(file_exists($fileLoc)) {
				$this->js[] = "<script src='" . $baseurl . "skin" . BS . $jsPaths . BS . $varJs[0] . BS . "js" . BS . $varJs[1] . ".js'></script>";
			}
		}
	}

	/**
	 * Set CSS [Deprecated]
	 */
	// public function setBaseCss($varCss) {
	// 	$varCss = explode(BS, $varCss);
	// 	$dir = Core::getSingleton("system/config")->loadConfigFile()->frontend->directory;
	// 	$sysConfig = Core::getSingleton("system/config")->loadConfigFile();
	// 	$baseurl = $sysConfig->system->url;
	// 	$this->css[] = "<link rel='stylesheet' href='" . $baseurl . $dir->skin . BS . $dir->base . BS . $varCss[0] . BS . $dir->css . BS . $varCss[1] . ".css'>";
	// }

	/**
	 *	Link external css file
	 */
	public function linkCss($varLinkCss) {
		$this->css[] = '<link href="'.$varLinkCss.'" rel="stylesheet">';
	}

	/**
	 *	Link External JS
	 */
	public function linkJs($varJs) {
		$this->js[] = '<script src="' . $varJs . '"></script>';
	}

	/**
	 *	Set JS [Deprecated]
	 */
	// public function setBaseJs($varJs) {
	// 	$varJs = explode(BS, $varJs);
	// 	$dir = Core::getSingleton("system/config")->loadConfigFile()->frontend->directory;
	// 	$sysConfig = Core::getSingleton("system/config")->loadConfigFile();
	// 	$baseurl = $sysConfig->system->url;
	// 	$this->js[] = "<script src='" . $baseurl . $dir->skin . BS . $dir->base . BS . $varJs[0] . BS . $dir->js . BS . $varJs[1] . ".js'></script>";
	// }

	/**
	 *	Get Images from Skin
	 */
	public function getBaseImage($varImage) {
		$varImage = explode(BS, $varImage);
		$dir = Core::getSingleton("system/config")->loadConfigFile()->frontend->directory;
		$sysConfig = Core::getSingleton("system/config")->loadConfigFile();
		$baseurl = $sysConfig->system->url;
		return $baseurl . $dir->skin . BS . $dir->base . BS . $varImage[0] . BS . $dir->images . BS . $varImage[1];
	}

	/**
	 *	Set Blocks
	 */
	public function setBlock( $block ) {
		$block = explode(BS, $block);
		$path = Core::$paths[0] . $block[0] . DS . "view" . DS . $block[1] . ".phtml";
		if( file_exists($path) ) {
			$this->blocks[] = $path;
		}
		return false;
	}

	/**
	 *	Create Url Links
	 */
	public function genLink($varUrl) {
		$baseurl = Core::getSingleton("system/config")->loadConfigFile()->system->url;
		return $baseurl . $varUrl;
	}

	/**
	 *	Get the block and insert
	 */
	public function getBlock( $block = false ) {
		if( $block ) {
			$blocks = explode(BS, $block);
			$blockPath = Core::$paths[0] . $blocks[0] . DS . "view" . DS . $blocks[1] . ".phtml";
			if( file_exists($blockPath) ) {
				return include $blockPath;
			}
		}
		return false;
	}

	/**
	 *	Redirect
	 */
	public function _redirect( $urlKey ) {
		header("location: " . $urlKey);
		exit();
	}

	/**
	 *	Render all the blocks
	 */
	public function render() {
		return include dirname(dirname(__FILE__)) . DS . "view" . DS . "main.phtml";
	}

	/**
	 *	Clear the blocks [pending removal]
	 */
	public function clear() {
		$this->css = null;
		$this->js = null;
		$this->blocks = null;
		return $this;
	}

	/**
	 *	set Page title
	 *	@var string $title
	 *	@return null
	 */
	public function setPageTitle( $title ) {
		$this->pageTitle = $title;
		return;
	}

	/**
	 *	Get Page Title
	 *	@return string $pageTitle
	 */
	public function getPageTitle() {
		return $this->pageTitle;
	}

	/**
	 *	Get Css
	 *	@return array $this->css
	 */
	public function getCss() {
		return $this->css;
	}

	/**
	 *	Get Js
	 *	@return array $this->js
	 */
	public function getJs() {
		return $this->js;
	}

	/**
	 *	Get blocks
	 *	@return array $this->blocks
	 */
	public function getBlocks() {
		return $this->blocks;
	}

	/**
	 *	Middleware
	 *	@param string $name
	 *	@return
	 */
	public function middleware( $name ) {
		return $middleware = new $name;
	}
}