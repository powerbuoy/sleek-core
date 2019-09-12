<?php
namespace Sleek;

###################
# Title tag support
add_theme_support('title-tag');

################
# Modify excerpt
add_filter('excerpt_length', function () {
	return 25;
});

add_filter('excerpt_more', function () {
	return ' /../';
});

#####################
# Give pages excerpts
add_action('init', function () {
	add_post_type_support('page', 'excerpt');
});

###################
# Disable Gutenberg
add_filter('use_block_editor_for_post_type', '__return_false', 10);

##################################
# Show the editor on the blog page
# https://wordpress.stackexchange.com/questions/193755/show-default-editor-on-blog-page-administration-panel
add_action('edit_form_after_title', function ($post) {
	if ($post->ID === get_option('page_for_posts')) {
		add_post_type_support('page', 'editor');
	}
}, 0);

#####################
# Change email sender
add_filter('wp_mail_from', __NAMESPACE__ . '\\mail_from');

function mail_from () {
	return get_option('admin_email');
}

add_filter('wp_mail_from_name', __NAMESPACE__ . '\\mail_from_name');

function mail_from_name () {
	return get_bloginfo('name');
}

################################################
# Remove "Protected:" from protected post titles
add_filter('private_title_format', function () {
	return '%s';
});

add_filter('protected_title_format', function () {
	return '%s';
});

#####################################
# Prevent WP wrapping iframe's in <p>
# https://gist.github.com/KTPH/7901c0d2c66dc2d754ce
# add_filter('the_content', function ($content) {
#	return preg_replace('/<p>\s*(<iframe .*>*.<\/iframe>)\s*<\/p>/iU', '\1', $content);
# });

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
add_filter('redirect_canonical', __NAMESPACE__ . '\\disable_404_guessing');

function disable_404_guessing ($url) {
	if (is_404() and !isset($_GET['p'])) {
		return false;
	}

	return $url;
}

######################
# 404 attachment pages
add_filter('template_redirect', __NAMESPACE__ . '\\attachments404');

function attachments404 () {
	global $wp_query;

	if (is_attachment()) {
		status_header(404); # Sets 404 header
		$wp_query->set_404(); # Shows 404 template
	}
}
