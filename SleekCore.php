<?php
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
// add_action('add_meta_boxes', function ($post_type, $post) {
// 	if (isset($post->ID) and $post->ID === get_option('page_for_posts')) {
// 		remove_action('edit_form_after_title', '_wp_posts_page_notice');
// 		add_post_type_support('page', 'editor');
// 	}
// }, 0, 2);
