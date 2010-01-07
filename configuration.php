<?php

/* configuration.php */

$nf['blog']['title'] =				'The Newsflash Blog';
$nf['blog']['subtitle'] =			'Blogging about a way to blog';
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
$nf['database']['category_table'] =	'categories';
$nf['database']['user_table'] =		'users';

$nf['template']['main'] =			'main.html';
$nf['template']['post_text'] =		'post_text.html';
$nf['template']['post_link'] =		'post_link.html';
$nf['template']['post_image'] =		'post_image.html';

?>