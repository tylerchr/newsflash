<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PostManagement {
	
	// ---- ---- ---- ---- ---- ---- ---- ---- ---- 
	//	Methods that perform WRITES to the database
	// ---- ---- ---- ---- ---- ---- ---- ---- ---- 
	
	public function SavePost($post) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('INSERT INTO ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' (post_type, post_title, post_slug, post_author, post_text, post_date, post_category, post_tags) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')) {
			
			$stmt->bind_param("sssssiss", $post->type, $post->title, $post->slug, $post->author, $post->text, $post->date, $post->category, $post->tags);
			if ($stmt->execute()) {
				return true;
			} else {
				echo 'Execution error: ' . $sql->error();	
			}
		
		} else {
			echo 'Preparation error: ' . $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function UpdatePost($post) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('UPDATE ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' SET post_type=?, post_title=?, post_slug=?, post_author=?, post_text=?, post_date=?, post_category=?, post_tags=? WHERE post_id=?')) {
			
			$stmt->bind_param("sssssissi", $post->type, $post->title, $post->slug, $post->author, $post->text, $post->date, $post->category, $post->tags, $post->id);
			if ($stmt->execute()) {
				return true;
			} else {
				echo 'Execution error: ' . $sql->error();	
			}
		
		} else {
			echo 'Preparation error: ' . $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function SavePostData($post) {
		if ($this->DoesPostExist($post->id)) {
			// post with ID exists, so we'll update it
			return $this->UpdatePost($post);
		} else {
			// error, no ID given and can't update with no ID -- creating new post instead!
			return $this->SavePost($post);
		}		
	}
	
	public function DeletePost($post_id) {
		$pid = intval($post_id);
		if ($this->DoesPostExist($pid)) {

			require(dirname(__FILE__) . '/../configuration.php');
			$sql = new mysql();
			
			if ($stmt = $sql->mysqli->prepare('DELETE FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_id=?')) {
				
				$stmt->bind_param("i", $pid);
				if ($stmt->execute()) {
					return true;
				} else {
					echo $sql->error();	
				}
			
			} else {
				echo $sql->mysqli->error;	
			}
			
			return false;

		} else {
			// continue	
		}	
	}
	
	// ---- ---- ---- ---- ---- ---- ---- ---- ---- 
	//	Methods that only READ the database
	// ---- ---- ---- ---- ---- ---- ---- ---- ---- 
	
	public function DoesPostExist($post_id) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT post_id FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_id = ?')) {
			
			$stmt->bind_param("i", $post_id);
			if ($stmt->execute()) {
				$stmt->store_result();
				$row_ct = $stmt->num_rows;
				$stmt->close();
				
				if ($row_ct > 0) {
					return true;
				} else {
					return false;	
				}
			} else {
				echo 'Execution error: ' . $sql->error();	
			}
		
		} else {
			echo 'Preparation error: ' . $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function GetPosts($id=-1) {
		return $this->GetPostsThroughFilter();
	}
	
	public function GetPostsFromCategory($category_id) {
		$filters['post_category'] = $category_id>0 ? $category_id : NULL;;
		return $this->GetPostsThroughFilter($filters);	
	}
	
	public function GetPostsByAuthor($author_id) {
		$filters['post_author'] = $author_id;
		return $this->GetPostsThroughFilter($filters);
	}
	
	public function GetCertainPost($post_id) {
		$filters['post_id'] = $post_id;
		$posts = $this->GetPostsThroughFilter($filters);
		return array($posts[$post_id]);
	}
	
	public function GetPostsTaggedWith($tag) {
		$filters['post_tags'] = "%" . $tag . "%";
		return $this->GetPostsThroughFilter($filters);
	}
	
	public function GetPostsMatchingQuery($query) {
		$filters['_separator'] = "OR";
		$filters['post_title'] = "%" . $query . "%";
		$filters['post_text'] = "%" . $query . "%";		
		$filters['post_tags'] = "%" . $query . "%";
		return $this->GetPostsThroughFilter($filters);
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
		
		$filters['post_date'] = array($lowend, $highend);
		return $this->GetPostsThroughFilter($filters);
		
	}
	
	public function GetPostsThroughFilter($filter=array(), $page=1) {
		
		// generate the WHERE statement
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				
				// range array
				if (is_array($value) && count($value) == 2 && is_int($value[0]) && is_int($value[1])) {
					$whereLine[] = $key . " >=  ?";
					$whereLine[] = $key . " <=  ?";
					$whereVariableTypes[] = "i";
					$whereVariableTypes[] = "i";
					$whereVariables[] = $value[0];
					$whereVariables[] = $value[1];
					
				// null item
				} else if (is_null($value)) {
					$whereLine[] = $key . " IS NULL";	
					
				// integer values
				} else if (is_int($value)) {
					$whereLine[] = $key . " = ?";
					$whereVariableTypes[] = "i";
					$whereVariables[] = $value;
				
				// string value
				} else if (is_string($value) && substr($key, 0, 1) != "_") {
					$whereVariableTypes[] = "s";
					// LIKE-style queries
					if (substr($value,0,1) == "%" && substr($value,-1) == "%") {
						$newValue = substr(substr($value, 1), 0, strlen($value)-2);
						$whereLine[] = $key . " LIKE CONCAT('%', ?, '%')";
						$whereVariables[] = $newValue;
					} else {						
						$whereLine[] = $key . " = ?";
						$whereVariables[] = $value;
					}
					
				// unknown values value (toss the filter variables)
				} else if (substr($key, 0, 1) != "_") {
					// I suck as a developer, because this should never be reached
					echo "The infamous fourth type: " . $value . "<br />";
				}
			}
			
			if (strlen($filter['_separator']) > 0) {
				$separator = $filter['_separator'];
			} else {
				$separator = "AND";	
			}
			
			$query_where = " WHERE ";
			$query_where .= implode(" " . $separator . " ", $whereLine);
			if (count($whereVariableTypes) > 0) {
				array_unshift($whereVariables, implode($whereVariableTypes));
			}
		}
		
		// generate the LIMIT statement
		$limit_statement = "";
		if ($page > 0) {
			$limit = 2;
			$offset = ($page-1) * $limit;
			$limit_statement = " LIMIT " . $limit . " OFFSET " . $offset . " ";	
		}
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		$query = 'SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . $query_where . ' ORDER BY post_date DESC' . $limit_statement . ";";
		
		if ($stmt = $sql->mysqli->prepare($query)) {
			
			// attach the parameters, if there are any
			if (count($whereVariableTypes) > 0) {
				call_user_func_array(array($stmt, 'bind_param'), $whereVariables);
			}
			
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
	
	public function GenerateSlug($input) {
		$output = str_replace(" ", '-', $input);
		$output = strtolower(preg_replace("/[^a-zA-Z0-9-\s]/", "", $output));
		return $output;
	}
		
}

?>