<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class Page {
	
	public $id;
	public $short_title;
	public $title;
	public $slug;
	public $text;
	public $date;
	
	public function __construct() {
		
		// Initialize a blank page
		
		$this->id =				-1;	
		$this->short_title =	NULL;
		$this->title =			'';
		$this->slug =			'';
		$this->date =			time();
	}
		
}

?>