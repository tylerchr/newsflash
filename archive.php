<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

// Needs to include code for fancy URL rewriting someday

$pm = new PostManagement();
$ui = new ui();
$pc = new PageConfig('archive');
$pc->archive['year'] = $_GET['year'];
$pc->archive['month'] = $_GET['month'];
$pc->archive['day'] = $_GET['day'];
echo $ui->buildPage($pc);

?>