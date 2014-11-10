<?php

/**
 * Plugin Name: Localized Content
 * Plugin URI: https://bitbucket.org/richjenks/localized-content
 * Description: Show different content or redirect to another location based on user's location
 * Version: 1.0
 * Author: Rich Jenks <rich@richjenks.com>
 * Author URI: http://richjenks.com
 * License: GPL2
 */

require 'LocalizedContent.php';

// Register shortcodes
$shortcodes = array( 'text', 'include', 'redirect' );
foreach ( $shortcodes as $shortcode ) {
	\add_shortcode( 'localized-' . $shortcode, function ( $atts ) use ( $shortcode ) {
		$region = new RichJenks\LocalizedContent\LocalizedContent( $atts, $shortcode );
		return $region->get_content();
	} );
}