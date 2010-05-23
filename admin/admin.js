$(document).ready(function() {
	
	$.expr[':'].icontains = function(obj, index, meta, stack) {
		return (obj.textContent || obj.innerText || jQuery(obj).text() || '').toLowerCase().indexOf(meta[3].toLowerCase()) >= 0;
	};
	
	/* "More" in navigation */
	$("header nav ul.quicklinks li a#more-link").click(function() {
		$(this).parent().hide();
		$("header nav ul.quicklinks li.more").css("display", "inline-block");
	});
	
	/* Post management live filter */
	$("#searchbar").keyup(function() {
		value = $(this).val();
		$(".post-listing tbody tr").show();
		$(".post-listing tbody tr:not(:icontains(" + value + "))").hide();
	});
	
	// Set up handling for new post type picking
	$("ul#new-post-type li a").click(function(event) {
		// Deactivate other buttons and make the clicked one active		
		$("ul#new-post-type li").each(function() {
			$(this).removeClass("tab-selected");
		});
		$(this).parent().addClass("tab-selected");
		
		// Show/hide proper form elements depending on type
		type = $(this).html();
		$("form#new-post-form #post-type").attr("value", type);
		if (type == 'Text') {
			$("form#new-post-form #post-title").show(); 
			$("form#new-post-form #post-link").hide();
			$("form#new-post-form #post-image").hide();
			$("form#new-post-form #post-text").show();
		} else if (type == 'Quote') {
			$("form#new-post-form #post-title").show(); 
			$("form#new-post-form #post-link").hide();
			$("form#new-post-form #post-image").hide();
			$("form#new-post-form #post-text").show();
		} else if (type == 'Link') {
			$("form#new-post-form #post-title").show(); 
			$("form#new-post-form #post-link").show();
			$("form#new-post-form #post-image").hide();
			$("form#new-post-form #post-text").show();
		} else if (type == 'Image') {
			$("form#new-post-form #post-title").show(); 
			$("form#new-post-form #post-link").hide();
			$("form#new-post-form #post-image").show();
			$("form#new-post-form #post-text").show();
		} else if (type == 'Page') {
			$("form#new-post-form #post-title").show(); 
			$("form#new-post-form #post-link").hide();
			$("form#new-post-form #post-image").hide();
			$("form#new-post-form #post-text").show();
		}
	});
	
	
	$("#post-text textarea").bind("keyup", function() {
		converter = new Showdown.converter();
		text = converter.makeHtml($("#post-text textarea").val());
		$("#markdownpreview").html(text);
	});
	
	$("#linky-text").trigger("click");
});