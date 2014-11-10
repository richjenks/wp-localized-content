<?php

/**
 * LocalizedContent
 *
 * Determines user's timezone and provides interface for matching conditions
 */

namespace RichJenks\LocalizedContent;

class LocalizedContent {

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
	 * __construct
	 *
	 * @param string $action 'echo', 'include' or 'redirect'
	 */

	public function __construct( $atts, $action ) {

		// One of three actions
		$this->action = $action;

		// Get user's timezone
		$this->timezone = $this->get_timezone();

		// If timezone found, get matching attribute value
		if ( $this->timezone )
			$this->content = $this->choose_content( $atts );

		// If no match but default exists, do it
		if ( !$this->content && isset( $atts['default'] ) )
			$this->content = $atts['default'];

	}

	/**
	 * get_timezone
	 *
	 * @return string User's timezone, slashes swapped for underscores
	 */

	private function get_timezone() {
		$data = json_decode( file_get_contents( 'http://ip-api.com/json/' . $_SERVER['REMOTE_ADDR'] ) );
		return ( $data->status === 'success' ) ? $data->timezone : false;
	}

	/**
	 * choose_content
	 *
	 * Chooses the correct piece of content to be returned
	 *
	 * @param  array  $atts Shortcode attributes
	 * @return string Value of the shortcode att matching user's timezone
	 */

	private function choose_content( $atts ) {

		// Sanitize user's timezone
		$current = strtolower( str_replace( '/', '_', $this->timezone ) );

		// Check each option to see if it matches user's timezone
		foreach ( $atts as $option => $content ) {
			$length = strlen( $option );
			if ( substr( $current, 0, $length ) === $option ) {
				$result = $content;
				break; // Stop on first match
			}
		}

		return ( isset( $result ) ) ? $result : false;

	}

	/**
	 * get_content
	 *
	 * Returns the right content in the right format
	 *
	 * @return string Content to be shown
	 */

	public function get_content() {
		switch ( $this->action ) {

			case 'text':
				return $this->content;

			case 'include':
				// -- $sql = "SELECT post_content FROM $_GLOBALS['wpdb']->posts WHERE ID = '$this->content' OR post_name = '$this->content' LIMIT 1";
				$sql = 'SELECT post_content FROM `' . $GLOBALS['wpdb']->posts . '` WHERE ID = "' . $this->content . '" OR post_name = "' . $this->content . '" LIMIT 1';
				$content = $GLOBALS['wpdb']->get_var( $sql );
				return do_shortcode($content);

			case 'redirect':
				if ( strlen( $this->content ) !== 0 ) return '<script>window.location = "' . $this->content . '";</script>';

		}
	}

}