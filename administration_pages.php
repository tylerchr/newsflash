<?php

// show all posts
function posts() {
		
	$pm = new PostManagement();
	$posts = $pm->GetPosts();
	
        $listing = '';
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
	
	$pageListing = '
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
	return $admin->ConstructPage($pageListing, 'Content Listing');
}

function posts_write() {
		
	$pm = new PostManagement();
        $prefilled_strings = array();
        
	// Discover if we're editing or posting
        if (isset($_GET['post'])) {
            // Editing
            $post = $_GET['post'];
            if ($post > 0) {
                    $edit = $pm->GetCertainPost($post);
            }
            $post_data = $edit['posts'][$post];
            
            $prefilled_strings = array(
                'display_title' => $post_data->title,
                'id' => $post_data->id,
                'type' => $post_data->type,
                'link' => $post_data->link,
                'image' => $post_data->image,
                'text' => $post_data->text
            );
        } else {
            // New post
            $prefilled_strings = array(
                'display_title' => '[ Click to type title ]',
                'id' => '',
                'type' => '',
                'link' => '',
                'image' => '',
                'text' => ''
            );
        }
	
	$pageListing = '
	<form id="new-post-form" action="administration.php?add-save" method="post">
		<ul id="new-post-type">
			<li class="tab-selected"><a href="#" id="linky-text">Text</a></li>
			<li><a href="#">Quote</a></li>
			<li><a href="#">Link</a></li>
			<li><a href="#">Image</a></li>
			<li><a href="#">Page</a></li>
		</ul>
		
		<span class="data-container" id="post-title">
			<h4 class="caption">Post title</h4>
			<input type="text" name="title" value="' . $prefilled_strings["display_title"] . '" />
		</span>
		
		<input type="hidden" id="post-id" name="id" value="' . $prefilled_strings["id"] . '" />
		<input type="hidden" id="post-type" name="type" value="' . $prefilled_strings["type"] . '" />
		
		<span class="data-container" id="post-link">
			<h4 class="caption">Link URL</h4>
			<input type="text" name="link" id="post-link" value="' . $prefilled_strings["link"] . '" />
		</span>
		<span class="data-container" id="post-image">
			<h4 class="caption">Image URL</h4>
			<input type="text" name="image" id="post-image" value="' . $prefilled_strings["image"] . '" />
		</span>
		
		<span class="data-container" id="post-text">
			<textarea id="new-post" name="text">' . $prefilled_strings["text"] . '</textarea>
		</span>
		
		<div id="edit-post-button-bar">
			<a href="administration.php?posts" class="visible-button">Cancel</a>
			<input type="submit" class="visible-button" value="Save this post" />
		</div>
	</form>
	<div id="markdownpreview"></div>
	';
	
	$admin = new AdminPage();
	return $admin->ConstructPage($pageListing, 'Write something new');
	
}

function addsave() {

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
	return $admin->ConstructPage($pageListing, true);
		
}

function comments() {
	
	$pageListing = '
		<p>This is a list of all the posts you\'ve created.</p>
	';
	
	$admin = new AdminPage();
	return $admin->ConstructPage($pageListing, 'Comment Moderation');
	
}

function themes() {
		
	$pageListing = '
		<p>This is a list of all the posts you\'ve created.</p>
	';
	
	$admin = new AdminPage();
	return $admin->ConstructPage($pageListing, 'Theme Management');
		
}

function add_save() {
	
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
	return $admin->ConstructPage($pageListing);
	
}

function postremove() {
	
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
		return $admin->ConstructPage($pageListing);
		
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
		return $admin->ConstructPage($pageListing);
	
	}
		
}

function unknownpage() {
	$param = get_params();
		
	$admin = new AdminPage();
	header("HTTP/1.0 404 Not Found");
	$page = '
		<p>You\'ve requested the "' . $param[0] . '" page, which, uh... does not exist. Sorry?</p>
	';
	return $admin->ConstructPage($page, 'Fail whale! (404 Not Found)');
}

function landingpage() {
	
	$sidebar[] = array(
		"text" => "New comments",
		"link" => "http://google.com",
		"number" => 999
	);
	
	$sidebar[] = array(
		"text" => "Write post",
		"link" => "administration.php?posts/write",
	);
	$sidebar[] = array(
		"text" => "Manage posts",
		"link" => "administration.php?posts",
	);
	$sidebar[] = array(
		"text" => "Themes",
		"link" => "administration.php?themes",
	);
	$sidebar[] = array(
		"text" => "Authors",
		"link" => "administration.php?authors",
	);
	$sidebar[] = array(
		"text" => "Settings",
		"link" => "administration.php?settings",
	);
	$sidebar[] = array(
		"text" => "Log out",
		"link" => "administration.php?logout",
	);
	$page = '
		
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
	';	
	
	$admin = new AdminPage();
	return $admin->ConstructPage($page, null, $sidebar);
}

function loginvalidate() {
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
}

function logout() {
	session_destroy();
	header("Location: administration.php?posts&msg=1");	
}

function login_page() {
	
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
	
	return $content;
	
}

?>