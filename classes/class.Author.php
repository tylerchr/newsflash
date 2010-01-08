<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class Author {
	
	public $id;
	public $first_name;
	public $last_name;
	public $password_hash;
	public $email;
	public $bio;
	public $created_at;
	
	public function __construct() {
		
		// Initialize a blank author
		
		$this->id =				-1;	
		$this->first_name =		NULL;
		$this->last_name =		NULL;
		$this->password_hash =	'';
		$this->email =			'';
		$this->bio =			'';
		$this->created_at =		time();
	}
		
}

?>