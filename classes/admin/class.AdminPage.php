<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class AdminPage {
	
	public function ConstructPage($contents, $contain=false) {
		
		require(dirname(__FILE__) . '/../../configuration.php');

$stuff = '<html>
	<head>
		<title>Admin</title>
		<link rel="stylesheet" href="' . $nf['paths']['siteroot'] . 'admin/admin.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
		<script type="text/javascript" src="' . $nf['paths']['siteroot'] . 'admin/admin.js"></script>
	</head>
	<body>
		<div id="navigation">
			<div class="container">
				<ul class="linkbar link-button-bar">
					<li><a href="administration.php" class="subtle-link">Newsflash Panel</a></li>
					<li><a href="administration.php?posts/write">Write</a></li>
					<li><a href="administration.php?posts">Manage</a></li>
					<li><a href="administration.php?themes">Themes</a></li>
					<li><a href="administration.php?authors">Authors</a></li>
					
					<li class="menu-right"><a href="administration.php?logout">Log Out</a></li>
					<li class="menu-right"><a href="#">Settings</a></li>
					<li class="menu-right"><a href="#">View Site</a></li>
				</ul>
			</div>
		</div>
		<div id="admin-body">
			';
			
			if ($contain==true) { $stuff .= '<div class="container frame">'; }
			
			$stuff .= $contents;
			
			if ($contain==true) { $stuff .= '</div>'; }
			
			$stuff .= '
		</div>
		<div id="footer">
			Thank you for using <a href="#">Newsflash</a>, a <a href="#">CodePrinciples</a> project.
		</div>
	</body>
</head>';

		return $stuff;
		
	}
	
	public function GetLandingPage() {
		$stuff = '
			<div class="container">
			
				<div class="home-thumb">
					<span class="home-thumb-title">Comments awaiting moderation</span>
					A lot of people write really stupid things on the Internet, and unfortunately a lot of these things have ended up on your poor blog. Please review these comments and filter out the morons.
					<ul>
						<li>Dumb person</li>
						<li>Complete moron</li>
						<li>Bright idea</li>
						<li>Flamewar provoker</li>
						<li>Your dad</li>
					</ul>
					This is a little extra data for you to read out loud and impress your friends with.
				</div>
				<div class="home-thumb">
					<span class="home-thumb-title">Quick Links</span>
					<ul class="linkbar">
						<li><a href="#" class="visible-button">Write a new post</a></li>
						<li><a href="#" class="visible-button">Add an author</a></li>
						<li><a href="#" class="visible-button">Strike it rich</a></li>
						<li><a href="administration.php?logout" class="visible-button">Log out</a></li>
					</ul>
				</div>
				<div class="home-thumb">
					<span class="home-thumb-title">Posting</span>
					These are the posts that need to be moderated.
				</div>
				
			</div>
		';	
		
		return $stuff;
	}
		
}

?>