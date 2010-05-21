<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PageManagement {
	
	public function GetPageList() {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT page_id, page_short_title, page_title, page_slug FROM ' . $nf['database']['table_prefix'] . $nf['database']['page_table'] . ' ORDER BY page_id ASC')) {
			
			$stmt->bind_result($pid, $pshorttitle, $ptitle, $pslug);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$page = new Page();
					$page->id = $pid;
					$page->short_title = $pshorttitle;
					$page->title = $ptitle;
					$page->slug = $pslug;
					
					$pages[$pid] = $page;
				}
				return $pages;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function SavePage($page) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('INSERT INTO ' . $nf['database']['table_prefix'] . $nf['database']['page_table'] . ' (page_short_title, page_title, page_slug, page_text, page_date) VALUES (?, ?, ?, ?, ?)')) {
			
			$stmt->bind_param("sssssi", $page->short_title, $page->title, $page->slug, $page->text, $page->date);
			if ($stmt->execute()) {
				return true;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function GetPages() {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT page_id, page_short_title, page_title, page_slug, page_text, page_date FROM ' . $nf['database']['table_prefix'] . $nf['database']['page_table'] . ' ORDER BY page_date DESC')) {
			
			$stmt->bind_result($pid, $pshorttitle, $ptitle, $pslug, $ptext, $pdate);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$page = new Page();
					$page->id = $pid;
					$page->short_title = $pshorttitle;
					$page->title = $ptitle;
					$page->slug = $pslug;
					$page->text = $ptext;
					$page->date = $pdate;
					
					$pages[$pid] = $page;
				}
				return $pages;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function GetCertainPage($pid) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT page_id, page_short_title, page_title, page_slug, page_text, page_date FROM ' . $nf['database']['table_prefix'] . $nf['database']['page_table'] . ' WHERE page_id = ?')) {
			
			$stmt->bind_param("i", $pid);
			$stmt->bind_result($pid, $pshorttitle, $ptitle, $pslug, $ptext, $pdate);
			if ($stmt->execute()) {
					$stmt->fetch();
					
					$page = new Page();
					$page->id = $pid;
					$page->short_title = $pshorttitle;
					$page->title = $ptitle;
					$page->slug = $pslug;
					$page->text = $ptext;
					$page->date = $pdate;
					
					return array($page);
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	
	}
		
	public function GetPagesMatchingQuery($query) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();

		if ($stmt = $sql->mysqli->prepare('SELECT page_id, page_short_title, page_title, page_slug, page_text, page_date FROM ' . $nf['database']['table_prefix'] . $nf['database']['page_table'] . ' WHERE page_short_title LIKE CONCAT(\'%\', ?, \'%\') OR page_title LIKE CONCAT(\'%\', ?, \'%\') OR page_text LIKE CONCAT(\'%\', ?, \'%\') ORDER BY page_date DESC')) {
			
			
			$stmt->bind_param("sss", $query, $query, $query);
			$stmt->bind_result($pid, $pshorttitle, $ptitle, $pslug, $ptext, $pdate);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$page = new Page();
					$page->id = $pid;
					$page->short_title = $pshorttitle;
					$page->title = $ptitle;
					$page->slug = $pslug;
					$page->text = $ptext;
					$page->date = $pdate;
					
					$pages[$pid] = $page;
				}
				return $pages;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function GetPageDates() {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();

		if ($stmt = $sql->mysqli->prepare('SELECT page_date FROM ' . $nf['database']['table_prefix'] . $nf['database']['page_table'] . ' ORDER BY page_date DESC')) {
			
			$stmt->bind_result($pdate);
			if ($stmt->execute()) {
				$core = new Core();
				while ($stmt->fetch()) {
					
					$month = date("F Y", $core->TimeFromUniversal($pdate));
					$dates[$month]['year'] = date("Y", $core->TimeFromUniversal($pdate));
					$dates[$month]['month'] = date("n", $core->TimeFromUniversal($pdate));
					$dates[$month]['day'] = date("j", $core->TimeFromUniversal($pdate));
					$dates[$month]['count']++;
				}
				return $dates;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}		
}

?>