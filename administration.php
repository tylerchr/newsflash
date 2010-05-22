<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

$vars = explode('?', $_SERVER['REQUEST_URI']);
$split = explode('&', $vars[1]);

// Validate user login
session_start();
if ($_SESSION['auth_loggedin'] == true) {

	if ($split[0] == 'posts') {
		
		$pm = new PostManagement();
		$posts = $pm->GetPosts();
		
		foreach ($posts['posts'] as $post) {
			$comments = rand(0,5);
			if ($comments > 0) {
				$comment_code = '<span>' . $comments . ' comments</span>';
			} else {
				$comment_code = '';	
			}
			$listing .= '
				<tr>
					<td class="post-title"><a href="#">' . $comment_code . $post->title . '</a></td>
					<td class="post-date">' . date("n/j/Y g:ia", $post->date) . '</td>
					<td class="post-buttons"><a href="administration.php?posts/write&post=' . $post->id . '">Edit</a><a href="administration.php?post-remove&post=' . $post->id . '">Remove</a></td>
				</tr>';
		}
		
		$pageListing = '<h1>Content Listing</h1>
		
		<div id="posts-top-controls">
			<a href="administration.php?posts/write" class="visible-button">Write a new post/page</a>
			<p id="infosection">122 posts since December 2006</p>
			<span id="filter-posts">Live Filter <input type="search" id="searchbar" /></span>
		</div>
		<table class="post-listing">
			<thead>
				<tr>
					<th>Title</th>
					<th>Date</th>
					<th>&nbsp;</th>
				<tr>
			</thead>
			<tbody>
					' . $listing . '
				<tr id="info-row">
					<td colspan="3">Hello World</td>
				</tr>
			</tbody>
		</table>';
		
		$admin = new AdminPage();
		$finalPage = $admin->ConstructPage($pageListing, true);
		
	} else if ($split[0] == 'posts/write') {
		
		$pm = new PostManagement();
		
		// Discover if we're editing or posting
		$post = $_GET['post'];
		if ($post > 0) {
			$edit = $pm->GetCertainPost($post);
		}
		$post_data = $edit['posts'][$post];
		
		if (strlen($post_data->title) > 0) {
			$display_title = $post_data->title;	
		} else {
			$display_title = '[ Click to type title ]';
		}
		
		$pageListing = '<h1>Write something new</h1>
		
		<form id="new-post-form" action="administration.php?add-save" method="post">
			<div id="new-post-header">
				<span class="data-container" id="post-title-view"><h2>' . $display_title . '</h2></span>
				<span class="data-container" id="post-title"><input type="text" name="title" value="' . $display_title . '" /></span>
				<ul id="new-post-type">
					<li>Select post type:</li>
					<li class="tab-selected"><a href="#" id="linky-text">Text</a></li>
					<li><a href="#">Quote</a></li>
					<li><a href="#">Link</a></li>
					<li><a href="#">Image</a></li>
					<li><a href="#">Page</a></li>
				</ul>
			</div>
			<input type="hidden" id="post-id" name="id" value="' . $post_data->id . '" />
			<input type="hidden" id="post-type" name="type" value="' . $post_data->type . '" />
			<span class="data-container" id="post-link"><h3 class="caption">Link URL</h3><input type="text" name="link" id="post-link" value="' . $post_data->link . '" /></span>
			<span class="data-container" id="post-image"><h3 class="caption">Image URL</h3><input type="text" name="image" id="post-image" value="' . $post_data->image . '" /></span>
			<span class="data-container" id="post-text"><textarea id="new-post" name="text">' . $post_data->text . '</textarea></span>
			<div id="edit-post-button-bar">
				<a href="administration.php?posts" class="visible-button">Cancel</a>
				<input type="submit" class="visible-button" value="Save this post" />
			</div>
		</form>
		';
		
		$admin = new AdminPage();
		$finalPage = $admin->ConstructPage($pageListing, true);
		
	} else if ($split[0] == 'comments') {
		
		$pageListing = '<h1>Comment Moderation</h1>
		<p>This is a list of all the posts you\'ve created.</p>';
		
		$admin = new AdminPage();
		$finalPage = $admin->ConstructPage($pageListing, true);
		
	} else if ($split[0] == 'themes') {
		
		$pageListing = '<h1>Theme Management</h1>
		<p>This is a list of all the posts you\'ve created.</p>';
		
		$admin = new AdminPage();
		$finalPage = $admin->ConstructPage($pageListing, true);
		
	} else if ($split[0] == 'add-save') {
	
		$pm = new PostManagement();
			
		$np = new post();
		$np->id = intval($_POST['id']);
		$np->type = strtolower($_POST['type']);
		$np->title = $_POST['title'];
		$np->slug = $pm->GenerateSlug($_POST['title']);
		$np->author = $_SESSION['auth_info']->id;
		$np->text = $_POST['text'];
		$np->category = '';
		$np->tags = '';
		
		$pid = intval($_POST['id']);
		if ($pm->SavePostData($np)) {
			// $pageListing = "Post was saved successfully.";
			header("Location: administration.php?posts");
		} else {
			$pageListing = "Error: problem. Sad day.";
		}
		
		$admin = new AdminPage();
		$finalPage = $admin->ConstructPage($pageListing, true);
		
	} else if ($split[0] == 'post-remove') {
	
		if ($_POST['removable-post-id']) {
			
			$pm = new PostManagement();
			$np = $pm->GetCertainPost(intval($_POST['removable-post-id']));
			if ($pm->DeletePost(intval($_POST['removable-post-id']))) {
				// deleted
				$pageListing = 'Deleted post "' . $np[0]->title . '" permanently.';
			} else {
				// failed	
				$pageListing = 'We couldn\'t delete the post at all! Give up on it.';
			}
			
			$admin = new AdminPage();
			$finalPage = $admin->ConstructPage($pageListing, true);
			
		} else {
	
			$pm = new PostManagement();
				
			$pid = $_GET['post'];
			$np = $pm->GetCertainPost($pid);
			
			$pageListing = '
				<h1>Remove Post</h1><p>Are you confident that you want to remove this post?</p>
				<p>' . $np[0]->title . '</p>
				<p>' . date("F j, Y @ g:i A", $np[0]->date) . '</p>
				<form action="administration?post-remove" method="post">
					<input type="hidden" name="removable-post-id" value="' . $pid . '" />
					<input type="submit" class="visible-button" value="Permanently remove post" />
				</form>';
			
			$admin = new AdminPage();
			$finalPage = $admin->ConstructPage($pageListing, true);
		
		}
		
	} else if ($split[0] == 'logout') {
	
		session_destroy();
		header("Location: administration.php?posts&msg=1");
		
	} else {
	
		$admin = new AdminPage();
		$finalPage = $admin->ConstructPage($admin->GetLandingPage(), false);
	
	}
	
} else if ($split[0] == 'login-validate') {
	
	$un = $_POST['username'];
	$c = new Core();
	$pw_hash = $c->CreatePasswordHash($_POST['password']);
	
	$am = new AuthorManagement();
	if ($author_results = $am->ValidateAuthorCredentials($un, $pw_hash)) {
		
		$_SESSION['auth_loggedin'] = true;
		$_SESSION['auth_username'] = $un;
		$_SESSION['auth_info'] = $author_results;
		
		header("Location: administration.php");
	} else {
		header("Location: administration.php?posts&msg=0");
	}
	
} else {
	
	if (is_numeric($_GET['msg'])) {
		if ($_GET['msg'] == 0) {
			$msg = '<p id="login-msg" class="msg-error">Crap, either your username or password are wrong.</p>';	
		} else if ($_GET['msg'] == 1) {
			$msg = '<p id="login-msg" class="msg-okay">Ok, you\'re done. Go outside and meet some people!</p>';	
		}
	}

	$content = '	
	<html>
		<head>
			<title>Test</title>
			<link rel="stylesheet" href="admin/login.css" />
		</head>
		<body>
			<div id="login">
				<h1>Log In</h1>
				' . $msg . '
				<form action="administration.php?login-validate" method="post">
					<span class="login-title">username</span><input type="text" name="username" />
					<span class="login-title">password</span><input type="password" name="password" />
					<input type="submit" value="Log In" />
				</form>
			</div>
			<p id="footer">Thank you for using Newsflash.</p>
		</body>
	</html>';
	
	$finalPage = $content;
	
}

echo $finalPage;

?>