<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class SearchPage extends Page {
	
	private $query;
	
	public function __construct($query) {
		$this->SetQuery($query);
	}
	
	public function SetQuery($query) {
		if (is_string($query)) {
			$this->query = $query;	
		} else {
			$this->query = NULL;
		}
	}
	
	public function GetQuery() {
		return $this->query;	
	}
	
	public function ConstructContents() {
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$pm = new PostManagement();
		$posts = $pm->GetPostsMatchingQuery($this->GetQuery());
		$PageConfig->variables->nf_page_title = 'Results for \'' . $PageConfig->searchQuery . '\' - ' . $nf['blog']['title'];
		$PageConfig->variables->nf_posts = $this->FormatPostListing($posts, $PageConfig);
		
		return $PageConfig;
	}
	
	
		
}

?>