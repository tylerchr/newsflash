<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class CategoryPage extends Page {
	
	private $categoryID;
	
	public function SetPageVariables($vars) {
		$this->SetCategoryID($vars['identifier']);	
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
		$opt = new Options();
		
		$pm = new PostManagement();
		$post_data = $pm->GetPostsFromCategory($this->GetCategoryID(), $this->getPageData());
		$posts = $post_data['posts'];
		$this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
		$cm = new CategoryManagement();
		$category = $cm->GetCategoryWithID($this->GetCategoryID());
		if (!$category) {
                    $cname = 'Unfiled';	
		} else {
                    $cname = $category->name;
                }
                $PageConfig = new PageConfig('category');
		$PageConfig->variables->nf_page_title = $cname . ' - ' . $opt->ValueForKey("blog/title");
		
		// render the page
		if (isset($PageConfig->PostListStyle) && $PageConfig->PostListStyle == 'condensed') {
			$PageConfig->variables->nf_posts = $this->FormatCondensedPosts($posts, $PageConfig);
		} else {
			if (count($posts) > 0) {
				foreach ($posts as $single_post) {
					$PageConfig->variables->nf_posts .= $this->FormatPost($single_post, $PageConfig);
				}
			} else {
				$PageConfig->variables->nf_posts = $opt->ValueForKey("error/no_posts");	
			}
		}
		
		return $PageConfig;
	}
		
}

?>