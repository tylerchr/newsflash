<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class Core {
	
	public function TimeToUniversal($time) {
		require(dirname(__FILE__) . '/../configuration.php');
		$opt = new Options();
		return $time + ($opt->ValueForKey("blog/timezone") * 3600);	
	}
	
	public function TimeFromUniversal($time) {
		require(dirname(__FILE__) . '/../configuration.php');
		$opt = new Options();
		return $time - ($opt->ValueForKey("blog/timezone") * 3600);	
	}
	
	public function CreatePasswordHash($password) {
		return md5($password);	
	}
	
}

?>