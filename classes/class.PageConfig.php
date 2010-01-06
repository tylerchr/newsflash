<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PageConfig {
	
	public $type;
	public $tags;
	
	public $SinglePostID;
	public $listCategoryID;
	
	public function __construct($type) {
		
		$this->type = $type;
		$this->tags = array(
			'%nf_title%' => "",
			'%nf_headscape%' => "",
			'%dev_content%' => "Content!",
			'%nf_end%' => "");
		
	}
		
}

?>