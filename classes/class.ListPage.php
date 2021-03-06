<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class ListPage extends Page {
	
	private $theme;
	
	public function ConstructContents() {
		$opt = new Options();
		
		$pm = new PostManagement();
		$pageNumber = $this->getPageData();
		$post_data = $pm->GetPosts(null, $this->getPageData());
		$this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
                $PageConfig = new PageConfig('list');
		$PageConfig->variables->nf_page_title = $opt->ValueForKey("blog/title");
		
		// render the page
		if (isset($PageConfig->PostListStyle) && $PageConfig->PostListStyle == 'condensed') {
			$PageConfig->variables->nf_posts = $this->FormatCondensedPosts($post_data['posts'], $PageConfig);
		} else {
			if (count($post_data['posts']) > 0) {
				foreach ($post_data['posts'] as $single_post) {
					$PageConfig->variables->nf_posts .= $this->FormatPost($single_post, $PageConfig);
				}
			} else {
				require(dirname(__FILE__) . '/../configuration.php');
				$PageConfig->variables->nf_posts = $opt->ValueForKey("error/no_posts");
			}
		}
		
		return $PageConfig;
	}
		
}

?>