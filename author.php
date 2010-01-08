<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

// Needs to include code for fancy URL rewriting someday

$pc = new PageConfig('author');
$pc->AuthorID = intval($_GET['author']);

$ui = new ui();
echo $ui->buildPage($pc);

?>