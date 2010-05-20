<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class TagPage extends Page {
	
	private $tag;
	
	public function SetPageVariables($vars) {
		$this->SetTag($vars['identifier']);
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
		$post_data = $pm->GetPostsTaggedWith($this->GetTag(), $this->getPageData());
		$posts = $post_data['posts'];
		$this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
		$PageConfig->variables->nf_page_title = 'Tagged with \'' . $this->GetTag() . '\' - ' . $nf['blog']['title'];
		
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