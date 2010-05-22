<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class mysql {
	
	public $mysqli;
	
	public function __construct() {
		$this->Connect();
	}
	
	public function Connect() {
		
		$opt = new Options();
		
		$this->mysqli = new mysqli($opt->ValueForKey("database/host"), $opt->ValueForKey("database/username"), $opt->ValueForKey("database/password"), $opt->ValueForKey("database/database"));
		if (mysqli_connect_errno()) {
			$this->mysqli = null;
			return mysqli_connect_error();
		} else {
			return true;	
		}
	}
		
}

?>