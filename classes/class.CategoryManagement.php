<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class CategoryManagement {
	
	public function GetAllCategories() {
		
		$sql = new mysql();
		$opt = new Options();
		
		if ($stmt = $sql->mysqli->prepare('SELECT category_id, category_name FROM ' . $opt->ValueForKey("database/table_prefix") . $opt->ValueForKey("database/category_table"))) {
			
			$stmt->bind_result($cid, $cname);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$category = new Category();
					$category->id = $cid;
					$category->name = $cname;
					
					$categories[$cid] = $category;
				}
				return $categories;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function GetCategoryWithID($category_id) {
		
		$sql = new mysql();
		$opt = new Options();
		
		if ($stmt = $sql->mysqli->prepare('SELECT category_id, category_name FROM ' . $opt->ValueForKey("database/table_prefix") . $opt->ValueForKey("database/category_table") . ' WHERE category_id=?')) {
			
			$stmt->bind_param("i", $category_id);
			$stmt->bind_result($cid, $cname);
			if ($stmt->execute()) {
				$stmt->store_result();
				
				if ($stmt->num_rows > 0) {
					$stmt->fetch();
					$category = new Category();
					$category->id = $cid;
					$category->name = $cname;
					return $category;
				}
			} else {
				echo $sql->error();	
			}
		} else {
			echo $sql->mysqli->error;	
		}
		return false;
		
	}
	
	public function CategoryNameWithID($category_id) {
		if ($cat = $this->GetCategoryWithID($category_id)) {
			return $cat->name;
		} else {
			return "Unfiled";	
		}
		
	}
	
	public function CreateNewCategory($new_category) {
		
		$sql = new mysql();
		$opt = new Options();
		
		if ($stmt = $sql->mysqli->prepare('INSERT INTO ' . $opt->ValueForKey("database/table_prefix") . $opt->ValueForKey("database/category_table") . ' (category_name) VALUES (?)')) {
			
			$stmt->bind_param("s", $new_category->name);
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
	
	public function GetListOfUsedCategories() {
		$pm = new PostManagement();
		$posts = $pm->GetCategoryTotals();
		foreach ($posts as $key => $value) {
			$new_array[$this->CategoryNameWithID($key)] = $value;
		}
		return $posts;
	}
		
}

?>