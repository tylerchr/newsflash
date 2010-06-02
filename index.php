<?php

$start = microtime(true);

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');

$nf = new Newsflash();
$options = array();
if (isset($_GET['p'])) {
	$options['page'] = intval($_GET['p']);
}

$page = new ListPage($options);
echo $nf->GetFinal($page);

$total = round((microtime(true) - $start) * 1000, 2);
echo '<p style="display:block;text-align:center;margin-bottom: 40px;font-family:Arial;font-size:0.8em;color:#aaa;">this page loaded in <strong>', $total, 'ms</strong> of server time</p>';

?>