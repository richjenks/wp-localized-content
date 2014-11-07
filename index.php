<?php

/**
 * Plugin Name: Regional Content
 * Plugin URI: https://github.com/richjenks/wp-regional-content
 * Description: Show different content or redirect to another location based on user's location
 * Version: 1.0
 * Author: Rich Jenks <rich@richjenks.com>
 * Author URI: http://richjenks.com
 * License: GPL2
 */

namespace RichJenks\RegionalContent;

// TEST!
$_SERVER['REMOTE_ADDR'] = '23.23.23.23';

// Include all plugin files
foreach ( scandir( __DIR__ . '/plugin' ) as $file ) {
	if ( !in_array( $file, array( '.', '..' ) ) ) {
		require __DIR__ . '/plugin/' . $file;
	}
}

// Shortcodes
$shortcodes = array(
	'echo',
	'include',
	'redirect',
);

// Register shortcodes
foreach ( $shortcodes as $shortcode ) {
	\add_shortcode( 'regional-' . $shortcode, function ( $atts ) use ( $shortcode ) {
		$region = new Region( $atts, $shortcode );
		return $region->do_action();
	} );
}