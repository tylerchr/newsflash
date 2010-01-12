<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

// Needs to include code for fancy URL rewriting someday

$nf = new Newsflash();
$page = new LiteralPage(intval($_GET['page']));
echo $nf->GetFinal($page);

?>