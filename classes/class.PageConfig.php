<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PageConfig {
	
	public $type;
	public $tags;
	
	public $SinglePostID;
	
	public function __construct($type) {
		
		$this->type = $type;
		$this->tags = array(
			'%nf_title%' => "",
			'%nf_headscape%' => "",
			'%dev_content%' => "Content!",
			'%nf_end%' => "<hr /a><span style='font-family:Verdana; font-size:0.9em; background-color:#FFFF99; margin:10px;'>End of the page</span>");
		
	}
		
}

?>