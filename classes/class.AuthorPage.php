<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class AuthorPage extends Page {
	
	private $authorID;
	
	public function __construct($authorid) {
		$this->SetAuthorID($authorid);
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
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$am = new AuthorManagement();
		$posts = $am->GetCertainAuthor($this->GetAuthorID());
		$PageConfig->variables->nf_page_title = 'Posts by ' . $posts[0]->first_name . ' ' . $posts[0]->last_name . ' - ' . $nf['blog']['title'];
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
				require(dirname(__FILE__) . '/../configuration.php');
				$PageConfig->variables->nf_posts = $nf['error']['no_posts'];	
			}
		}
		// $PageConfig->variables->nf_posts = $this->FormatPostListing($posts, $PageConfig);
		
		return $PageConfig;
	}
	
	public function FormatPost($post, $PageConfig) {
			
		$pf = new PackageFinder();
		$pm = new PostManagement();
		
		if ($pf->PackageEnabled('Gravatar') && !class_exists('Gravatar')) {
			$gravatar_path = $nf['paths']['absolute'] . 'packages/pkg.Gravatar/Gravatar.php';
			require($gravatar_path);
		}
		
		// Get Gravatar	
		$grv = new Gravatar($post->email);
		if ($grv->GravatarExists()) {
			$grv->setSize(128);
			$gravatar = $grv;	
		} else {
			if (rand(0,1) == 0) {
				$gravatar = 'themes/theme.default/images/default-avatar-female.png';	
			} else {
				$gravatar = 'themes/theme.default/images/default-avatar-male.png';
			}
		}
		
		// Assign author-related tags
		$PageConfig->variables->nf_author_name =		$post->first_name . ' ' . $post->last_name;
		$PageConfig->variables->nf_author_permalink =	$nf['paths']['siteroot'] . 'author.php?author=' . $post->id;
		$PageConfig->variables->nf_author_email =		$post->email;
		$PageConfig->variables->nf_author_bio =			$this->FormatMarkdown($post->bio);
		$PageConfig->variables->nf_author_homepage =	$post->homepage;
		$PageConfig->variables->nf_author_avatar =		$gravatar;
		
		// Get and format list of author's posts
		$postsByAuthor = $pm->GetPostsByAuthor($post->id); // get posts by given author id
		$PageConfig->variables->nf_author_posts = $this->FormatCondensedPosts($postsByAuthor, $PageConfig);
		
		$post_html = $this->OpenTemplate('author', $PageConfig);
		
		return $post_html;
	}
	
}

?>