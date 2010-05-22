<html>
	<head>
		<title><?php echo $tags->nf_page_title; ?></title>
		<link rel="stylesheet" href="themes/theme.default/styles.css" type="text/css" />
		<?php echo $tags->nf_feed_link; ?>
		<?php echo $tags->nf_headscape; ?>
	</head>
	<body>
		<?php echo $tags->nf_administration_bar; ?>
		<div class="container">			
			<header>
				<h2><?php echo $tags->nf_blog_subtitle; ?></h2>
				<h1><a href="<?php echo $tags->nf_siteroot; ?>">Welcome to <?php echo $tags->nf_blog_title; ?></a></h1>
			</header>
			<div id="posts">
				<?php echo $tags->nf_posts; ?>
			</div>
			<div id="sidebar">
				<h3>Search</h3>
				<?php echo $tags->nf_search_bar; ?>
				<h3>Pages</h3>
				<?php echo $tags->nf_pages_list; ?>
				<h3>Categories</h3>
				<?php echo $tags->nf_category_list; ?>
				<h3>Tags</h3>
				<?php echo $tags->nf_tag_list; ?>
				<h3>Archives</h3>
				<?php echo $tags->nf_archive_list; ?>
			</div>
			<div id="footer">
				Thank you for choosing <a href="http://codeprinciples.com/newsflash/">Newsflash</a> for your blog! &#9733;
			</div>
		</div>
		<?php echo $tags->nf_end; ?>
	</body>
</html>