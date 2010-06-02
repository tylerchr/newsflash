<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class Newsflash {
	
	public function GetFinal($Page) {
		
		$admin_bar = "";
		// check if we are authenticated as an administrator
		if (isset($_SESSION['auth_loggedin']) && $_SESSION['auth_loggedin'] == true) {
			$admin_bar = "<nav>
				<ul>
					<li>Logged in as me</li>
					<li>Hello</li>
				</ul>
			</nav>";	
		}
		
		return $admin_bar . $Page->Render($Page);
	}
		
}

?>
