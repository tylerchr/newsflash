<?php

require('classes/classes.php');

function FormatMarkdown($input) {	
	// Markdown-format the text if Markdown is available, otherwise return the input text
	$pf = new Packages();
	$opt = new Options();
	if ($pf->PackageEnabled('markdown') && !function_exists('Markdown')) {
		$markdown_path = $opt->ValueForKey("paths/absolute") . 'packages/pkg.markdown/markdown.php';
		require($pf->ScriptForPackage('markdown'));
	}
	
	if (function_exists('Markdown')) {
		$post_text = Markdown($input);
	} else {
		$post_text = $input;
	}
	
	return $post_text;
}

function FormatTimestamp($timestamp) {
	return date("D, d M Y H:i:s T", $timestamp);	
}

$opt = new Options();

// get the posts
$pm = new PostManagement();
$posts = $pm->GetRecentPosts();
foreach ($posts['posts'] as $post) {
	$new_posts[] = $post;
	if ($post->type == "link") {
		$link = $post->link;
	} else {
		$link = $opt->ValueForKey("paths/siteroot") . "post.php?post=" . $post->id;
	}
	$rss_items[] = new phparess_item(array(
		"title" => $post->title,
		"link" => $link,
		"description" => FormatMarkdown($post->text),
		"pubDate" => FormatTimestamp($post->date)
	));
}

// create a channel
$channel = new phparess_channel(array(
	"title" => $opt->ValueForKey("blog/title"),
	"link" => $opt->ValueForKey("paths/siteroot"),
	"description" => $opt->ValueForKey("blog/subtitle"),
));
$channel->addItems($rss_items);

// create a phparess feed, and display it
$rss = new phparess();
$rss->setChannel($channel);
header('Content-type: text/plain');
//header('Content-type: application/rss+xml');
echo $rss;

/*

// display the content
header('Content-type: text/plain');

// create a channel
$channel = new phparess_channel(array(
	"title"=>"phparess test feed",
	"link"=>"http://github.com/tylerchr/phparess",
	"description"=>"A set of PHP classes for writing basic RSS feeds"
));
$channel->addItems($items);

// create a phparess feed, and display it
$rss = new phparess();
$rss->setChannel($channel);

header('Content-type: application/rss+xml');
echo $rss;

*/

?>