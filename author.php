<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

$nf = new Newsflash();
$page = new AuthorPage(intval($_GET['author']));
echo $nf->GetFinal($page);

?>