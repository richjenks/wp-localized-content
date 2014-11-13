<?php

/**
 * LocalizedContent
 *
 * Determines user's timezone and provides interface for matching conditions
 */

namespace RichJenks\LocalizedContent;

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
	 * __construct
	 *
	 * @param string $action 'echo', 'include' or 'redirect'
	 */

	public function __construct( $atts, $action ) {

		$this->atts   = $atts;
		$this->action = $action;

		// Get user's timezone from cookie or API
		$this->timezone = ( isset( $_SESSION['timezone'] ) ) ? $_SESSION['timezone'] : $this->get_timezone();

		// If timezone found, get matching attribute value
		if ( $this->timezone )
			$this->content = $this->choose_content( $atts );

		// Ensure `default` is lowercase
		if ( isset( $atts['Default'] ) ) {
			$atts['default'] = $atts['Default'];
			unset( $atts['Default'] );
		}

		// If no match but default exists, do it
		if ( !$this->content && isset( $atts['default'] ) )
			$this->content = $atts['default'];

	}

	/**
	 * get_timezone
	 *
	 * Determines the user's timezone using API
	 *
	 * @return string User's timezone, slashes swapped for underscores
	 */

	private function get_timezone() {

		$data = json_decode( file_get_contents( 'http://ip-api.com/json/' . $_SERVER['REMOTE_ADDR'] ) );

		if ( $data->status === 'success' ) {

			// API call was succesful so store & return
			$_SESSION['timezone'] = $data->timezone;
			return $data->timezone;

		} else {
			return false;
		}

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
				$sql = 'SELECT post_content FROM `' . $GLOBALS['wpdb']->posts . '` WHERE ID = "' . $this->content . '" OR post_name = "' . $this->content . '" LIMIT 1';
				$content = $GLOBALS['wpdb']->get_var( $sql );
				return do_shortcode($content);

			case 'redirect':
				if ( strlen( $this->content ) !== 0 ) return '<script>window.location = "' . $this->content . '";</script>';

		}
	}

	/**
	 * is_flag
	 *
	 * Determines whether a flag is enabled
	 * A flag being an attr without a val
	 *
	 * @param string $flag Name of flag
	 * @param array $atts Shortcode attributes
	 *
	 * @return bool True if flag is given, false if not
	 */

	public function is_flag( $flag, $atts ) {
		$is = false;
		foreach ( $atts as $key => $value ) {
			if ( is_int( $key ) && $value === $flag ) {
				$is = true;
			}
		}
		return $is;
	}

	/**
	 * debug
	 *
	 * Outputs debug infomation
	 */

	public function debug() {
		echo '<pre style="color: #111; background: #ddd;">';
		echo '<b>Attributes</b><br>';
		var_dump( $this->atts );
		echo '<b>Action</b><br>';
		var_dump( $this->action );
		echo '<b>Timezone</b><br>';
		var_dump( $this->timezone );
		echo '<b>Content</b><br>';
		var_dump( $this->content );
		echo '<b>Output</b><br>';
		var_dump( $this->get_content() );
		echo '</pre>';
	}

}