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
	
	public function GetPosts($id=-1, $page=0) {
		return $this->ReturnPosts(null, $page);
	}
	
	public function GetPostsFromCategory($category_id, $page=0) {
		$filters['post_category'] = $category_id>0 ? $category_id : NULL;;
		return $this->ReturnPosts($filters, $page);	
	}
	
	public function GetPostsByAuthor($author_id, $page=0) {
		$filters['post_author'] = $author_id;
		return $this->ReturnPosts($filters, $page);
	}
	
	public function GetCertainPost($post_id, $page=0) {
		$filters['post_id'] = $post_id;
		return $this->ReturnPosts($filters, $page);
	}
	
	public function GetPostsTaggedWith($tag, $page=0) {
		$filters['post_tags'] = "%" . $tag . "%";
		return $this->ReturnPosts($filters, $page);
	}
	
	public function GetPostsMatchingQuery($query, $page=0) {
		$filters['_separator'] = "OR";
		$filters['post_title'] = "%" . $query . "%";
		$filters['post_text'] = "%" . $query . "%";		
		$filters['post_tags'] = "%" . $query . "%";
		return $this->ReturnPosts($filters, $page);
	}
	
	public function ReturnPosts($filter, $page) {
		return array(
			"posts" => $this->GetPostsThroughFilter($filter, $page),
			"page" => $page['page'],
			"results" => $this->CountPostsForFilter($filter)
		);	
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
	
	public function GetPostsFrom($year=0, $month=0, $day=0, $page=0) {
		
		// default precision to year only
		$precision = 0;
			// 0, year
			// 1, year + month
			// 2, year + month + day
		
		// fall back to this year
		if ($year < 0)
			$year = date("Y", time());
		
		$months = range(1,12);
		if (in_array($month, $months)) {
			// we have a valid month
			$precision = 1;
			$days = range(1,31);	
			if (in_array($day, $days)) {
				// we have a valid day
				$precision = 2;
			}
				
		}
		
		switch ($precision) {
			case 1:
				// year + month
				$lowend = mktime(0,0,0,$month,1,$year);
				$highend = mktime(23,59,59,$month,cal_days_in_month(CAL_GREGORIAN,$month,$year),$year);
				break;
			case 2:
				// year + month + day
				$lowend = mktime(0,0,0,$month,$day,$year);
				$highend = mktime(23,59,59,$month,$day,$year);
				break;
			default:
				// default, which is the same as year only
				$lowend = mktime(0,0,0,1,1,$year);
				$highend = mktime(23,59,59,12,31,$year);
				break;
		}
		
		// Correct for Universal time
		$core = new Core();
		$lowend = $core->TimeToUniversal($lowend);
		$highend = $core->TimeToUniversal($highend);
		
		$filters['post_date'] = array($lowend, $highend);
		return $this->ReturnPosts($filters, $page);
		
	}
	
	private function _GenerateQueryFromFilterAndPage($filter=array(), $page=0, $count=false) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		
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
		if ($page['page'] > 0) {
			$offset = ($page['page']-1) * $page['limit'];
			$limit_statement = " LIMIT " . $page['limit'] . " OFFSET " . $offset . " ";	
		}
		
		$query = 'SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date, post_category, post_tags FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . $query_where . ' ORDER BY post_date DESC' . $limit_statement . ";";
		
		return array("query" => $query, "variable_types" => $whereVariableTypes, "variables" => $whereVariables);
		
	}
	
	public function CountPostsForFilter($filter=array()) {
		$metaquery = $this->_GenerateQueryFromFilterAndPage($filter, $page);
		$index = strpos($metaquery['query'], " FROM");
		$count_query = "SELECT COUNT(*) " . substr($metaquery['query'], $index);
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare($count_query)) {
			
			// attach the parameters, if there are any
			if (count($metaquery['variable_types']) > 0) {
				call_user_func_array(array($stmt, 'bind_param'), $metaquery['variables']);
			}
			
			if ($stmt->execute()) {
				$stmt->bind_result($count);
				$stmt->fetch();
				return $count;
			}
		
		}
		
		return -1;
	}
	
	public function GetPostsThroughFilter($filter=array(), $page=0) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		$metaquery = $this->_GenerateQueryFromFilterAndPage($filter, $page);
		
		$query = $metaquery['query'];
		
		if ($stmt = $sql->mysqli->prepare($query)) {
			
			// attach the parameters, if there are any
			if (count($metaquery['variable_types']) > 0) {
				call_user_func_array(array($stmt, 'bind_param'), $metaquery['variables']);
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