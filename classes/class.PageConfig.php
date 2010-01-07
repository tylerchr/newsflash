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
	public $listTag;
	
	public function __construct($type) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		
		$this->type = $type;
		$this->tags = array(
			'%nf_blog_title%' => $nf['blog']['title'],
			'%nf_blog_subtitle%' => $nf['blog']['subtitle'],
			'%nf_title%' => "",
			'%nf_headscape%' => "",
			'%nf_search_bar%' => '<form action="search.php" method="get">
					<input type="search" name="q" />
					<input type="submit" value="Go" />
				</form>',
			'%dev_content%' => "Content!",
			'%nf_end%' => "");
		
	}
		
}

?>