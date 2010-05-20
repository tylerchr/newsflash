<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class ListPage extends Page {
	
	private $theme;
	
	public function ConstructContents() {
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$pm = new PostManagement();
		$pageNumber = $this->getPageData();
		$post_data = $pm->GetPosts(null, $this->getPageData());
		$posts = $post_data['posts'];
		$this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
		$PageConfig->variables->nf_page_title = $nf['blog']['title'];
		
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