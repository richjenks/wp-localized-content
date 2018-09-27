<?php

/**
 * Plugin Name: Localized Content
 * Plugin URI: https://github.com/richjenks/wp-localized-content
 * Description: Show different content or redirect to another URL based on the user's timezone
 * Version: 1.4.0
 * Author: Rich Jenks <rich@richjenks.com>
 * Author URI: http://richjenks.com
 * License: GPL2
 */

// Register shortcode for each action
$shortcodes = ['text', 'include', 'redirect'];
foreach ($shortcodes as $shortcode) {
	add_shortcode('localized-' . $shortcode, function ($atts) use ($shortcode) {
		$region = new LocalizedContent($atts, $shortcode);
		return $region->get_content();
	} );
}

// Script to determine timezone
// May trigger a full page reload and `wp_enqueue_script()` runs too late
add_action('wp_head', function () {
	global $wp_version;
	$src = plugin_dir_url(__FILE__) . 'timezone.js';
	$tag = sprintf('<script src=%s?ver=%s></script>', $src, $wp_version);
	echo $tag . PHP_EOL;
}, 0);

/**
 * LocalizedContent
 *
 * Determines user's timezone and provides interface for matching conditions
 */
class LocalizedContent {

	/**
	 * @var array Shortcode attributes
	 */
	private $atts;

	/**
	 * @var string 'echo', 'include' or 'redirect'
	 */
	private $action;

	/**
	 * @var string User's timezone, slashes swapped for underscores
	 */
	private $timezone = false;

	/**
	 * @var string Content of attr option to be returned
	 */
	private $content = false;

	/**
	 * @var string Name of the cookie
	 */
	private $cookie = 'STYXKEY_timezone';

	/**
	 * __construct
	 *
	 * @param string $action 'echo', 'include' or 'redirect'
	 */
	public function __construct($atts, $action) {

		$this->atts   = $atts;
		$this->action = $action;

		// Get user's timezone from shortcode or cookie
		if ($atts['timezone'])
			$this->timezone = $atts['timezone'];
		elseif (isset($_COOKIE[$this->cookie]))
			$this->timezone = $_COOKIE[$this->cookie];

		// If timezone found, get matching attribute value
		if ($this->timezone)
			$this->content = $this->choose_content($atts);

		// Ensure `default` is lowercase
		if (isset( $atts['Default'])) {
			$atts['default'] = $atts['Default'];
			unset($atts['Default']);
		}

		// If no match but default exists, do it
		if (!$this->content && isset( $atts['default']))
			$this->content = $atts['default'];

		// If all fails, do nothing...

	}

	/**
	 * choose_content
	 *
	 * Chooses the correct piece of content to be returned
	 *
	 * @param  array  $atts Shortcode attributes
	 * @return string Value of the shortcode att matching user's timezone
	 */
	private function choose_content($atts) {

		// Sanitize user's timezone
		$current = strtolower(str_replace('/', '_', $this->timezone));

		// Check each option to see if it matches user's timezone
		foreach ($atts as $option => $content) {
			$length = strlen($option);
			if (substr($current, 0, $length) === $option) {
				$result = $content;
				break; // Stop on first match
			}
		}

		return (isset($result)) ? $result : false;

	}

	/**
	 * get_content
	 *
	 * Returns the right content in the right format
	 *
	 * @return string Content to be shown
	 */
	public function get_content() {
		switch ($this->action) {
			case 'text':
				return $this->content;
			case 'include':
				$post = get_post($this->content);
				return do_shortcode($post->post_content);
			case 'redirect':
				return '<script>window.location = "' . $this->content . '";</script>';
		}
	}

}