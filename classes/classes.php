<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

$path = dirname(__FILE__) . '/';
require($path . '../configuration.php');

$classlist = array(	'Core',
					'ui',
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
					'AuthorManagement'
				  );

foreach ($classlist as $name) {
	if (!class_exists($name)) {
		require('class.' . $name . '.php');
	}
}

?>