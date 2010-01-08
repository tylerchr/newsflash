<?php

/*	Newsflash
	Tyler Christensen (tyler9xp@gmail.com)
	4 January 2010
*/

class post {
	
	public $id;
	public $type;
	public $title;
	public $slug;
	public $author;
	public $author_id;
	public $text;
	public $link;
	public $image;
	public $date;
	public $category;
	public $category_id;
	public $tags;
	
	public function __construct() {
		
		// Initialize an empty post
		
		$this->id =		-1;	
		$this->type =	'text';
		$this->title =	'';
		$this->slug =	'';
		$this->author =	'';
		$this->text =	'';
		$this->date =	time();
		$this->category='';
		$this->tags =	'';
	}
	
	public function TagCloud() {
		$tags = explode(";", $this->tags);
		if (count($tags) > 0) {
			foreach($tags as $value) {
				if (strlen($value) > 0) {
					$taglist[] = $value;
				}
			}
			
			if (count($taglist) > 0) {
				sort($taglist);
			}
			
			$tm = new TagManagement();
			if (count($taglist) > 0) {
				foreach($taglist as $value) {
					$tagstring .= $tm->FormatTag($value);
				}
				
				return $tagstring;
			} else {
				return "No tags";	
			}
			
		} else {
			return "No tags";	
		}
	}
	
	public function AsJSON() {
		$array = array(
			'id' => $this->id,
			'type' => $this->type,
			'title' => $this->title,
			'slug' => $this->slug,
			'author' => $this->author,
			'text' => $this->text,
			'date' => $this->date,
			'category' => $this->category,
			'tags' => $this->tags);
			
		return json_encode($array);
	}
		
}

?>