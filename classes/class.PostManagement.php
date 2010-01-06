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
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author = $pauthor;
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
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author = $pauthor;
					$post->text = $ptext;
					$post->link = $plink;
					$post->image = $pimage;
					$post->date = $pdate;
					$post->category = $cm->CategoryNameWithID($pcategory);
					$post->ptags = $ptags;
					
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
					
					$post = new post();
					$post->id = $pid;
					$post->type = $ptype;
					$post->title = $ptitle;
					$post->slug = $pslug;
					$post->author = $pauthor;
					$post->text = $ptext;
					$post->link = $plink;
					$post->image = $pimage;
					$post->date = $pdate;
					$post->category = $cm->CategoryNameWithID($pcategory);
					$post->ptags = $ptags;
					
					return $post;
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