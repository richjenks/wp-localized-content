<?php

/**
 * Region
 *
 * Determines user's timezone and provides interface for matching conditions
 */

namespace RichJenks\RegionalContent;

class Region {

	/**
	 * @var string 'echo', 'include' or 'redirect'
	 */

	private $action;

	/**
	 * @var string User's timezone, slashes swapped for underscores
	 */

	private $timezone;

	/**
	 * @var string Matched result
	 */

	private $result;

	/**
	 * __construct
	 *
	 * @param string $action 'echo', 'include' or 'redirect'
	 */

	public function __construct( $atts, $action ) {

		// One of three actions
		$this->action = $action;

		// Set default, if not specified
		$atts['default'] = ( isset( $atts['default'] ) ) ? strtolower( str_replace( '/', '_', $data['default'] ) ) : 'europe_london';

		// Get user's timezone
		$this->timezone = $this->get_timezone( $atts['default'] );

		// Get matching attribute value
		$this->result = $this->get_result( $atts );

	}

	/**
	 * do_action
	 *
	 * Actually do the flippin' action!
	 * index.php needs to return, so can't do this in constuctor
	 */

	public function do_action() {
		$action = new Action( $this->action, $this->result );
		return $action->do_action();
	}

	/**
	 * get_timezone
	 *
	 * @return string User's timezone, slashes swapped for underscores
	 */

	private function get_timezone( $default ) {
		$data = json_decode( file_get_contents( 'http://ip-api.com/json/' . $_SERVER['REMOTE_ADDR'] ) );
		return ( $data->status !== 'success' ) ? $default : strtolower( str_replace( '/', '_', $data->timezone ) );
	}

	/**
	 * get_result
	 *
	 * @return string Value of the shortcode att matching user's timezone
	 */

	private function get_result( $atts ) {

		// Check if timezone matches atts
		foreach ( $atts as $timezone => $value ) {

			// Sanitize timezone
			$timezone = strtolower( str_replace( '/', '_', $timezone ) );

			// Timezone length
			$length = strlen( $timezone );

			// Check for match
			if ( substr( $this->timezone, 0, $length ) === $timezone ) {
				$result = $value;
			}

		}

		return ( isset( $result ) ) ? $result : false;

	}

}