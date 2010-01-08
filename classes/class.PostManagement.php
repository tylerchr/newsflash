<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PostManagement {	
	
	public function SavePost($post) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('INSERT INTO ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' (post_type, post_title, post_slug, post_author, post_text, post_date, post_category, post_tags) VALUES (?, ?, ?, ?, ?, ?)')) {
			
			$stmt->bind_param("sssssiss", $post->type, $post->title, $post->slug, $post->author, $post->text, $post->date, $post->category, $post->tags);
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
	
	public function GetPosts($id=-1) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' ORDER BY post_date DESC')) {
			
			$stmt->bind_result($pid, $ptype, $ptitle, $pslug, $pauthor, $ptext, $plink, $pimage, $pdate, $pcategory, $ptags);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$cm = new CategoryManagement();
					$am = new AuthorManagement();
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author_id = $pauthor;
					$post->author = $am->GetAuthorNameForID($pauthor);
					$post->text = $ptext;
					$post->link = $plink;
					$post->image = $pimage;
					$post->date = $pdate;
					$post->category = $cm->CategoryNameWithID($pcategory);
					$post->category_id = $pcategory;
					$post->tags = $ptags;
					
					$posts[$pid] = $post;
				}
				return $posts;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function GetPostsFromCategory($category_id) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($category_id == -1) {
			$query = 'SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_category IS NULL AND ? ORDER BY post_date DESC';
		} else {
			$query = 'SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_category = ? ORDER BY post_date DESC';
		}
		
		if ($stmt = $sql->mysqli->prepare($query)) {
			
			
			$stmt->bind_param("i", $category_id);
			$stmt->bind_result($pid, $ptype, $ptitle, $pslug, $pauthor, $ptext, $plink, $pimage, $pdate, $pcategory, $ptags);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$cm = new CategoryManagement();
					$am = new AuthorManagement();
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author_id = $pauthor;
					$post->author = $am->GetAuthorNameForID($pauthor);
					$post->text = $ptext;
					$post->link = $plink;
					$post->image = $pimage;
					$post->date = $pdate;
					$post->category = $cm->CategoryNameWithID($pcategory);
					$post->category_id = $pcategory;
					$post->tags = $ptags;
					
					$posts[$pid] = $post;
				}
				return $posts;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
			
	}
	
	public function GetPostsByAuthor($author_id) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_author = ? ORDER BY post_date DESC')) {
			
			
			$stmt->bind_param("i", $author_id);
			$stmt->bind_result($pid, $ptype, $ptitle, $pslug, $pauthor, $ptext, $plink, $pimage, $pdate, $pcategory, $ptags);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$cm = new CategoryManagement();
					$am = new AuthorManagement();
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author_id = $pauthor;
					$post->author = $am->GetAuthorNameForID($pauthor);
					$post->text = $ptext;
					$post->link = $plink;
					$post->image = $pimage;
					$post->date = $pdate;
					$post->category = $cm->CategoryNameWithID($pcategory);
					$post->category_id = $pcategory;
					$post->tags = $ptags;
					
					$posts[$pid] = $post;
				}
				return $posts;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
			
	}
	
	public function GetCategoryTotals() {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT post_id, post_category FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'])) {
			
			$stmt->bind_result($pid, $pcategory);
			if ($stmt->execute()) {
				$category_list = array();
	
				while ($stmt->fetch()) {
					if (!is_null($pcategory)) {
						$category_list[$pcategory]++;
					} else {
						$category_list[-1]++;
					}
					
				}
				return $category_list;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;
		}
		
		return false;
	}
	
	public function GetCertainPost($pid) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_id = ?')) {
			
			$stmt->bind_param("i", $pid);
			$stmt->bind_result($pid, $ptype, $ptitle, $pslug, $pauthor, $ptext, $plink, $pimage, $pdate, $pcategory, $ptags);
			if ($stmt->execute()) {
					$stmt->fetch();
					
					$cm = new CategoryManagement();
					$am = new AuthorManagement();
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author_id = $pauthor;
					$post->author = $am->GetAuthorNameForID($pauthor);
					$post->text = $ptext;
					$post->link = $plink;
					$post->image = $pimage;
					$post->date = $pdate;
					$post->category = $cm->CategoryNameWithID($pcategory);
					$post->category_id = $pcategory;
					$post->tags = $ptags;
					
					return array($post);
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	
	}
	
	public function GetPostsTaggedWith($tag) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();

		if ($stmt = $sql->mysqli->prepare('SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_tags LIKE CONCAT(\'%\', ?, \'%\') ORDER BY post_date DESC')) {
			
			
			$stmt->bind_param("s", $tag);
			$stmt->bind_result($pid, $ptype, $ptitle, $pslug, $pauthor, $ptext, $plink, $pimage, $pdate, $pcategory, $ptags);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$cm = new CategoryManagement();
					$am = new AuthorManagement();
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author_id = $pauthor;
					$post->author = $am->GetAuthorNameForID($pauthor);
					$post->text = $ptext;
					$post->link = $plink;
					$post->image = $pimage;
					$post->date = $pdate;
					$post->category = $cm->CategoryNameWithID($pcategory);
					$post->category_id = $pcategory;
					$post->tags = $ptags;
					
					$posts[$pid] = $post;
				}
				return $posts;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function GetPostsMatchingQuery($query) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();

		if ($stmt = $sql->mysqli->prepare('SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_title LIKE CONCAT(\'%\', ?, \'%\') OR post_text LIKE CONCAT(\'%\', ?, \'%\') OR post_tags LIKE CONCAT(\'%\', ?, \'%\') ORDER BY post_date DESC')) {
			
			
			$stmt->bind_param("sss", $query, $query, $query);
			$stmt->bind_result($pid, $ptype, $ptitle, $pslug, $pauthor, $ptext, $plink, $pimage, $pdate, $pcategory, $ptags);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$cm = new CategoryManagement();
					$am = new AuthorManagement();
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author_id = $pauthor;
					$post->author = $am->GetAuthorNameForID($pauthor);
					$post->text = $ptext;
					$post->link = $plink;
					$post->image = $pimage;
					$post->date = $pdate;
					$post->category = $cm->CategoryNameWithID($pcategory);
					$post->category_id = $pcategory;
					$post->tags = $ptags;
					
					$posts[$pid] = $post;
				}
				return $posts;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function GetPostDates() {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();

		if ($stmt = $sql->mysqli->prepare('SELECT post_date FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' ORDER BY post_date DESC')) {
			
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
	
	public function GetPostsFrom() {
		$months = range(1,12);
		$days = range(1,31);
		
		$core = new Core();
		
		// Validate ranges
		if (func_num_args() >= 1) {
			$year = func_get_arg(0);
			if ($year < 0) {
				$year = date("Y", time());	
			}
		}
		
		if (func_num_args() >= 2) {				
			$month = func_get_arg(1);
			if (!in_array($month, $months)) {
				return $this->GetPostsFrom($year);
			}
		}
		
		if (func_num_args() >= 3) {
			$day = func_get_arg(2);
			if (!in_array($day, $days)) {
				return $this->GetPostsFrom($year, $month);
			}
		}
		
		// Get proper lows and highs				
		if (func_num_args() == 1) {
			$lowend = mktime(0,0,0,1,1,$year);
			$highend = mktime(23,59,59,12,31,$year);
			// year
		} else if (func_num_args() == 2) {
			$lowend = mktime(0,0,0,$month,1,$year);
			$highend = mktime(23,59,59,$month,cal_days_in_month(CAL_GREGORIAN,$month,$year),$year);
			// year+month
		} else if (func_num_args() == 3) {
			$lowend = mktime(0,0,0,$month,$day,$year);
			$highend = mktime(23,59,59,$month,$day,$year);
			// year+month+day
		} else {
			return $this->GetPostsFrom(date("Y", time()));
		}
		
		// Correct for Universal time
		$lowend = $core->TimeToUniversal($lowend);
		$highend = $core->TimeToUniversal($highend);
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();

		if ($stmt = $sql->mysqli->prepare('SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_date >= ? AND post_date <= ? ORDER BY post_date DESC')) {
			
			
			$stmt->bind_param("ii", $lowend, $highend);
			$stmt->bind_result($pid, $ptype, $ptitle, $pslug, $pauthor, $ptext, $plink, $pimage, $pdate, $pcategory, $ptags);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$cm = new CategoryManagement();
					$am = new AuthorManagement();
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author_id = $pauthor;
					$post->author = $am->GetAuthorNameForID($pauthor);
					$post->text = $ptext;
					$post->link = $plink;
					$post->image = $pimage;
					$post->date = $pdate;
					$post->category = $cm->CategoryNameWithID($pcategory);
					$post->tags = $ptags;
					
					$posts[$pid] = $post;
				}
				return $posts;
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