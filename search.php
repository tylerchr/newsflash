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
$ui = new ui();
$pc = new PageConfig('search');
$pc->searchQuery = $_GET['q'];
echo $ui->buildPage($pc);

?>