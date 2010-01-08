<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PageConfig {
	
	public $type;
	public $variables;
	
	public $SinglePostID;
	public $listCategoryID;
	public $listTag;
	
	public function __construct($type) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		
		$this->type = $type;
		$this->variables = new PageVariables();
		
	}
		
}

?>