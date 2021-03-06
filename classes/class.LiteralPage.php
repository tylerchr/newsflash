<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class LiteralPage extends Page {
	
	private $pageID;
	
	public function SetPageVariables($vars) {
		$this->SetPageID($vars['identifier']);
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
		
		$opt = new Options();
		
		$pam = new PageManagement();
		$posts = $pam->GetCertainPage($this->GetPageID());
		// $this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
                $PageConfig = new PageConfig('literal');
		$PageConfig->variables->nf_page_title = $posts[0]->title . ' - ' . $opt->ValueForKey("blog/title");
		$PageConfig->type = 'page';
		
		// render the page
		if (isset($PageConfig->PostListStyle) && $PageConfig->PostListStyle == 'condensed') {
			$PageConfig->variables->nf_posts = $this->FormatCondensedPosts($posts, $PageConfig);
		} else {
			if (count($posts) > 0) {
				foreach ($posts as $single_post) {
					$PageConfig->variables->nf_posts .= $this->FormatPost($single_post, $PageConfig);
				}
			} else {
				require(dirname(__FILE__) . '/../configuration.php');
				$PageConfig->variables->nf_posts = $opt->ValueForKey("error/no_posts");
			}
		}
		
		return $PageConfig;
	}
	
	public function FormatPost($post, $PageConfig, $highlight=array()) {
		
		$core = new Core();
		$opt = new Options();
			
		// Assign page-related tags
		$PageConfig->variables->nf_page_id =			$post->id;
		$PageConfig->variables->nf_page_title =		$post->title;
		$PageConfig->variables->nf_page_date =		date("j F Y", $core->TimeFromUniversal($post->date));
		$PageConfig->variables->nf_page_text =		$this->FormatMarkdown($post->text);
		$PageConfig->variables->nf_page_permalink =	$opt->ValueForKey("paths/siteroot") . 'page.php?page=' . $post->id;
		
		$post_html = $this->OpenTemplate('page', $PageConfig);	
		
		return $post_html;	
	}
	
	
		
}

?>