<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class TagPage extends Page {
	
	private $tag;
	
	public function __construct($tag) {
		$this->SetTag($tag);
	}
	
	public function SetTag($tag) {
		if (is_string($tag)) {
			$this->tag = $tag;	
		} else {
			$this->tag = NULL;
		}
	}
	
	public function GetTag() {
		return $this->tag;	
	}
	
	public function ConstructContents() {
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$pm = new PostManagement();
		$posts = $pm->GetPostsTaggedWith($this->GetTag());
		$PageConfig->variables->nf_page_title = 'Tagged with \'' . $PageConfig->listTag . '\' - ' . $nf['blog']['title'];
		$PageConfig->variables->nf_posts = $this->FormatPostListing($posts, $PageConfig);
		
		return $PageConfig;
	}
	
	
		
}

?>