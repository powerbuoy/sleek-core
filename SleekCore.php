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
add_filter('wp_mail_from', 'sleek_email_from');

function sleek_email_from () {
	return get_option('admin_email');
}

add_filter('wp_mail_from_name', 'sleek_email_from_name');

function sleek_email_from_name () {
	return get_bloginfo('name');
}

######################
# 404 attachment pages
add_filter('template_redirect', 'sleek_404_attachments');

function sleek_404_attachments () {
	global $wp_query;

	if (is_attachment()) {
		status_header(404); # Sets 404 header
		$wp_query->set_404(); # Shows 404 template
	}
}
