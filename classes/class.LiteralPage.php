<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class LiteralPage extends Page {
	
	private $pageID;
	
	public function __construct($pageid) {
		$this->SetPageID($pageid);
	}
	
	public function SetPageID($pageid) {
		if (is_numeric($pageid)) {
			$this->pageID = $pageid;	
		} else {
			$this->pageID = null;	
		}
	}
	
	public function GetPageID() {
		return $this->pageID;	
	}
	
	public function ConstructContents() {
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$pam = new PageManagement();
		$posts = $pam->GetCertainPage($this->GetPageID());
		$PageConfig->variables->nf_page_title = $posts[0]->title . ' - ' . $nf['blog']['title'];
		$PageConfig->type = 'page';
		$PageConfig->variables->nf_posts = $this->FormatPostListing($posts, $PageConfig);
		
		return $PageConfig;
	}
	
	
		
}

?>