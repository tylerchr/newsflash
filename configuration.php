<?php

/* configuration.php */

// $nf['blog']['title'] =				'The Newsflash Blog';
$nf['blog']['subtitle'] =			'Blogging about a new way to blog';
// $nf['blog']['timezone'] =			-7;

$nf['paths']['siteroot'] =			'http://jauntyserv.local/newsflash/';		// RECOMMENDED
$nf['paths']['absolute'] =			$path = dirname(__FILE__) . '/';			// RECOMMENDED
$nf['paths']['packages'] =			"./packages/";
$nf['paths']['cache'] =				"cache";									// RECOMMENDED
$nf['plugins']['plugins'] =			array();

// These database settings MUST be defined in this file
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

// post settings
$nf['posts']['posts_per_page'] =	10;

// cache settings
$nf['cache']['enabled'] =			true;										// RECOMMENDED
$nf['cache']['ttl'] =				10; // in seconds							// RECOMMENDED

?>