<?php

$start = microtime(true);

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

$bc = new BasicCache();
$id = basename($_SERVER['REQUEST_URI']);
if ($final_page = $bc->GetCacheResource($id)) {
	$source = 'cache';
} else {
	$source = 'live generation';
	
	// generate the page
	$nf = new Newsflash();
	$options = array();
	if (isset($_GET['p'])) {
		$options['page'] = intval($_GET['p']);
	}
	$page = new ListPage($options);
	$final_page = $nf->GetFinal($page);
	
	$bc->SaveCacheResource($id, $final_page);
}

echo $final_page;

$total = round((microtime(true) - $start) * 1000, 2);
echo '<p style="display:block;text-align:center;margin-bottom: 40px;font-family:Arial;font-size:0.8em;color:#aaa;">this page loaded in <strong>', $total, 'ms</strong> of server time<br />loaded via <strong>' . $source . '</strong></p>';

?>