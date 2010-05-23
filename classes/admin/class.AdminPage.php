<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class AdminPage {
	
	public function ConstructPage($contents, $title=false, $sidebar=array()) {
		
		$author_name = $_SESSION['auth_info']->username;
		$avatar = 'themes/theme.default/images/default-avatar-male.png';
		
		// attempt to get Gravatar
		$pf = new Packages();
		if ($pf->ImportPackage('Gravatar')) {
		
			// Get Gravatar	
			$grv = new Gravatar($_SESSION['auth_info']->email);
			if ($grv->GravatarExists()) {
				$grv->setSize(128);
				$avatar = $grv;	
			}
		
		}
		
		// title string
		if (!is_null($title) && strlen($title) > 0) {
			$title_string = '<h3>' . $title . '</h3>';
		} else {
			$title_string = '';	
		}
		
		if (!is_null($sidebar) && count($sidebar) > 0) {
			$sidebar_text = '<ul class="sidebar">';
			foreach ($sidebar as $item) {
				
				if (isset($item['number'])) {
					$text = '<span class="text">' . $item['text'] .'</span> <span class="number">' . $item['number'] . '</span>';
				} else {
					$text = '<span class="text">' . $item['text'] .'</span>';
				}
				
				$sidebar_text .= '<li>';
				if (isset($item['link'])) {
					 $sidebar_text .= '<a href="' . $item['link'] . '">' . $text . '</a>';	
				} else {
					$sidebar_text .= $text;
				}
				
				$sidebar_text .= '</li>';
			}
			$sidebar_text .= '</ul>';
			$content_class = 'content sidebar';
		} else {
			$sidebar_text = '';
			$content_class = 'content';
		}
		
		$opt = new Options();

$stuff = '
	<!DOCTYPE html>
	<html>
	<head>
		<title>Admin</title>
		<link rel="stylesheet" href="' . $opt->ValueForKey("paths/siteroot") . 'admin/admin.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
		<script type="text/javascript" src="' . $opt->ValueForKey("paths/siteroot") . 'admin/admin.js"></script>
		<script type="text/javascript" src="' . $opt->ValueForKey("paths/siteroot") . 'admin/showdown.js"></script>
	</head>
	<body>
		<header>
			<section class="ninesixty">
				<h1><a href="administration.php">' . $opt->ValueForKey('blog/title') . '</a></h1>
				<h2>' . $opt->ValueForKey('blog/subtitle') . '</h2>
				<nav>
					<ul class="quicklinks">
						<li><a href="administration.php?posts/write">Write</a></li>
						<li><a href="#" id="more-link">More &#x2192;</a></li>
						<li class="more"><a href="administration.php?posts">Manage</a></li>
						<li class="more"><a href="administration.php?themes">Themes</a></li>
						<li class="more"><a href="administration.php?authors">Authors</a></li>
					</ul>
					<ul class="settings">
						<li class="menu-right my-name"><a href="#"><img src="' . $avatar . '" /> ' . $author_name . '</a></li>
						<li class="menu-right"><a href="' . $opt->ValueForKey('paths/siteroot') . '">View Site</a></li>
						<li class="menu-right"><a href="administration.php?settings">Settings</a></li>
						<li class="menu-right"><a href="administration.php?logout">Log Out</a></li>
					</ul>
				</nav>
			</section>
		</header>
		<section class="ninesixty adminbody">
			' . $title_string . '
			<section class="frame">			
				' . $sidebar_text . '
				<section class="' . $content_class . '">
					' . $contents . '
				</section>
				
			</section>
		</section>
		<footer>
			<section class="ninesixty">
				Thank you for using <a href="http://codeprinciples.com/newsflash/">Newsflash</a>, a <a href="http://codeprinciples.com">CodePrinciples</a> project.
			</section>
		</footer>
	</body>
</head>';

		return $stuff;
		
	}
		
}

?>