<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

// Needs to include code for fancy URL rewriting someday

/*
$ui = new ui();
$pc = new PageConfig('tag');
$pc->listTag = $_GET['tag'];
echo $ui->buildPage($pc);
*/

$nf = new Newsflash();
$page = new TagPage();
echo $nf->GetFinal($page);

?>