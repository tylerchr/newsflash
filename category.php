<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

// Needs to include code for fancy URL rewriting someday

$ui = new ui();
$pc = new PageConfig('category');
$pc->listCategoryID = intval($_GET['cid']);
echo $ui->buildPage($pc);

?>