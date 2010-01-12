<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class ArchivePage extends Page {
	
	private $year;
	private $month;
	private $day;
	
	public function __construct($year=-1, $month=-1, $day=-1) {
		if ($year > 0) {
			$this->SetYear($year);
		}
		
		if ($month > 0) {
			$this->SetMonth($month);
		}
		
		if ($day > 0) {
			$this->SetDay($day);
		}
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
		require($nf['paths']['absolute'] . 'packages/packages.php');
		
		$pm = new PostManagement();
		$posts = $pm->GetPostsFrom($this->GetYear(), $this->GetMonth(), $this->GetDay());
		$PageConfig->variables->nf_page_title = 'Archives for ' . $this->TimePeriodString();
		
		// render the page
		if ($PageConfig->PostListStyle == 'condensed') {
			$PageConfig->variables->nf_posts = $this->FormatCondensedPosts($posts, $PageConfig);
		} else {
			if (count($posts) > 0) {
				foreach ($posts as $single_post) {
					$PageConfig->variables->nf_posts .= $this->FormatPost($single_post, $PageConfig);
				}
			} else {
				require(dirname(__FILE__) . '/../configuration.php');
				$PageConfig->variables->nf_posts = $nf['error']['no_posts'];	
			}
		}
		
		return $PageConfig;
	}
	
	
		
}

?>