<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

// Needs to include code for fancy URL rewriting someday

$fakepost = new post();
$fakepost->randomPost();

$pm = new PostManagement();
// if ($pm->SavePost($fakepost)) {
	//echo "New post saved: " . $fakepost->title;	
// }

$ui = new ui();
$pc = new PageConfig('category');
$pc->listCategoryID = intval($_GET['cid']);
$ui->buildPage($pc);

?>