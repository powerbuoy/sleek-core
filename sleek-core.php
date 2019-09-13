<?php
namespace Sleek\Core;

######################
# Charset and viewport
add_action('wp_head', function () {
	echo '<meta charset="' . get_bloginfo('charset') . '">';
	echo '<meta name="viewport" content="' . apply_filters('sleek_meta_viewport', 'width=device-width, initial-scale=1.0') . '">';
}, 0);

###############
# Theme support
add_theme_support('html5');
add_theme_support('title-tag');
add_theme_support('custom-logo');
add_theme_support('post-thumbnails');
add_theme_support('automatic-feed-links');

###################
# Disable Gutenberg
add_filter('use_block_editor_for_post_type', '__return_false', 10);

##################################
# Show the editor on the blog page
# NOTE: With gutenberg enabled this will still show the classic editor...
# https://wordpress.stackexchange.com/questions/193755/show-default-editor-on-blog-page-administration-panel
add_action('edit_form_after_title', function ($post) {
	if ($post->ID === get_option('page_for_posts')) {
		add_post_type_support('page', 'editor');
	}
}, 0);

#####################
# Give pages excerpts
add_action('init', function () {
	add_post_type_support('page', 'excerpt');
});

################
# Modify excerpt
add_filter('excerpt_length', function () {
	return 25;
});

add_filter('excerpt_more', function () {
	return ' /../';
});

##############
# Editor style
add_editor_style();

#####################
# Change email sender
add_filter('wp_mail_from', function () {
	return get_option('admin_email');
});

add_filter('wp_mail_from_name', function () {
	return get_bloginfo('name');
});

################################################
# Remove "Protected:" from protected post titles
add_filter('private_title_format', function () {
	return '%s';
});

add_filter('protected_title_format', function () {
	return '%s';
});

#############################################
# Add a no-js class to body and remove onload
add_filter('body_class', function ($classes) {
	$classes[] = 'no-js';

	return $classes;
});

add_action('wp_head', function () {
	echo "<script>document.documentElement.classList.replace('no-js', 'js');</script>";
});

##############################################################
# Fix pagination output (Remove h2, wrapping div, classes etc)
add_filter('navigation_markup_template', function ($template, $class) {
	return '<nav id="pagination">%3$s</nav>';
}, 10, 2);

#####################################
# Prevent WP wrapping iframe's in <p>
# https://gist.github.com/KTPH/7901c0d2c66dc2d754ce
# add_filter('the_content', function ($content) {
#	return preg_replace('/<p>\s*(<iframe .*>*.<\/iframe>)\s*<\/p>/iU', '\1', $content);
# });

########################
# Clean up widget output
# NOTE: Not yet in WP-Core
# https://core.trac.wordpress.org/ticket/48033
add_filter('register_sidebar_defaults', function ($defaults) {
	$defaults['before_widget'] = '<div id="widget-%1$s" class="%2$s">';
	$defaults['after_widget'] = '</div>';
	$defaults['before_title'] = '<h2>';
	$defaults['after_title'] = '</h2>';

	return $defaults;
});

#############################
# Clean up wp_list_categories
add_action('wp_list_categories', function ($output) {
	# Remove title attributes (which can be insanely long)
	# https://www.isitwp.com/remove-title-attribute-from-wp_list_categories/
	$output = preg_replace('/ title="(.*?)"/s', '', $output);

	# If there's no current cat - add the class to the "all" link
	if (strpos($output, 'current-cat') === false) {
		$output = str_replace('cat-item-all', 'cat-item-all current-cat', $output);
	}

	# If there are no categories, don't display anything
	if (strpos($output, 'cat-item-none') !== false) {
		$output = false;
	}

	return $output;
});

##########################
# Disable 404 URL guessing
# https://core.trac.wordpress.org/ticket/16557
add_filter('redirect_canonical', function ($url) {
	if (is_404() and !isset($_GET['p'])) {
		return false;
	}

	return $url;
});

##############################
# 404 some pages or post-types
add_filter('template_redirect', function () {
	global $wp_query;

	if (apply_filters('sleek_404s', false)) {
		status_header(404); # Sets 404 header
		$wp_query->set_404(); # Shows 404 template
	}
});
