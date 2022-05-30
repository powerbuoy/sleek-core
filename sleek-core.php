<?php
namespace Sleek\Core;

require_once __DIR__ . '/enqueue-scripts.php';
require_once __DIR__ . '/get-terms-post-type-arg.php';

add_action('after_setup_theme', function () {
	###############
	# Theme support
	add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script']);
	add_theme_support('title-tag');
	add_theme_support('custom-logo');
	add_theme_support('post-thumbnails');

	##############
	# Editor style
	add_editor_style();

	######################
	# Disable theme editor
	if (get_theme_support('sleek/disable_theme_editor') and !defined('DISALLOW_FILE_EDIT')) {
		define('DISALLOW_FILE_EDIT', true);
	}

	###################
	# Disable Gutenberg
	if (get_theme_support('sleek/classic_editor')) {
		add_filter('use_block_editor_for_post_type', '__return_false', 10);

		# Also disable block editor for widgets
		remove_theme_support('widgets-block-editor');

		# And remove its CSS (unless in admin)
		if (!is_admin()) {
			add_action('wp_enqueue_scripts', function () {
				wp_dequeue_style('wp-block-library');
			});
		}
	}

	#####################
	# Change email sender
	if (get_theme_support('sleek/nice_email_from')) {
		add_filter('wp_mail_from', function () {
			return get_option('admin_email');
		});

		add_filter('wp_mail_from_name', function () {
			return get_bloginfo('name');
		});
	}

	##########################
	# Disable 404 URL guessing
	# https://core.trac.wordpress.org/ticket/16557
	if (get_theme_support('sleek/disable_404_guessing')) {
		add_filter('redirect_canonical', function ($url) {
			if (is_404() and !isset($_GET['p'])) {
				return false;
			}

			return $url;
		});
	}
});

######################
# Charset and viewport
add_action('wp_head', function () {
	?>
	<meta charset="<?php echo get_bloginfo('charset') ?>">
	<meta name="viewport" content="<?php echo apply_filters('sleek/meta_viewport', 'width=device-width, initial-scale=1.0') ?>">
	<?php
}, 0); # NOTE: Render first thing in <head>

#####################
# Give pages excerpts
add_post_type_support('page', 'excerpt');

################
# Modify excerpt
add_filter('excerpt_length', function () {
	return 25;
});

add_filter('excerpt_more', function () {
	return ' /../';
});

#############################################
# Add a no-js class to body and remove onload
add_filter('body_class', function ($classes) {
	$classes[] = 'no-js';

	return $classes;
});

add_action('wp_head', function () {
	echo "<script>document.documentElement.classList.replace('no-js', 'js');</script>";
}, 0);
