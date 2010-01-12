<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

$nf = new Newsflash();
$page = new ArchivePage($_GET['year'], $_GET['month'], $_GET['day']);
echo $nf->GetFinal($page);

?>