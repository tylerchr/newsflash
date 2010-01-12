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
	
	public function FormatPost($post, $PageConfig) {
		
		$core = new Core();
			
		// Assign page-related tags
		$PageConfig->variables->nf_page_id =			$post->id;
		$PageConfig->variables->nf_page_title =		$post->title;
		$PageConfig->variables->nf_page_date =		date("j F Y", $core->TimeFromUniversal($post->date));
		$PageConfig->variables->nf_page_text =		$this->FormatMarkdown($post->text);
		$PageConfig->variables->nf_page_permalink =	$nf['paths']['siteroot'] . 'page.php?page=' . $post->id;
		
		$post_html = $this->OpenTemplate('page', $PageConfig);	
		
		return $post_html;	
	}
	
	
		
}

?>