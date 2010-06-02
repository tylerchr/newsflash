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
		
		require(dirname(__FILE__) . '/../configuration.php');		
		$this->mysqli = new mysqli($nf['database']['host'], $nf['database']['username'], $nf['database']['password'], $nf['database']['database']);
		if (mysqli_connect_errno()) {
			$this->mysqli = null;
			return mysqli_connect_error();
		} else {
			return true;	
		}
	}
		
}

?>