<?php
namespace Sleek\Core;

add_action('after_setup_theme', function () {
	###########################################
	# Adds support post_type arg in get_terms()
	# https://www.dfactory.eu/get_terms-post-type/
	if (get_theme_support('sleek/get_terms_post_type_arg')) {
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
