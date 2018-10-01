<?php

/**
 * Determines user's timezone and provides interface for matching conditions
 */
class Localizer {

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
	private $cookie;

	/**
	 * __construct
	 *
	 * @param string $action 'echo', 'include' or 'redirect'
	 */
	public function __construct($atts, $action) {

		// Let people override cookie name
		global $localized_content_cookie;
		$this->cookie = apply_filters('localized_content_cookie', $localized_content_cookie);

		// Shortcode atts & action from shortcode registration
		$this->atts   = $atts;
		$this->action = $action;

		// Get user's timezone from shortcode or cookie
		if ($atts['timezone'])
			$this->timezone = $atts['timezone'];
		elseif (!empty($_COOKIE[$this->cookie]))
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
				// Redirects are actually processed during the `wp` hook
				// so don't need to return any content and can exit early
				header('Location: ' . $this->content, true, 307);
				die;
		}
	}

}
