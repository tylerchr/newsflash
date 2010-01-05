<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class PostManagement {	
	
	public function SavePost($post) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('INSERT INTO ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' (post_type, post_title, post_slug, post_author, post_text, post_date) VALUES (?, ?, ?, ?, ?, ?)')) {
			
			$stmt->bind_param("sssssi", $post->type, $post->title, $post->slug, $post->author, $post->text, $post->date);
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
		
		if ($stmt = $sql->mysqli->prepare('SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' ORDER BY post_date DESC')) {
			
			$stmt->bind_result($pid, $ptype, $ptitle, $pslug, $pauthor, $ptext, $plink, $pimage, $pdate);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
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
	
	public function GetCertainPost($pid) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('SELECT post_id, post_type, post_title, post_slug, post_author, post_text, post_link, post_image, post_date FROM ' . $nf['database']['table_prefix'] . $nf['database']['post_table'] . ' WHERE post_id = ?')) {
			
			$stmt->bind_param("i", $pid);
			$stmt->bind_result($pid, $ptype, $ptitle, $pslug, $pauthor, $ptext, $plink, $pimage, $pdate);
			if ($stmt->execute()) {
					$stmt->fetch();
					
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