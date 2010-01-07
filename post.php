<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

// Needs to include code for fancy URL rewriting someday

$pc = new PageConfig('single');
$pc->SinglePostID = intval($_GET['post']);

$ui = new ui();
echo $ui->buildPage($pc);

?>