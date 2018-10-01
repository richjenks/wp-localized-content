<?php

/**
 * Plugin Name: Localized Content
 * Plugin URI: https://github.com/richjenks/wp-localized-content
 * Description: Show different content or redirect to another URL based on the user's timezone
 * Version: 1.5.1
 * Author: Rich Jenks <rich@richjenks.com>
 * Author URI: http://richjenks.com
 * License: GPL2
 */

// Localizer class that does the heavy lifting
require 'Localizer.php';

// Default cookie name
$localized_content_cookie = 'STYXKEY_timezone';

// Register shortcode for each action
$shortcodes = ['text', 'include', 'redirect'];
foreach ($shortcodes as $shortcode) {
	add_shortcode('localized-' . $shortcode, function ($atts) use ($shortcode) {
		$localizer = new Localizer($atts, $shortcode);
		return $localizer->get_content();
	} );
}

// Process localized redirects early
add_action('wp', function () {
	global $post;
	if (has_shortcode($post->post_content, 'localized-redirect')) {
		do_shortcode($post->post_content);
	}
});

// Script to determine timezone
// May trigger a full page reload and `wp_enqueue_script()` runs too late
add_action('wp_head', function () {

	// Globals. Eurgh.
	global $wp_version;
	global $localized_content_cookie;

	// Prepare cookie name
	$var = apply_filters('localized_content_cookie', $localized_content_cookie);
	$var = sprintf('<script>var localized_content_cookie = "%s";</script>', $var);

	// Prepare script tag
	$tag = plugin_dir_url(__FILE__) . 'timezone.js';
	$tag = sprintf('<script src="%s?ver=%s"></script>', $tag, $wp_version);

	// Make it so!
	echo $var . PHP_EOL . $tag . PHP_EOL;
}, 0);