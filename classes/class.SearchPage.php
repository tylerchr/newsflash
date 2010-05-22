<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class SearchPage extends Page {
	
	private $query;
	
	public function SetPageVariables($vars) {
		$this->SetQuery($vars['query']);
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
		
		$opt = new Options();
		
		$pm = new PostManagement();
		$post_data = $pm->GetPostsMatchingQuery($this->GetQuery(), $this->getPageData());
		$posts = $post_data['posts'];
		$this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
		$PageConfig->variables->nf_page_title = 'Results for \'' . $PageConfig->searchQuery . '\' - ' . $opt->ValueForKey("blog/title");
		
		// render the page
		if ($PageConfig->PostListStyle == 'condensed') {
			$PageConfig->variables->nf_posts = $this->FormatCondensedPosts($posts, $PageConfig);
		} else {
			if (count($posts) > 0) {
				foreach ($posts as $single_post) {
					$PageConfig->variables->nf_posts .= $this->FormatPost($single_post, $PageConfig, array($this->query));
				}
			} else {
				require(dirname(__FILE__) . '/../configuration.php');
				$PageConfig->variables->nf_posts = $opt->ValueForKey("error/no_posts");	
			}
		}
		// $PageConfig->variables->nf_posts = $this->FormatPostListing($posts, $PageConfig);
		
		return $PageConfig;
	}
	
	
		
}

?>