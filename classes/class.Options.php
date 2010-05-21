<?php

class Options {
	
	//
	// public methods
	//
	
	public function SetValueForKey($value, $key) {
		// we don't modify the configuration.php file, so we'll write it to database
		return $this->_DatabaseSetValueForKey($value, $key);
	}
	
	public function ValueForKey($key) {
		
		/*
		keys can be multidimensional with slashes using the format "parent/middleman/child"
		for example, $nf['blog']['title'] in the configuration file is "blog/title"
		*/
		
		// try getting it from configuration.php
		if ($this->_ConfigFileContainsKey($key))
			return $this->_ConfigFileValueForKey($key);
			
		// failing that, try for the database
		if ($this->_DatabaseContainsKey($key))
			return $this->_DatabaseValueForKey($key);
			
		// failing that, report failure
		return false;
		
			
	}
	
	public function ContainsKey($key) {
		return ($this->_ConfigFileContainsKey($key) || $this->_DatabaseContainsKey($key));
	}
	
	public function ContainsValue($value) {
		return ($this->_ConfigFileContainsValue($value) || $this->_DatabaseContainsValue($value));
	}
	
	//
	// private (configuration.php-based) methods
	//
	
	private function _ConfigFileSetValueForKey($value, $key) {
		
		// this isn't actually done; we won't ever write to the user's
		// configuration.php file
		
		return false;
			
	}
	
	private function _ConfigFileValueForKey($key) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		
		$key_parts = explode("/", $key);
		
		$fail = false;
		$base = $nf;
		foreach ($key_parts as $part) {
			if (isset($base[$part])) {
				$base = $base[$part];
			} else {
				$fail = true;	
			}
		}
		
		if ($fail) {
			return false;
		} else {
			if (is_numeric($base)) {
				return intval($base);
			} else {
				return $base;
			}
		}
		
	}
	
	private function _ConfigFileContainsKey($key) {
	
		// provided for compatibility
		return $this->_ConfigFileValueForKey($key);
		
	}
	
	private function _ConfigFileContainsValue($value) {
	
		// unimplemented, can't see why this would ever be useful
		// but provided for parity with the _Database methods
		
	}
	
	//
	// private (database-based) methods
	//
	
	private function _DatabaseSetValueForKey($value, $key) {
		
		if ($this->ContainsKey($key))
			return false;
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('INSERT INTO ' . $nf['database']['table_prefix'] . $nf['database']['options_table'] . ' (option_key, option_value) VALUES (?, ?)')) {
			
			$stmt->bind_param("ss", $key, $value);
			if ($stmt->execute()) {
				return true;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
		
	}
	
	private function _DatabaseValueForKey($key) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT option_value FROM ' . $nf['database']['table_prefix'] . $nf['database']['options_table'] . ' WHERE option_key = ?')) {
			
			$stmt->bind_param("s", $key);
			if ($stmt->execute()) {
				$stmt->bind_result($value);
				while ($stmt->fetch()) {
					$values[] = $value;	
				}
			} else {
				echo $sql->mysqli->error();	
			}
			
			// return the result	
			if (count($values) > 0) {
				// returns the first result, even if multiple are found
				return $values[0];
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
		
	}
	
	private function _DatabaseContainsKey($key) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT COUNT(*) FROM ' . $nf['database']['table_prefix'] . $nf['database']['options_table'] . ' WHERE option_key = ?')) {
			
			$stmt->bind_param("s", $key);
			if ($stmt->execute()) {
				
				$stmt->bind_result($count);
				$stmt->fetch();
				
				if ($count <= 0) {
					return false;
				}
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		// assume that the key exists
		return true;
		
	}
	
	private function _DatabaseContainsValue($value) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT COUNT(*) FROM ' . $nf['database']['table_prefix'] . $nf['database']['options_table'] . ' WHERE option_value = ?')) {
			
			$stmt->bind_param("s", $value);
			if ($stmt->execute()) {
				
				$stmt->bind_result($count);
				$stmt->fetch();
				
				if ($count > 0) {
					return true;
				}
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		// assume that the value does not exist
		return false;
		
	}
		
}

?>