<?php
namespace Sleek\Core;

###############
# Import CSS/JS
add_action('wp_enqueue_scripts', function () {
	# Import jQuery from CDN
	if (!get_theme_support('sleek/disable_jquery') and get_theme_support('sleek/jquery_cdn')) {
		wp_deregister_script('jquery');
		wp_register_script(
			'jquery',
			'//code.jquery.com/jquery-' . apply_filters('sleek/jquery_version', '3.4.1') . '.min.js',
			[], null, false
		);
		wp_enqueue_script('jquery');
	}

	# Remove jQuery entirely (unless user is logged in (to support for example Query Monitor))
	if (get_theme_support('sleek/disable_jquery') and !is_user_logged_in()) {
		wp_dequeue_script('jquery');
		# wp_deregister_script('jquery');
	}

	# Import CSS
	if (file_exists(get_stylesheet_directory() . '/dist/app.css')) {
		wp_enqueue_style(
			'sleek',
			get_stylesheet_directory_uri() . '/dist/app.css',
			[],
			filemtime(get_stylesheet_directory() . '/dist/app.css')
		);
	}

	# Import JS
	if (file_exists(get_stylesheet_directory() . '/dist/app.js')) {
		wp_enqueue_script(
			'sleek',
			get_stylesheet_directory_uri() . '/dist/app.js',
			(get_theme_support('sleek/disable_jquery') ? [] : ['jquery']),
			filemtime(get_stylesheet_directory() . '/dist/app.js'),
			true
		);
	}
}, 99);
