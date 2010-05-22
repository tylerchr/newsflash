<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class ArchivePage extends Page {
	
	private $year;
	private $month;
	private $day;
	
	public function SetPageVariables($vars) {
		if ($vars['year'] > 0)
			$this->SetYear($vars['year']);
		if ($vars['month'] > 0)	
			$this->SetMonth($vars['month']);
		if ($vars['day'] > 0)
			$this->SetDay($vars['day']);
	}
	
	public function SetYear($year) {
		if (is_numeric($year)) {
			$this->year = $year;	
		} else {
			$this->year = NULL;
		}
	}
	
	public function SetMonth($month) {
		if (is_numeric($month)) {
			$this->month = $month;	
		} else {
			$this->month = NULL;
		}
	}
	
	public function SetDay($day) {
		if (is_numeric($day)) {
			$this->day = $day;	
		} else {
			$this->day = NULL;
		}
	}
	
	public function GetYear() {
		return $this->year;	
	}
	
	public function GetMonth() {
		return $this->month;	
	}
	
	public function GetDay() {
		return $this->day;	
	}
	
	public function TimePeriodString() {
		
		if (!is_null($this->GetYear())) {
			$string = $this->GetYear();
		}
		
		if (!is_null($this->GetYear()) && !is_null($this->GetMonth())) {
			$string = date( 'F', mktime(1, 0, 0, $this->GetMonth())) . ' ' . $this->GetYear();
		}
		
		if (!is_null($this->GetYear()) && !is_null($this->GetMonth()) && !is_null($this->GetDay())) {
			$string = $this->GetDay() . ' ' . date( 'F', mktime(1, 0, 0, $this->GetMonth())) . ' ' . $this->GetYear();
		}
		
		return $string;	
	}
	
	public function ConstructContents() {
		require(dirname(__FILE__) . '/../configuration.php');
		$opt = new Options();
		require($opt->ValueForKey("paths/absolute") . 'packages/packages.php');
		
		$pm = new PostManagement();
		$post_data = $pm->GetPostsFrom($this->GetYear(), $this->GetMonth(), $this->GetDay(), $this->getPageData());
		$this->setPageData(array("page" => $post_data['page'], "results" => $post_data['results']));
		$PageConfig->variables->nf_page_title = 'Archives for ' . $this->TimePeriodString();
		
		// render the page
		if ($PageConfig->PostListStyle == 'condensed') {
			$PageConfig->variables->nf_posts = $this->FormatCondensedPosts($post_data['posts'], $PageConfig);
		} else {
			if (count($post_data['posts']) > 0) {
				foreach ($post_data['posts'] as $single_post) {
					$PageConfig->variables->nf_posts .= $this->FormatPost($single_post, $PageConfig);
				}
			} else {
				require(dirname(__FILE__) . '/../configuration.php');
				$PageConfig->variables->nf_posts = $opt->ValueForKey("error/no_posts");	
			}
		}
		
		return $PageConfig;
	}
	
	
		
}

?>