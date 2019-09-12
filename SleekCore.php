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
