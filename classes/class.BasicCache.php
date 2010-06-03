<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class BasicCache {
	
	private $resource_identifier;
	
	public function __construct() {
		
	}
	
	public function isCachingEnabled() {
		if ($enabled = $this->_ConfigValueForKey('cache/enabled')) {
			return $enabled;	
		} else {
			return true;	
		}
	}
	
	// SAVING AND LOADING
	
	public function isResourceCached($id) {
		return ($this->_doesCacheFileExist($id) && $this->isResourceValid($id));
	}
	
	public function isResourceValid($id) {
		// meaning, is the resource non-expired
		
		$expiration = $this->_ResourceExpirationDate($id);
		if ($expiration > time()) {
			$ttl = $expiration - time();
			$this->debuglog('not yet expired, expires in ' . $ttl . ' seconds');
			return true;	
		} else {
			$ttl = time() - $expiration;
			$this->debuglog('expired ' . $ttl . ' seconds ago');
			return false;	
		}
			
	}
	
	public function SaveCacheResource($id, $page) {
		$this->debuglog('caching new data');
		$path = $this->AbsolutePathOfResource($id);
		if (file_put_contents($path, $page))
			return true;
		else
			return false;
	}
	
	public function GetCacheResource($id) {
		$path = $this->AbsolutePathOfResource($id);
		if ($this->isResourceCached($id) && $content = file_get_contents($path)) {
			return $content;
		}

		return false;
	}
	
	/*
	 *	PRIVATE METHODS
	 */
	
	private function _doesCacheFileExist($id) {
		$path = $this->AbsolutePathOfResource($id);
		return file_exists($path);
	}
	
	private function _ResourceModifiedDate($id) {
		if ($this->_doesCacheFileExist($id)) {
			return filemtime($this->AbsolutePathOfResource($id));
		}
	}
	
	private function _ResourceExpirationDate($id) {
		$expiration = $this->_ResourceModifiedDate($id) + $this->timeToLive();
		return $expiration;
	}
	
	// READING CONFIGURATION OPTIONS
	// unlike the usual, we want to do this from the config file first and fall back on
	// the standard Options class cache/database second so that we can avoid hitting the
	// database if possible
	
	private function timeToLive() {
		if ($ttl = $this->_ConfigValueForKey('cache/ttl')) {
			return $ttl;
		} else {
			// if TTL can't be retrieved, default to one hour
			return 3600;
		}
	}
	
	private function cachePath() {
		// get absolute path
		$abs = $this->_ConfigValueForKey('paths/absolute');
		$cache = $this->_ConfigValueForKey('paths/cache');		
		return $abs . $cache;
	}
	
	private function AbsolutePathOfResource($id) {
		return $this->cachePath() . '/' . base64_encode($id) . '.html';
	}
	
	private function _ConfigValueForKey($key) {
		$parts = explode('/', $key);
		
		// check the config file first
		require(dirname(__FILE__) . '/../configuration.php');	
		if (isset($nf[$parts[0]][$parts[1]])) {
			return $nf[$parts[0]][$parts[1]];
		} else {			
			// else check the Options class
			$this->debuglog('[' . $key . '] Not in the config file, asking Options...');	
			$opt = new Options();
			return $opt->ValueForKey($key);
		}
		
		return false;
	}
	
	// DEBUGGING
	private function debuglog($log) {
		// echo '[cache] ' . $log . '<br />';	
	}
		
}

?>