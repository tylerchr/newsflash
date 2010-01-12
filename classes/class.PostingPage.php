<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PostingPage extends Page {
	
	private $postID;
	
	public function __construct($postid) {
		$this->SetPostID($postid);
	}
	
	public function SetPostID($postid) {
		if (is_numeric($postid)) {
			$this->postID = $postid;	
		} else {
			$this->postID = null;	
		}
	}
	
	public function GetPostID() {
		return $this->postID;	
	}
	
	public function ConstructContents() {
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$pm = new PostManagement();
		$posts = $pm->GetCertainPost($this->GetPostID());
		$PageConfig->variables->nf_page_title = $posts[0]->title . ' - ' . $nf['blog']['title'];
		$PageConfig->variables->nf_posts = $this->FormatPostListing($posts, $PageConfig);
		
		return $PageConfig;
	}
	
	
		
}

?>