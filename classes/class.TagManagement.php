<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class TagManagement {
	
	public function GetAllTags() {
		
		$sql = new mysql();
		$opt = new Options();
		
		if ($stmt = $sql->mysqli->prepare('SELECT post_tags FROM ' . $opt->ValueForKey("database/table_prefix") . $opt->ValueForKey("database/post_table"))) {
			
			$stmt->bind_result($ptags);
			if ($stmt->execute()) {
				while ($stmt->fetch()) {
					
					$tagstring = explode(";", $ptags);
					if (count($tagstring) > 0) {
						foreach ($tagstring as $value) {
							if (strlen($value) > 0) {
								
								if (!isset($tags[$value]))
									$tags[$value] = 0;
								
								$tags[$value]++;
							}
						}	
					}
				}
				return $tags;
			} else {
				echo $sql->error();	
			}
		
		} else {
			echo $sql->mysqli->error;	
		}
		
		return false;
	}
	
	public function FormatTag($tag) {
		return '<a href="tag.php?tag=' . $tag . '">' . $tag . '</a>&nbsp;';
	}
		
}

?>