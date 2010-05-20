<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

$nf = new Newsflash();
$options = array(
	"page" => $_GET['p']
);
$page = new ListPage($options);

echo $nf->GetFinal($page);

?>