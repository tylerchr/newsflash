<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class ListPage extends Page {
	
	private $theme;
	
	public function ConstructContents() {
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$pm = new PostManagement();
		$posts = $pm->GetPosts();
		$PageConfig->variables->nf_page_title = $nf['blog']['title'];
		$PageConfig->variables->nf_posts = $this->FormatPostListing($posts, $PageConfig);
		
		return $PageConfig;
	}
	
	
		
}

?>