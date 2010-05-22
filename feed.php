<?php

require('classes/classes.php');

class Feed {
	
	public function __construct() {
		// nothing
	}
	
	public function __toString() {
		$feed = $this->_generate_feed();
		if (is_string($feed)) {
			return $feed;	
		} else {
			return "";	
		}
	}
	
	//
	// private methods
	//
	
	private function _generate_feed() {
		
		$opt = new Options();
		$pf = new Packages();
		if ($pf->ImportPackage('FeedWriter')) {

			//Creating an instance of FeedWriter class. 
			//The constant ATOM is passed to mention the version
			
			if ($opt->ContainsKey('feed/format')) {	
				$format = $opt->ValueForKey('feed/format');
			} else {
				$format = "atom";
			}
			
			if ($format == "rss") {
				$feed = new FeedWriter(RSS2);
				$date_format = DATE_RSS;
			} else {
				$feed = new FeedWriter(ATOM);
				$date_format = DATE_ATOM;
			}
		
			//Setting the channel elements
			//Use wrapper functions for common elements
			$feed->setTitle($opt->ValueForKey("blog/title"));
			$feed->setLink($opt->ValueForKey("paths/siteroot"));
			
			//For other channel elements, use setChannelElement() function
			$feed->setChannelElement('updated', date($date_format , time()));
			$feed->setChannelElement('author', array('name'=>'Tyler Christensen'));
					
			//Adding a feed. Genarally this protion will be in a loop and add all feeds.
			$pm = new PostManagement();
			$posts = $pm->GetRecentPosts();
			foreach ($posts['posts'] as $post) {
				
				if ($post->type == "link") {
					$link = $post->link;
				} else {
					$link = $opt->ValueForKey("paths/siteroot") . "post.php?post=" . $post->id;
				}
		
				//Create an empty FeedItem
				$newItem = $feed->createNewItem();
				
				//Add elements to the feed item
				//Use wrapper functions to add common feed elements
				$newItem->setTitle($post->title);
				$newItem->setLink($link);
				$newItem->addElement("published", date($date_format, $post->date));
				$newItem->setDate($post->date);
				//Internally changed to "summary" tag for ATOM feed
				$text = $this->_clean_string($this->_format_markdown($post->text));
				$newItem->setDescription($text);
							
				//Now add the feed item	
				$feed->addItem($newItem);
				
			}
			
			return $feed->genarateFeed();	
		}	
	}
	
	private function _clean_string($string) {
 
    return str_replace(array(
			chr(145), 
			chr(146), 
			chr(147), 
			chr(148), 
			chr(151)
		),
		array(
			"'", 
		    "'", 
		    '"', 
		    '"', 
		    '-'
		), $string);
	
	}

	private function _format_markdown($input) {	
		// Markdown-format the text if Markdown is available, otherwise return the input text
		$pf = new Packages();
		if ($pf->ImportPackage('Markdown')) {
			return Markdown($input);
		}
		return $input;
	}
	
}

$feed = new Feed();
echo $feed;

?>