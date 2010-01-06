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
	public $text;
	public $link;
	public $image;
	public $date;
	public $category;
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
	
	public function randomPost() {
		// Fake ID
		$this->id = rand(-999, -1);
		
		// Fake type
		$types = array('text', 'image', 'quote', 'link');
		$this->type = $types[rand(0,count($types)-1)];
		
		// Title
		$headlines = array(
			'Grandmother of eight makes hole in one',
			'Deaf mute gets new hearing in killing',
			'Police begin campaign to run down jaywalkers',
			'House passes gas tax onto senate',
			'Stiff opposition expected to casketless funeral plan',
			'Two convicts evade noose, jury hung',
			'William Kelly was fed secretary',
			'Milk drinkers are turning to powder',
			'Safety experts say school bus passengers should be belted',
			'Quarter of a million Chinese live on water',
			'Farmer bill dies in house',
			'Iraqi head seeks arms',
			'Eye drops off shelf',
			'Squad helps dog bite victim',
			'Dealers will hear car talk at noon',
			'Enraged cow injures farmer with ax',
			'Lawmen from Mexico barbecue guests',
			'Miners refuse to work after death',
			'Two Soviet ships collide - one dies',
			'Two sisters reunite after eighteen years at checkout counter',
			'Never withhold herpes from loved one',
			'Nicaragua sets goal to wipe out literacy',
			'Drunk drivers paid $1,000 in 1984',
			'Autos killing 110 a day, let\'s resolve to do better',
			'If strike isn\'t settled quickly it may last a while',
			'War dims hope for peace',
			'Smokers are productive, but death cuts efficiency',
			'Cold wave linked to temperatures',
			'Child\'s death ruins couple\'s holiday',
			'Blind woman gets new kidney from dad she hasn\'t seen in years',
			'Man is fatally slain',
			'Something went wrong in jet crash, experts say',
			'Death causes loneliness, feeling of isolation');
		$this->title = $headlines[rand(0,count($headlines)-1)];
		
		// Generate the slug
		$slug = $this->title;
		$slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $slug);
		$this->slug = strtolower(str_replace(' ', '-', $slug));
		
		// Generate an author
		$authors = array('Margaret Thatcher','Thomas Edison','Mother Teresa','Helen Keller','Madonna','Jacqueline Kennedy Onasis','Tom Brokaw','James Taylor','Mr. Rogers','Isaac Newton','Lewis Carrol','Andy Rooney','General Norman Schwarzkopf','Norman Rockwell','Pablo Piccaso','Paul McCartney','Plato','Edgar Allen Poe','Elvis','Mae West','Ernest Hemingway','Vincent Van Gogh','W.C.Fields','Robin Williams','Walt Disney','Walter Cronkite','Shakespeare','Frank Lloyd Wright','Julia Roberts','John F. Kennedy, Jr.','Terry Bradshaw','Gloria Steinem','Charles Dickens','Thomas Edison','Whoopi Goldberg','Sigourney Weaver','Bill Clinton','Dave Letterman','Newt Gingrich','Jim Carrey','Mary Tyler Moore','Danny Glover','Carol Burnett','Paul Harvey','Alicia Silverstone','Neil Diamond','Julia Child','George Carlin','Valerie Harper','John Candy','Weird Al Yankovick','Marilyn Vos Savant','Tom Hanks','C. G. Jung','William James','Henri Mancini','Bob Newhart','Meryl Streep','Benny Goodman','Harrison Ford','Steve Martin','Ronald Regan','Dan Aykroyd','Susan B. Anthony','Arthur Ashe','Augustus Caesar','Jane Austen','William F. Buckley, Jr.','Chevy Chase','Phil Donahue','Peter Jennings','Charles Everett Koop','C. S. Lewis','Roy Rogers','Chuck Yeager','Jack Nicholson','Charlie Brown','Oprah Winfrey','Paul Newman','Pelé','Fred Astaire','Eddie Murphy','Jimmy Conners','Michael J. Fox','Ross Perot','Sean Connery','Elizabeth Dole','Dick Van Dyke','Andy Griffith','Peyton Manning','Nathaniel Hawthorne','Shirley MacLaine','Michael Landon','John Katz','Billy Crystal','Carrie Fisher','Darth Vader','Bill Cosby','Bill Gates','Bob Dylan','Carl Sagan','Charles Yeager','Colin L. Powell','Diana, Princess of Wales','Henry A. Kissinger','Elvis Presley','Madonna','Mahatma Gandhi','Michael J. Jordan','Michele Pfeiffer','Doris Day','Liberace','Elizabeth Taylor','Yogi Berra','Dan Rather','Michael Jackson','John Travolta','Tom Cruise','Spider Man','James Dean','Clint Eastwood','Ray Charles','Jesse Jackson','Thomas Jefferson','Hank Aaron','Mohammad Ali','Aristotle','Neil Armstrong','Lucille Ball','Hank Aaron','Beethoven','Alexander Graham Bell','Napoleon','George Washington','Cleopatra','Columbus','Dr. Seuss','Albert Einstein','Eisenhower','F Lee Bailey','Ben Franklin','Sigmund Freud','Gandhi','Alfred Hitchcock','Bob Hope','Harry Houdini','Martin Luther King','John Lennon','Leonardo Da Vinci','Lewis And Clark','Abraham Lincoln','Louis Pasteur','Marilyn Monroe','Mark Twain','Willey Mays','Michelangelo','Miles Davis','Mozart');
		$this->author = $authors[rand(0,count($authors)-1)];
		
		// Generate fake text
		$this->text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam tellus nunc, sollicitudin eu lobortis sit amet, molestie in dui. Aliquam vitae urna eu nisi convallis tempor et in justo. Vivamus viverra lacinia ligula, in rutrum mauris cursus at. Maecenas congue, mi quis luctus dictum, leo diam venenatis mauris, nec placerat nisl ligula id magna. Aliquam odio diam, tempus sit amet viverra vitae, tincidunt et orci. Etiam sit amet tortor in nibh posuere pulvinar. Quisque non mauris id neque blandit euismod vitae eget sem. Donec id interdum enim. Sed bibendum enim sed massa aliquam auctor. Praesent aliquam mi lacinia enim bibendum vel dictum est mollis. Vivamus sodales ipsum non diam sollicitudin eget dictum eros ullamcorper. Aenean vitae ligula ligula. Cras a lectus eget est blandit lobortis. Nullam sed massa eros. Nunc pellentesque enim at velit interdum eu consequat lacus facilisis. Quisque gravida imperdiet nisl, eget pellentesque orci accumsan ac. Curabitur nec mi erat, id condimentum nisi. Integer ut nibh velit. Integer metus lacus, placerat ut interdum sagittis, accumsan ut dui. Nam venenatis placerat diam ac blandit.';
		
		// Give date of now
		$this->date = time();
	}
		
}

?>