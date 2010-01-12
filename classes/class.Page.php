<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class Page {
	
	private $theme;
	
	public function Render($Page) {
		$this->SetTheme('theme.default');
		
		$PageConfig = $Page->ConstructContents();
		$PageConfig = $this->SetGenericTags($PageConfig);

		// Check if theme exists, before continuing
		if (!is_dir($this->theme_path)) {
			$this->setTheme('theme.default');
		}
		
		// Open the main page template
		$mainpage = $this->OpenTemplate('main', $PageConfig);
		
		return $mainpage;
	}
	
	public function SetGenericTags($PageConfig) {
		require(dirname(__FILE__) . '/../configuration.php');
		
		$PageConfig->variables->nf_blog_title = $nf['blog']['title'];
		$PageConfig->variables->nf_blog_subtitle = $nf['blog']['subtitle'];
		$PageConfig->variables->nf_site_root = '../../';
		$PageConfig->variables->nf_category_list = $this->GetCategoryList();
		$PageConfig->variables->nf_tag_list = $this->GetTagList();
		$PageConfig->variables->nf_archive_list = $this->GetArchivesList();
		$PageConfig->variables->nf_pages_list = $this->GetPageList();
		$PageConfig->variables->nf_pages_list = $this->GetPageList();
		$PageConfig->variables->nf_search_bar = $this->GetSearchBar();
		
		return $PageConfig;		
	}
	
	public function SetTheme($theme_name) {
		require(dirname(__FILE__) . '/../configuration.php');
		$path = $nf['paths']['absolute'] . 'themes/' . $theme_name . '/';
		
		// If theme files exist, set it up and return true
		if (is_dir($path)) {
			$this->theme = $theme_name;
			$this->theme_path = $nf['paths']['absolute'] . 'themes/' . $this->theme . '/';
			$this->theme_url = $nf['paths']['siteroot'] . 'themes/' . $this->theme . '/';	
			return true;
		} else {
			return false;	
		}
	}
	
	public function OpenTemplate($template, $PageConfig) {
		require(dirname(__FILE__) . '/../configuration.php');
		
		$tags = $PageConfig->variables;
		ob_start();
		include($this->theme_path . $nf['template'][$template]);
		$page = ob_get_contents();
		ob_end_clean();
		
		return $page;	
	}
	
	//
	// SIMPLY GETS LISTS OF STUFF
	//
	
	public function GetCategoryList() {
		$cm = new CategoryManagement();
		$cats = $cm->GetListOfUsedCategories();
		if (count($cats) > 0) {
			foreach ($cats as $key => $value) {
				$categories[$cm->CategoryNameWithID($key)]['id'] = $key;
				$categories[$cm->CategoryNameWithID($key)]['count'] = $value;
			}
			ksort($categories);
			
			$menu = '<ul class="category-list">';
			foreach($categories as $key => $value) {
				$menu .= '<li class="category-list-item"><a href="category.php?cid=' . $value['id'] . '">' . $key . ' (' . $value['count'] . ')</a></li>';
			}
			$menu .= '</ul>';
		}
	
		return $menu;
	}
	
	public function GetPageList() {
		$pm = new PageManagement();
		$pages = $pm->GetPageList();
		if (count($pages) > 0) {
			$page_list = '<ul>';
			foreach ($pages as $key => $value) {
				$page_list .= '<li class="page-list-item"><a href="page.php?page=' . $value->id . '">' . $value->short_title . '</a></li>';
			}
			$page_list .= '</ul>';
		} else {
			$page_list = '<ul><li>No Pages</li></ul>';	
		}
		return $page_list;
	}
	
	public function GetTagList() {
		$tm = new TagManagement();
		$tags = $tm->GetAllTags();
		
		foreach ($tags as $key => $value) {
			$new_tag_list[] = $key;
		}
		sort($new_tag_list);
		
		$tag_list = '<ul class="tag-list">';
		foreach ($new_tag_list as $value) {
			$tag_list .= '<li class="tag-list-item">' . $tm->FormatTag($value) . '</li>';
		}
		$tag_list .= '</ul>';
		return $tag_list;
	}
	
	public function GetArchivesList() {
		$pm = new PostManagement();
		$dates = $pm->GetPostDates();
		if (count($dates) > 0) {
			$archive_list = '<ul>';
			foreach ($dates as $key => $value) {
				$archive_list .= '<li class="archive-list-item"><a href="archive.php?year=' . $value['year'] . '&month=' . $value['month'] . '">' . $key . ' (' . $value['count'] . ')</a></li>';
			}
			$archive_list .= '</ul>';
		} else {
			$archive_list = '<ul><li>No Archives</li></ul>';	
		}
		return $archive_list;
	}
	
	public function GetSearchBar() {
		$bar = '<form action="search.php" method="get">
			<input type="search" name="q" />
			<input type="submit" value="Go" />
		</form>';
		
		return $bar;	
	}
	
	public function ReadTemplateFile($file) {
		$path = $this->theme_path . $file;
		if (file_exists($path)) {
			$contents = file_get_contents($path);
		}
		return $contents;
	}
	
	//
	// BAD CODE
	//
	
	
	public function FormatPostListing($posts, $PageConfig) {
		if (count($posts) > 0) {
			
			if ($PageConfig->PostListStyle == 'condensed') {
				$current_month = NULL;
				foreach ($posts as $single_post) {
					$month = date("F Y", $single_post->date);
					if ($month != $current_month) {
						$current_month = $month;
						$post_all .= '</ul><h1 class="nf-condensed-header">' . $month . '</h1><ul>';	
					}
					
					$post_all .=	'<li>' .
									'	<span class="nf-post-subhead nf-post-subhead-date">' . date("j M", $single_post->date) . '</span>' .
									'	<h2><a href="post.php?post=' . $single_post->id . '">' . $single_post->title . '</a></h2>' .
									'</li>';
				}
				return $post_all;
			} else {
				foreach ($posts as $single_post) {
					$post_all .= $this->FormatPost($single_post, $PageConfig);
				}
				return $post_all;
			}
		} else {
			require(dirname(__FILE__) . '/../configuration.php');
			return $nf['error']['no_posts'];	
		}
	}
	
	public function FormatPost($post, $PageConfig) {
		require(dirname(__FILE__) . '/../configuration.php');
		
		// Markdown-format the text, if Markdown is available
		$pf = new PackageFinder();
		if ($pf->PackageEnabled('markdown') && !function_exists('Markdown')) {
			$markdown_path = $nf['paths']['absolute'] . 'packages/pkg.markdown/markdown.php';
			require($markdown_path);
		}
		
		if (function_exists('Markdown')) {
			$post_text = Markdown($post->text);
		} else {
			echo "<li>Help (" . $post->title . ")</li>";
			$post_text = $post->text;
		}
		
		
		$core = new Core();
		$tags = $PageConfig->variables;
		$tags->nf_siteroot =		$nf['paths']['siteroot'];
		
		if (method_exists($post, 'TagCloud'))
			$tags->nf_post_tags =		$post->TagCloud();
		
		$pm = new PostManagement();
		
		if ($PageConfig->type == 'page') {
			
			// Assign page-related tags
			$tags->nf_page_id =			$post->id;
			$tags->nf_page_title =		$post->title;
			$tags->nf_page_date =		date("j F Y", $core->TimeFromUniversal($post->date));
			$tags->nf_page_text =		$post_text;
			$tags->nf_page_permalink =	$nf['paths']['siteroot'] . 'page.php?page=' . $post->id;
			
			$post_html = $this->OpenTemplate('page', $PageConfig);		
		} else if ($PageConfig->type == 'author') {
			
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
			
			// Markdownify their bio
			if ($pf->PackageEnabled('markdown') && !function_exists('Markdown')) {
				$markdown_path = $nf['paths']['absolute'] . 'packages/pkg.markdown/markdown.php';
				require($markdown_path);
			}
			$bio = Markdown($post->bio);
			
			// Assign author-related tags
			$tags->nf_author_name =		$post->first_name . ' ' . $post->last_name;
			$tags->nf_author_permalink =	$nf['paths']['siteroot'] . 'author.php?author=' . $post->id;
			$tags->nf_author_email =	$post->email;
			$tags->nf_author_bio =		$bio;
			$tags->nf_author_homepage =	$post->homepage;
			$tags->nf_author_avatar =	$gravatar;
			
			$PageConfig2 = $PageConfig;
			$PageConfig2->type = 'all';
			$PageConfig2->PostListStyle = 'condensed';
			$tags->nf_author_posts =	$this->FormatPostListing($pm->GetPostsByAuthor($post->id), $PageConfig2);
			
			$post_html = $this->OpenTemplate('author', $PageConfig);
		} else {
			
			// Assign post-related tags
			$tags->nf_post_id =			$post->id;
			$tags->nf_post_type =		$post->type;
			$tags->nf_post_title =		$post->title;
			$tags->nf_post_author =		'<a href="author.php?author=' . $post->author_id . '">' . $post->author . '</a>';
			$tags->nf_post_date =		date("j F Y", $core->TimeFromUniversal($post->date));
			$tags->nf_post_time =		date("g:i A", $core->TimeFromUniversal($post->date));
			$tags->nf_post_text =		$post_text;
			$tags->nf_link_link =		$post->link;
			$tags->nf_image_image =		$post->image;
			$tags->nf_post_category =	'<a href="category.php?cid=' . $post->category_id . '">' . $post->category . '</a>';
			$tags->nf_post_permalink =	$nf['paths']['siteroot'] . 'post.php?post=' . $post->id;
			
			$post_html = $this->OpenTemplate('post_' . $post->type, $PageConfig);
		}
		return $post_html;
	}
	
	public function AbsolutifyPathnames($abs, $content) {
		
		// Should only insert absolute path IF:
		//     1. It is within a tag
		//     2. It is not already an absolute, beginning in a prefix://
		
		// But we're lazy and for now will check neither
		$content = str_ireplace('src="', 'src="' . $abs, $content);
		$content = str_ireplace('src=\'', 'src="' . $abs, $content);
		$content = str_ireplace('href="', 'href="' . $abs, $content);
		$content = str_ireplace('href=\'', 'href="' . $abs, $content);
		
		return $content;	
	}
		
}

?>