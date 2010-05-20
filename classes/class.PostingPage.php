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
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$pm = new PostManagement();
		$post_data = $pm->GetCertainPost($this->GetPostID());
		$posts = array($post_data['posts'][52]);
		$this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
		echo print_r($post, true);
		$PageConfig->variables->nf_page_title = $posts[0]->title . ' - ' . $nf['blog']['title'];
		$PageConfig->variables->nf_posts = $this->FormatPost($posts[0], $PageConfig);
		
		return $PageConfig;
	}
	
	
		
}

?>