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
		
		if ($PageConfig->type == 'single') {
			$PageConfig->tags['%nf_posts%'] = $this->GetSinglePost($PageConfig->SinglePostID);
		} else if ($PageConfig->type == 'all') {
			$PageConfig->tags['%nf_posts%'] = $this->GetAllPosts($PageConfig->SinglePostID);
		} else if ($PageConfig->type == 'category') {
			$PageConfig->tags['%nf_posts%'] = $this->GetCategoryPosts($PageConfig->listCategoryID);	
		}
		
		$PageConfig->tags['%nf_category_list%'] = $this->GetCategoryList();
		$PageConfig->tags['%nf_tag_list%'] = $this->GetTagList();
		
		$mainpage = $this->ReplaceTags($PageConfig->tags, $mainpage);
		
		
		// Show the final result
		echo $mainpage;
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
			
			$menu = '<h3>Categories</h3><ul class="category-list">';
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
		
		$tag_list = '<h3>Tags</h3><ul class="tag-list">';
		foreach ($new_tag_list as $value) {
			$tag_list .= '<li class="tag-list-item">' . $tm->FormatTag($value) . '</li>';
		}
		$tag_list .= '</ul>';
		return $tag_list;
	}
	
	public function GetSinglePost($post_id) {
		$pm = new PostManagement();
		return $this->FormatPost($pm->GetCertainPost($post_id));
	}
	
	public function GetAllPosts() {
		$pm = new PostManagement();
		$posts = $pm->GetPosts();
		foreach ($posts as $single_post) {
			$post_all .= $this->FormatPost($single_post);
		}
		return $post_all;
	}
	
	public function GetCategoryPosts($cat_id) {
		$pm = new PostManagement();
		$posts = $pm->GetPostsFromCategory($cat_id);
		foreach ($posts as $single_post) {
			$post_all .= $this->FormatPost($single_post);
		}
		return $post_all;
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
		
		$tags = array(
			'%nf_siteroot%' => $nf['paths']['siteroot'],
			'%nf_post_permalink%' => $nf['paths']['siteroot'] . 'post.php?post=' . $post->id,
			'%nf_post_id%' => $post->id,
			'%nf_post_type%' => $post->type,
			'%nf_post_title%' => $post->title,
			'%nf_post_date%' => date("j F Y", $post->date),
			'%nf_post_time%' => date("g:i A", $post->date),
			'%nf_post_tags%' => $post->TagCloud(),
			'%nf_text_content%' => $post_text,
			'%nf_link_link%' => $post->link,
			'%nf_image_image%' => $post->image,
			'%nf_post_category%' => $post->category);

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