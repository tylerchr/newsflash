<?php

/* configuration.php */

$nf['blog']['title'] =				'The Newsflash Blog';
$nf['blog']['subtitle'] =			'Blogging about a new way to blog';
$nf['blog']['timezone'] =			-7;

$nf['paths']['siteroot'] =			'http://jauntyserv.local/newsflash/';
$nf['paths']['absolute'] =			$path = dirname(__FILE__) . '/';
$nf['paths']['packages'] =			"./packages/";
$nf['plugins']['plugins'] =			array();

$nf['database']['host'] =			'localhost';
$nf['database']['username'] =		'newsflash';
$nf['database']['password'] =		'newsflashpw';
$nf['database']['database'] =		'newsflash';
$nf['database']['table_prefix'] =	'nf_';
$nf['database']['post_table'] =		'posts';
$nf['database']['page_table'] =		'pages';
$nf['database']['category_table'] =	'categories';
$nf['database']['author_table'] =	'authors';
$nf['database']['options_table'] =	'options';

$nf['template']['main'] =			'main.php';
$nf['template']['page'] =			'page.html';
$nf['template']['author'] =			'author.html';
$nf['template']['post_text'] =		'post_text.html';
$nf['template']['post_link'] =		'post_link.html';
$nf['template']['post_image'] =		'post_image.html';

$nf['posts']['posts_per_page'] =	1;

$nf['error']['no_posts'] =			'<p class="nf-error-text">There aren\'t any posts to show here!</p>';

?>