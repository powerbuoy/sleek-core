<?php
namespace Sleek\Core;

add_action('after_setup_theme', function () {
	###############
	# Theme support
	add_theme_support('html5');
	add_theme_support('title-tag');
	add_theme_support('custom-logo');
	add_theme_support('post-thumbnails');

	##############
	# Editor style
	add_editor_style();

	###################
	# Disable Gutenberg
	if (get_theme_support('sleek-classic-editor')) {
		add_filter('use_block_editor_for_post_type', '__return_false', 10);
	}

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

	###########################################
	# Adds support post_type arg in get_terms()
	# https://www.dfactory.eu/get_terms-post-type/
	if (get_theme_support('sleek-get-terms-post-type-arg')) {
		add_filter('terms_clauses', function ($clauses, $taxonomy, $args) {
			if (!empty($args['post_type']))	{
				global $wpdb;

				$post_types = [];

				if (isset($args['post_type']) and is_array($args['post_type'])) {
					foreach ($args['post_type'] as $cpt)	{
						$post_types[] = "'" . $cpt . "'";
					}
				}

				if (!empty($post_types))	{
					$clauses['fields'] = 'DISTINCT ' . str_replace('tt.*', 'tt.term_taxonomy_id, tt.term_id, tt.taxonomy, tt.description, tt.parent', $clauses['fields']) . ', COUNT(t.term_id) AS count';
					$clauses['join'] .= ' INNER JOIN ' . $wpdb->term_relationships . ' AS r ON r.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN ' . $wpdb->posts . ' AS p ON p.ID = r.object_id';
					$clauses['where'] .= ' AND p.post_type IN (' . implode(',', $post_types) . ')';
					$clauses['orderby'] = 'GROUP BY t.term_id ' . $clauses['orderby'];
				}
			}

			return $clauses;
		}, 10, 3);
	}
});

######################
# Charset and viewport
add_action('wp_head', function () {
	?>
	<meta charset="<?php echo get_bloginfo('charset') ?>">
	<meta name="viewport" content="<?php echo apply_filters('sleek_meta_viewport', 'width=device-width, initial-scale=1.0') ?>">
	<?php
}, 0);

add_action('wp_footer', function () {
	?>
	<script>
		SLEEK_STYLESHEET_DIRECTORY_URI = "<?php echo get_stylesheet_directory_uri() ?>";
		SLEEK_AJAX_URL = "<?php echo admin_url('admin-ajax.php') ?>";
	</script>
	<?php
}, 0);

###############
# Import CSS/JS
add_action('wp_enqueue_scripts', function () {
	# Import jQuery from CDN
	if (!get_theme_support('sleek-disable-jquery') and get_theme_support('sleek-jquery-cdn')) {
		wp_deregister_script('jquery');
		wp_register_script(
			'jquery',
			'//code.jquery.com/jquery-' . apply_filters('sleek_jquery_version', '3.4.1') . '.min.js',
			[], null, false
		);
		wp_enqueue_script('jquery');
	}

	# Remove jQuery entirely
	if (get_theme_support('sleek-disable-jquery')) {
		wp_dequeue_script('jquery');
		wp_deregister_script('jquery');
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
			(get_theme_support('sleek-disable-jquery') ? [] : ['jquery']),
			filemtime(get_stylesheet_directory() . '/dist/app.js'),
			true
		);
	}
}, 99);

##################################
# Show the editor on the blog page
# NOTE: With gutenberg enabled this will still show the classic editor...
# TODO: Maybe should check sleek-classic-editor theme support?
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

#############################################
# Add a no-js class to body and remove onload
add_filter('body_class', function ($classes) {
	$classes[] = 'no-js';

	return $classes;
});

add_action('wp_head', function () {
	echo "<script>document.documentElement.classList.replace('no-js', 'js');</script>";
});

##############################
# 404 some pages or post-types
# add_filter('sleek_404s', function () {return is_term('some-secret-term')});
# TODO: Deprecate(?)
add_filter('template_redirect', function () {
	global $wp_query;

	if (apply_filters('sleek_404s', false)) {
		status_header(404); # Sets 404 header
		$wp_query->set_404(); # Shows 404 template
	}
});
