<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PostingPage extends Page {
	
	private $postID;
	
	public function SetPageVariables($vars) {
		$this->SetPostID($vars['identifier']);
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
		$opt = new Options();
		
		$pm = new PostManagement();
		$post_data = $pm->GetCertainPost($this->GetPostID());
		$posts = array($post_data['posts'][$this->GetPostID()]);

		$this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
		$PageConfig->variables->nf_page_title = $posts[0]->title . ' - ' . $opt->ValueForKey("blog/title");
		$PageConfig->variables->nf_posts = $this->FormatPost($posts[0], $PageConfig);
		
		return $PageConfig;
	}
	
	
		
}

?>