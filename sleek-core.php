<?php
namespace Sleek\Core;

###############
# Theme support
add_theme_support('html5');
add_theme_support('title-tag');
add_theme_support('custom-logo');
add_theme_support('post-thumbnails');
add_theme_support('automatic-feed-links');

# Ours
add_theme_support('sleek-mobile-viewport');
add_theme_support('sleek-classic-editor');
add_theme_support('sleek-jquery-cdn');
add_theme_support('sleek-disable-404-guessing');
add_theme_support('sleek-nice-email-from');
add_theme_support('sleek-comment-form-placeholders');
add_theme_support('sleek-tinymce-clean-paste');
add_theme_support('sleek-tinymce-no-colors');

# Disabled by default but here for reference
# add_theme_support('sleek-archive-filter');
# add_theme_support('sleek-get-terms-post-type-arg')

######################
# Charset and viewport
add_action('wp_head', function () {
	echo '<meta charset="' . get_bloginfo('charset') . '">';

	if (get_theme_support('sleek-mobile-viewport')) {
		echo '<meta name="viewport" content="' . apply_filters('sleek_meta_viewport', 'width=device-width, initial-scale=1.0') . '">';
	}
}, 0);

###################
# Disable Gutenberg
if (get_theme_support('sleek-classic-editor')) {
	add_filter('use_block_editor_for_post_type', '__return_false', 10);
}

################
# Include CSS/JS
# TODO: Is this action needed?
add_action('wp_enqueue_scripts', function () {
	# Include jQuery from CDN
	if (get_theme_support('sleek-jquery-cdn')) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', '//code.jquery.com/jquery-' . apply_filters('sleek_jquery_version', '3.4.1') . '.min.js', [], null, false);
		wp_enqueue_script('jquery');
	}

	$cssFile = apply_filters('sleek_css_file', 'main.css');
	$jsFile = apply_filters('sleek_js_file', 'main.js');

	# Include CSS
	if (file_exists(get_stylesheet_directory() . '/dist/' . $cssFile)) {
		wp_enqueue_style('sleek', get_stylesheet_directory_uri() . '/dist/' . $cssFile, [], filemtime(get_stylesheet_directory() . '/dist/' . $cssFile));
	}
	# Include JS
	if (file_exists(get_stylesheet_directory() . '/dist/' . $jsFile)) {
		wp_enqueue_script('sleek', get_stylesheet_directory_uri() . '/dist/' . $jsFile, ['jquery'], filemtime(get_stylesheet_directory() . '/dist/' . $jsFile), true);
	}
});

########################
# Set up for translation
load_theme_textdomain('sleek', get_template_directory() . '/languages');

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
# TODO: Is this action needed?
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
if (get_theme_support('sleek-nice-email-from')) {
	add_filter('wp_mail_from', function () {
		return get_option('admin_email');
	});

	add_filter('wp_mail_from_name', function () {
		return get_bloginfo('name');
	});
}

#############################################
# Add a no-js class to body and remove onload
add_filter('body_class', function ($classes) {
	$classes[] = 'no-js';

	return $classes;
});

add_action('wp_head', function () {
	echo "<script>document.documentElement.classList.replace('no-js', 'js');</script>";
});

##########################
# Disable 404 URL guessing
# https://core.trac.wordpress.org/ticket/16557
if (get_theme_support('sleek-disable-404-guessing')) {
	add_filter('redirect_canonical', function ($url) {
		if (is_404() and !isset($_GET['p'])) {
			return false;
		}

		return $url;
	});
}

##############################
# 404 some pages or post-types
# TODO: Use has_single=false instead
add_filter('template_redirect', function () {
	global $wp_query;

	if (apply_filters('sleek_404s', false)) {
		status_header(404); # Sets 404 header
		$wp_query->set_404(); # Shows 404 template
	}
});
