<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class ui {
	
	public $theme;
	public $theme_path;
	public $theme_url;
	
	public function __construct() {
		$this->SetTheme('theme.default');	
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
	
	public function buildPage($PageConfig) {					
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');

		// Check if theme exists, before continuing
		if (!is_dir($this->theme_path)) {
			$this->theme = 'theme.default';
			$this->GetThemePaths();
		}
		
		// Get template source
		$mainpage = file_get_contents($this->theme_path . $nf['template']['main']);
		
		// Clean up pathnames, make them absolute
		$mainpage = $this->AbsolutifyPathnames($this->theme_url, $mainpage);
		
		// Generate the contents of the page, based on the given PageConfig object
		$pm = new PostManagement();
		if ($PageConfig->type == 'single') {
			$posts = $pm->GetCertainPost($PageConfig->SinglePostID);
			$PageConfig->tags['%nf_page_title%'] = $posts[0]->title . ' - ' . $nf['blog']['title'];
		} else if ($PageConfig->type == 'all') {
			$posts = $pm->GetPosts();
			$PageConfig->tags['%nf_page_title%'] = $nf['blog']['title'];
		} else if ($PageConfig->type == 'category') {
			$posts = $pm->GetPostsFromCategory($PageConfig->listCategoryID);
			$cm = new CategoryManagement();
			$category = $cm->GetCategoryWithID($PageConfig->listCategoryID);
			$cname = $category->name;
			if (strlen($cname) == 0) {
				$cname = 'Unfiled';	
			}
			$PageConfig->tags['%nf_page_title%'] = $cname . ' - ' . $nf['blog']['title'];
		} else if ($PageConfig->type == 'tag') {
			$posts = $pm->GetPostsTaggedWith($PageConfig->listTag);
			$PageConfig->tags['%nf_page_title%'] = 'Tagged with \'' . $PageConfig->listTag . '\' - ' . $nf['blog']['title'];
		} else if ($PageConfig->type == 'search') {
			$posts = $pm->GetPostsMatchingQuery($PageConfig->searchQuery);
			$PageConfig->tags['%nf_page_title%'] = 'Results for \'' . $PageConfig->searchQuery . '\' - ' . $nf['blog']['title'];
		} else if ($PageConfig->type == 'archive') {
			$posts = $pm->GetPostsFrom($PageConfig->archive['year'], $PageConfig->archive['month'], $PageConfig->archive['day']);
		}
		
		// If we returned an array, assume it's of posts and format accordingly, otherwise just pass it on
		if (is_array($posts)) {
			$PageConfig->tags['%nf_posts%'] = $this->FormatPostListing($posts);
		} else if (strlen($posts) > 0) {
			$PageConfig->tags['%nf_posts%'] = $posts;
		} else {
			$PageConfig->tags['%nf_posts%'] = '<p class="nf-error-text">There aren\'t any posts to show here!</p>';
		}
		
		// $PageConfig->tags['%nf_site_root%'] = $nf['paths']['siteroot'];
		$PageConfig->tags['%nf_site_root%'] = '../../';
		$PageConfig->tags['%nf_category_list%'] = $this->GetCategoryList();
		$PageConfig->tags['%nf_tag_list%'] = $this->GetTagList();
		$PageConfig->tags['%nf_archive_list%'] = $this->GetArchivesList();
		
		// Replace the tags with actual data
		$mainpage = $this->ReplaceTags($PageConfig->tags, $mainpage);

		// Send back the final result
		return $mainpage;
	}
	
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
	
	public function FormatPostListing($posts) {
		if (count($posts) > 0) {
			foreach ($posts as $single_post) {
				$post_all .= $this->FormatPost($single_post);
			}
			return $post_all;
		} else {
			return "No posts";	
		}
	}
	
	public function ReplaceTags($tags, $content) {
		
		// first parameter is the replacement array to use
		// second parameter is the content to run replacement on
				
		foreach ($tags as $key => $value) {
			$content = str_ireplace($key, $value, $content);
		}
		return $content;
		
	}
	
	public function FormatPost($post) {
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
		$tags = array(
			'%nf_siteroot%' => $nf['paths']['siteroot'],
			'%nf_post_permalink%' => $nf['paths']['siteroot'] . 'post.php?post=' . $post->id,
			'%nf_post_id%' => $post->id,
			'%nf_post_type%' => $post->type,
			'%nf_post_title%' => $post->title,
			'%nf_post_author%' => $post->author,
			'%nf_post_date%' => date("j F Y", $core->TimeFromUniversal($post->date)),
			'%nf_post_time%' => date("g:i A", $core->TimeFromUniversal($post->date)),
			'%nf_post_tags%' => $post->TagCloud(),
			'%nf_text_content%' => $post_text,
			'%nf_link_link%' => $post->link,
			'%nf_image_image%' => $post->image,
			'%nf_post_category%' => '<a href="category.php?cid=' . $post->category_id . '">' . $post->category . '</a>');

		// Get template for a post		
		switch ($post->type) {
			case 'text':
				$post_html = $this->ReadTemplateFile($nf['template']['post_text']);
				break;
			case 'link':
				$post_html = $this->ReadTemplateFile($nf['template']['post_link']);
				break;
			case 'image':
				$post_html = $this->ReadTemplateFile($nf['template']['post_image']);
				break;
		}
		
		return $this->ReplaceTags($tags, $post_html);
	}
	
	public function ReadTemplateFile($file) {
		$path = $this->theme_path . $file;
		if (file_exists($path)) {
			$contents = file_get_contents($path);
		}
		return $contents;
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