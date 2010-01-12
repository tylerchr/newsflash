<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class CategoryPage extends Page {
	
	private $categoryID;
	
	public function __construct($pageid) {
		$this->SetCategoryID($pageid);
	}
	
	public function SetCategoryID($pageid) {
		if (is_numeric($pageid)) {
			$this->categoryID = $pageid;	
		} else {
			$this->categoryID = null;	
		}
	}
	
	public function GetCategoryID() {
		return $this->categoryID;	
	}
	
	public function ConstructContents() {
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$pm = new PostManagement();
		$posts = $pm->GetPostsFromCategory($this->GetCategoryID());
		$cm = new CategoryManagement();
		$category = $cm->GetCategoryWithID($this->GetCategoryID());
		$cname = $category->name;
		if (strlen($cname) == 0) {
			$cname = 'Unfiled';	
		}
		$PageConfig->variables->nf_page_title = $cname . ' - ' . $nf['blog']['title'];
		
		// render the page
		if ($PageConfig->PostListStyle == 'condensed') {
			$PageConfig->variables->nf_posts = $this->FormatCondensedPosts($posts, $PageConfig);
		} else {
			if (count($posts) > 0) {
				foreach ($posts as $single_post) {
					$PageConfig->variables->nf_posts .= $this->FormatPost($single_post, $PageConfig);
				}
			} else {
				require(dirname(__FILE__) . '/../configuration.php');
				$PageConfig->variables->nf_posts = $nf['error']['no_posts'];	
			}
		}
		
		return $PageConfig;
	}
		
}

?>