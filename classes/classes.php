<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

$path = dirname(__FILE__) . '/';
require($path . '../configuration.php');

$classlist = array(	'Newsflash',
					'Page',
					'ListPage',
					'CategoryPage',
					'TagPage',
					'ArchivePage',
					'PostingPage',
					'AuthorPage',
					'SearchPage',
					'LiteralPage',
					'Core',
					'BasicCache',
					'Options',
					'Packages',
					'mysql',
					'post',
					'PostManagement',
					'PageConfig',
					'Category',
					'CategoryManagement',
					'TagManagement',
					'PageVariables',
					'Page',
					'PageManagement',
					'Author',
					'AuthorManagement',
					'admin/AdminPage'
				  );

foreach ($classlist as $name) {
	
	$thing = explode('/', $name);
	if (count($thing) > 1) {
		$path = $thing[0] . '/';
		$name = $thing[1];	
	} else {
		$path = '';	
	}
	
	if (!class_exists($name)) {
		require($path . 'class.' . $name . '.php');
	}
}

?>