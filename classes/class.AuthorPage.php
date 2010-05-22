<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class AuthorPage extends Page {
	
	private $authorID;
	
	public function SetPageVariables($vars) {
		$this->SetAuthorID($vars['identifier']);
	}
	
	public function SetAuthorID($authorid) {
		if (is_numeric($authorid)) {
			$this->authorID = $authorid;	
		} else {
			$this->authorID = null;	
		}
	}
	
	public function GetAuthorID() {
		return $this->authorID;	
	}
	
	public function ConstructContents() {
		$opt = new Options();
		
		$am = new AuthorManagement();
		$posts = $am->GetCertainAuthor($this->GetAuthorID());
		$this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
		$PageConfig->variables->nf_page_title = 'Posts by ' . $posts[0]->first_name . ' ' . $posts[0]->last_name . ' - ' . $opt->ValueForKey("blog/title");
		$PageConfig->type = 'author';
		
		// render the page
		if ($PageConfig->PostListStyle == 'condensed') {
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
		// $PageConfig->variables->nf_posts = $this->FormatPostListing($posts, $PageConfig);
		
		return $PageConfig;
	}
	
	public function FormatPost($post, $PageConfig) {
			
		$pm = new PostManagement();
		$opt = new Options();
		
		// use default avatar image		
		if (rand(0,1) == 0) {
			$gravatar = 'themes/theme.default/images/default-avatar-female.png';	
		} else {
			$gravatar = 'themes/theme.default/images/default-avatar-male.png';
		}
		
		// attempt to get Gravatar
		$pf = new Packages();
		if ($pf->ImportPackage('Gravatar')) {
		
			// Get Gravatar	
			$grv = new Gravatar($post->email);
			if ($grv->GravatarExists()) {
				$grv->setSize(128);
				$gravatar = $grv;	
			}
		
		}
		
		// Assign author-related tags
		$PageConfig->variables->nf_author_name =		$post->first_name . ' ' . $post->last_name;
		$PageConfig->variables->nf_author_permalink =	$opt->ValueForKey("paths/siteroot") . 'author.php?author=' . $post->id;
		$PageConfig->variables->nf_author_email =		$post->email;
		$PageConfig->variables->nf_author_bio =			$this->FormatMarkdown($post->bio);
		$PageConfig->variables->nf_author_homepage =	$post->homepage;
		$PageConfig->variables->nf_author_avatar =		$gravatar;
		
		// Get and format list of author's posts
		$post_data = $pm->GetPostsByAuthor($post->id); // get posts by given author id
		$postsByAuthor = $post_data['posts'];
		$PageConfig->variables->nf_author_posts = $this->FormatCondensedPosts($postsByAuthor, $PageConfig);
		
		$post_html = $this->OpenTemplate('author', $PageConfig);
		
		return $post_html;
	}
	
}

?>