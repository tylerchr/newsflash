<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

require('classes/classes.php');
require('configuration.php');
require('administration_pages.php');

function get_params() {

	$vars = explode('?', $_SERVER['REQUEST_URI']);
	$split = explode('&', $vars[1]);
	return $split;
	
}

function bootstrap_page() {
	
	$split = get_params();
	
	if (strlen($split[0]) > 0) {
	
		$function_name = str_ireplace("/", "_", $split[0]);
		$function_name = str_ireplace("-", "", $function_name);
		if (count($split) > 0) {
			$function_params = $split;
			array_shift($function_params);
		}
		
		if (function_exists($function_name)) {
			return call_user_func($function_name, $function_params);
		}
		
		return unknownpage();
		
	}
	
	return landingpage();

}

// Validate user login
session_start();
$split = get_params();
if ($_SESSION['auth_loggedin'] == true) {
	echo bootstrap_page();
} else if ($split[0] == 'login-validate') {
	echo loginvalidate();
} else {
	echo login_page();
}

?>