<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class AuthorPage extends Page {
	
	private $authorID;
	
	public function __construct($authorid) {
		$this->SetAuthorID($authorid);
	}
	
	public function SetAuthorID($authorid) {
		if (is_numeric($authorid)) {
			$this->authorID = $authorid;	
		} else {
			$this->authorID = null;	
		}
	}
	
	public function GetAuthorID() {
		return $this->authorID;	
	}
	
	public function ConstructContents() {
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$am = new AuthorManagement();
		$posts = $am->GetCertainAuthor($this->GetAuthorID());
		$PageConfig->variables->nf_page_title = 'Posts by ' . $posts[0]->first_name . ' ' . $posts[0]->last_name . ' - ' . $nf['blog']['title'];
		$PageConfig->type = 'author';
		$PageConfig->variables->nf_posts = $this->FormatPostListing($posts, $PageConfig);
		
		return $PageConfig;
	}
	
	
		
}

?>