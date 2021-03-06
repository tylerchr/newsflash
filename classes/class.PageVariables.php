<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PageVariables {
	
	public $variables;
	
	public function __construct() {
		
		$opt = new Options();
		
		$this->nf_blog_title =		$opt->ValueForKey("blog/title");
		$this->nf_blog_subtitle =	$opt->ValueForKey("blog/subtitle");
		$this->nf_title =			NULL;
		$this->nf_headscape =		NULL;
		$this->nf_search_bar =		'<form action="search.php" method="get">' .
									'	<input type="search" name="q" />' .
									'	<input type="submit" value="Go" />' .
									'</form>';
		$this->dev_content =		"Content!";
		$this->nf_end =				"";
		
	}
		
}

?>