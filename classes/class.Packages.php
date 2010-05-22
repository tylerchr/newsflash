<?php

/* packages.php */

class Packages {
	
	/*	- - - - -
		PUBLIC PACKAGE LISTINGS
		- - - - - */
	
	private $basicPackages;
		// Basic files that get auto added to every page, such as css or javascript
	private $knottyPackages;
		// (aka plugin) A package is knotty if a .php file of the same name as the
		// package exists within
	private $packageDirectory;
		// Directory where packages are stored
	
	/*	- - - - -
		HAVING TO DO WITH PREFIX LOADING
		- - - - - */
	
	public $bannedPrefixes;
		// File prefixes which do not get checked or included in package listings
	public $supportedFileFormats;
		// Which file formats will be retrieved as basic packages and sent
		// to the interface builder to be included, such as css or javascript
		
	/*	- - - - -	
		DEVELOPMENT / DEBUG VARIABLES
		- - - - - */
		
	private $debugging; // dev-tag
	
	function __construct() {
		require("configuration.php");
		$opt = new Options();
		$this->packageDirectory = $opt->ValueForKey("paths/packages");
		
		$this->bannedPrefixes = array(".", ":"); // no UNIX hidden files or stupid Apple :2e files
		$this->supportedFileFormats = array("css", "js");
		
		$this->debugging = false; // dev-tag
		
		$this->GetEnabledPackages();
		$this->SortPackages();
	}
	
	public function ImportPackage($package) {
		
		if ($this->PackageEnabled($package) && (!class_exists($package) && !function_exists($package))) {
			$path = $this->ScriptForPackage($package);
			require_once($path);
		}
			
		if (class_exists($package) || function_exists($package)) {
			return true;
		}
		
		return false;	
			
	}
	
	public function ScriptForPackage($package) {
		$opt = new Options();
		$path = $opt->ValueForKey("paths/absolute") . 'packages/pkg.' . $package . '/' . $package . '.php';	
		if (file_exists($path)) {
			return $path;
		}
		
		return false;
	}
	
	public function GetEnabledPackages() {

		// Automatically enable all packages in the packages directory
		$handle = opendir($this->packageDirectory);
		while ($file = readdir($handle)) {
			if (substr($file, 0, 4) == "pkg.") {
				$this->packages[] = $file;
			}
		}
		closedir($handle);
		
	}
	
	public function PackageEnabled($pkg) {
		
		// Checks to see if the given package is enabled
		if (in_array('pkg.' . $pkg, $this->basicPackages) || in_array('pkg.' . $pkg, $this->knottyPackages)) {
			return true;
		} else {
			return false;	
		}
	}
	
	public function SortPackages() {
	
		foreach ($this->packages as $value) {
			// Check if there's a PHP script to include
			$pathname = $this->packageDirectory . $value . "/" . substr($value, 4) . ".php";
			if (file_exists($pathname)) {
				$this->log("[knotty : " . $value . "]");
				// This is a plugin, not just a basic package!
				$this->knottyPackages[] = $value;
				
			} else {
				
				$this->log("[basic : " . $value . "]");
				$this->basicPackages[] = $value;
				
			}
		}
		
	}
	
	public function GetBasicPackages() {
		foreach ($this->basicPackages as $value) {
			$packages[] = $value;
		}
		return $packages;
	}
	
	public function GetKnottyPackages() {
		foreach ($this->knottyPackages as $value) {
			$packages[] = $value;
		}
		return $packages;
	}
	
	public function GetContentsOfPackage($package) {
		$files = scandir($this->packageDirectory . "/" . $package);
		foreach ($files as $filename) {
			$ext = substr(strrchr($filename, '.'), 1);
			if (	!in_array(substr($filename, 0, 1), $this->bannedPrefixes) &&
					in_array($ext, $this->supportedFileFormats)) {
						
				$contents[] = $filename;	
			}
		}
		return $contents;
	}
	
	public function GetBasicPackageContents() {
		$basic_packages = $this->GetBasicPackages();
		foreach ($basic_packages as $value) {
			$basics[$value] = $this->GetContentsOfPackage($value);
		}
		return $basics;
	}
	
	/* ///////////// DEVELOPER FUNCTIONS ///////////// */
	
	public function enableDebugging() {
		$this->debugging = true;	
	}
	
	public function log($text) {
		if ($this->debugging == true) {
			echo $text . "<br />";	
		}	
	}
}

?>