<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class Page {
	
	private $theme;
	private $page;
	
	public function __construct() {
		
		$vars = $this->_ReadPageVariables();
		
		$this->SetPageVariables($vars);
		$this->SetPageData(array("page" => $vars['page']));
	}
	
	private function _ReadPageVariables() {
		
		$replacements = array(
			"p" => "page",
			"q" => "query",
			"tag" => "identifier",
			"cid" => "identifier",
			"author" => "identifier",
			"post" => "identifier",
			"page" => "identifier"
		);
		
		$variables['page'] = 1;
		foreach ($_GET as $key => $value) {
			if (array_key_exists($key, $replacements)) {
				$key = $replacements[$key];	
			}
			$variables[$key] = $value;	
		}
		return $variables;
	}
	
	public function SetPageVariables($id) {
		// do nothing with it, in this generic prototype
	}
	
	public function setPageData($page) {
		require(dirname(__FILE__) . '/../configuration.php');
		$page['limit'] = $nf['posts']['posts_per_page'];
		$page['total_pages'] = ceil($page['results'] / $page['limit']);
		$this->page = $page;
	}
	
	public function getPageData() {
		return $this->page;	
	}
	
	public function Render($Page) {
		$this->SetTheme('theme.default');
		
		$PageConfig = $Page->ConstructContents();
		
		// here would be the place to append a pagination control
		$PageConfig->variables->nf_posts .= $this->PaginationControl();
		
		$PageConfig = $this->SetGenericTags($PageConfig);

		// Check if theme exists, before continuing
		if (!is_dir($this->theme_path)) {
			$this->setTheme('theme.default');
		}
		
		// Open the main page template
		$mainpage = $this->OpenTemplate('main', $PageConfig);
		
		return $mainpage;
	}
	
	public function PaginationControl() {
		$pageData = $this->getPageData();
		$current_page = $pageData['page'];
		$total_pages = $pageData['total_pages'];
		
		if ($total_pages > 1) {
		
			$control = "<nav id=\"nf-pagination\"><ul id=\"nf-pagination-control\"><li><p>Page</p></li>";
			for ($i=1; $i<=$total_pages; $i++) {
				if ($i == $current_page) {
					$class = " class=\"nf-current-page\"";	
				} else {
					$class = "";	
				}
				
				$control .= "<li><a href=\"?p=" . $i . "\"" . $class . ">" . $i . "</a></li>";
			}
			$control .= "</ul></nav>";			
		} else {
			$control = "";
		}
		return $control;
	}
	
	public function SetGenericTags($PageConfig) {
		require(dirname(__FILE__) . '/../configuration.php');
		
		$PageConfig->variables->nf_blog_title =		$nf['blog']['title'];
		$PageConfig->variables->nf_blog_subtitle =	$nf['blog']['subtitle'];
		$PageConfig->variables->nf_site_root =		'../../';
		$PageConfig->variables->nf_category_list =	$this->GetCategoryList();
		$PageConfig->variables->nf_tag_list =		$this->GetTagList();
		$PageConfig->variables->nf_archive_list =	$this->GetArchivesList();
		$PageConfig->variables->nf_pages_list =		$this->GetPageList();
		$PageConfig->variables->nf_pages_list =		$this->GetPageList();
		$PageConfig->variables->nf_search_bar =		$this->GetSearchBar();
		
		$PageConfig->variables->nf_siteroot =		$nf['paths']['siteroot'];
		
		if (method_exists($post, 'TagCloud'))
			$tags->nf_post_tags =		$post->TagCloud();
		
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
	
	public function FormatPost($post, $PageConfig, $highlight=array()) {
		
		$core = new Core();
		
		// Assign post-related tags
		$PageConfig->variables->nf_post_id =		$post->id;
		$PageConfig->variables->nf_post_type =		$post->type;
		$PageConfig->variables->nf_post_title =		$this->_HighlightText($highlight, $post->title);
		$PageConfig->variables->nf_post_author =	'<a href="author.php?author=' . $post->author_id . '">' . $post->author . '</a>';
		$PageConfig->variables->nf_post_date =		date("j F Y", $core->TimeFromUniversal($post->date));
		$PageConfig->variables->nf_post_time =		date("g:i A", $core->TimeFromUniversal($post->date));
		$PageConfig->variables->nf_post_text =		$this->_HighlightText($highlight, $this->FormatMarkdown($post->text));
		$PageConfig->variables->nf_link_link =		$post->link;
		$PageConfig->variables->nf_image_image =	$post->image;
		$PageConfig->variables->nf_post_category =	'<a href="category.php?cid=' . $post->category_id . '">' . $post->category . '</a>';
		$PageConfig->variables->nf_post_permalink =	$nf['paths']['siteroot'] . 'post.php?post=' . $post->id;
		
		$post_html = $this->OpenTemplate('post_' . $post->type, $PageConfig);
		
		return $post_html;
	}
	
	private function _HighlightText($keys, $text) {
		foreach ($keys as $key) {
			$text = str_ireplace($key, "<span class=\"nf-search-highlight\">" . $key . "</span>", $text);
		}
		return $text;
	}
	
	public function FormatCondensedPosts($posts, $PageConfig) {
		if (count($posts) > 0) {
			
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
			require(dirname(__FILE__) . '/../configuration.php');
			return $nf['error']['no_posts'];	
		}
	}
	
	public function FormatMarkdown($input) {
		require(dirname(__FILE__) . '/../configuration.php');
		
		// Markdown-format the text if Markdown is available, otherwise return the input text
		$pf = new PackageFinder();
		if ($pf->PackageEnabled('markdown') && !function_exists('Markdown')) {
			$markdown_path = $nf['paths']['absolute'] . 'packages/pkg.markdown/markdown.php';
			require($markdown_path);
		}
		
		if (function_exists('Markdown')) {
			$post_text = Markdown($input);
		} else {
			$post_text = $input;
		}
		
		return $post_text;
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
		
}

?>