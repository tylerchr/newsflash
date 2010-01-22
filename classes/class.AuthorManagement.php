<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class AuthorManagement {
	
	public function GetAuthors() {
		
		if ($authors = $this->GetAuthorsWithClause('?', 'i', '1')) {
			return $authors;
		}
		
		return false;
	}
	
	public function GetCertainAuthor($aid) {
		
		if ($authors = $this->GetAuthorsWithClause('author_id = ?', 'i', $aid)) {
			return $authors;
		}
		
		return false;
	
	}
	
	public function GetAuthorsWithClause($where, $type, $param) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		$query = 'SELECT author_id, author_first_name, author_last_name, author_username, author_pwhash, author_email, author_bio, author_homepage, author_created_date FROM ' . $nf['database']['table_prefix'] . $nf['database']['author_table'] . ' WHERE ' . $where;
		if ($stmt = $sql->mysqli->prepare($query)) {
			
			$stmt->bind_param($type, $param);
			$stmt->bind_result($aid, $afirstname, $alastname, $username, $apasswordhash, $aemail, $abio, $ahomepage, $acreateddate);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					$author = new Author();
					$author->id = $aid;
					$author->first_name = $afirstname;
					$author->last_name = $alastname;
					$author->username = $username;
					$author->password_hash = $apasswordhash;
					$author->email = $aemail;
					$author->bio = $abio;
					$author->homepage = $ahomepage;
					$author->created_at = $acreateddate;
					$authors[] = $author;
				}
				
				return $authors;
				
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		return false;
	}
	
	public function SaveAuthor($author) {
		
		require(dirname(__FILE__) . '/../configuration.php');
		$sql = new mysql();
		
		if ($stmt = $sql->mysqli->prepare('INSERT INTO ' . $nf['database']['table_prefix'] . $nf['database']['author_table'] . ' (author_first_name, author_last_name, author_pwhash, author_email, author_created_date) VALUES (?, ?, ?, ?)')) {
			
			$stmt->bind_param("sssi", $author->name, $author->password_hash, $author->email, $author->created_at);
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
	
	public function ValidateAuthorCredentials($username, $pw_hash) {
		$authors = $this->GetAuthorsWithClause("author_username = ?", "s", $username);
		if (count($authors) == 1) {
			if ($authors[0]->password_hash == $pw_hash) {
				return $authors[0];	
			} else {
				return false;	
			}
		} else {
			return false;	
		}
	}
	
	public function GetAuthorNameForID($aid) {
		$author = $this->GetCertainAuthor($aid);
		if (count($author) > 0) {
			return $author[0]->first_name . ' ' . $author[0]->last_name;
		} else {
			return false;	
		}
	}
}

?>