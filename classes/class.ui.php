<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class ui {
	
	public function buildPage($PageConfig) {
		require(dirname(__FILE__) . '/../configuration.php');
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$theme_name = 'theme.default';
		
		$template = $nf['paths']['absolute'] . 'themes/' . $theme_name . '/';
		$absolute_path = $nf['paths']['siteroot'] . 'themes/' . $theme_name . '/';
		
		// Get template source
		$mainpage = file_get_contents($template . 'main.html');
		
		// Clean up pathnames, make them absolute
		$mainpage = $this->AbsolutifyPathnames($absolute_path, $mainpage);
		$mainpage = $this->ReplaceTags($mainpage, $PageConfig);
		
		// Show the final result
		echo $mainpage;
	}
	
	public function ReplaceTags($content, $PageConfig) {
			
		$pm = new PostManagement();
		
		if ($PageConfig->type == 'single') {
			$PageConfig->tags['%nf_posts%'] = $this->FormatPost($pm->GetCertainPost($PageConfig->SinglePostID));
		} else if ($PageConfig->type == 'all') {
			$posts = $pm->GetPosts();
			
			foreach ($posts as $single_post) {
				$post_all .= $this->FormatPost($single_post);
			}
			$PageConfig->tags['%nf_posts%'] = $post_all;
		}
				
		foreach ($PageConfig->tags as $key => $value) {
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
		
		$post = '
		<section class=\'nf_post\'>
			<h2><a href="' . $nf['paths']['siteroot'] . 'post.php?post=' . $post->id . '">' . $post->title . '</a></h2>
			' . $post_text . '
			<span class=\'nf_post_date\'>Posted on ' . date("F j, Y @ g:i A", $post->date) . '</span>
		</section>';
		
		return $post;
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