<?php

/**
 * Action
 *
 * Performs the required action
 */

namespace RichJenks\RegionalContent;

class Action {

	/**
	 * @var string Action to be performed
	 */

	private $action;

	/**
	 * @var string Value to be passed to the Action
	 */

	private $value;

	/**
	 * __construct
	 *
	 * Store vars
	 *
	 * @param string $action Action to be performed
	 * @param string $value Value to be passed to the Action
	 */

	public function __construct( $action, $value ) {
		$this->action = $action;
		$this->value = $value;
	}

	/**
	 * do_action
	 *
	 * Route to correct Action
	 */

	public function do_action() {
		return call_user_func( array( $this, 'regional_' . $this->action ) );
	}

	/**
	 * echo
	 *
	 * @return string Value to be echoed
	 */

	private function regional_echo() {
		return $this->value;
	}

	/**
	 * include
	 *
	 * @return string Content of post by ID or slug
	 */

	private function regional_include() {

		global $wpdb;

		// Get included page's content
		$content = $wpdb->get_var("SELECT post_content FROM $wpdb->posts WHERE ID = '$this->value' OR post_name = '$this->value' LIMIT 1");

		// Make recursive
		return do_shortcode($content);

	}

	/**
	 * redirect
	 *
	 * Redirects to the appropriate location
	 */

	private function regional_redirect() {
		if ( $this->value ) return '<script>window.location = "' . $this->value . '";</script>';
	}

}