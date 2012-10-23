<?php
/*
Plugin Name: Grid Shortcodes
Plugin URI: http://evanmattson.pagelines.me/plugins/grid-shortcodes
Demo: http://evanmattson.pagelines.me/plugins/grid-shortcodes
Description: Adds a collection of shortcodes for easy implementation of the responsive Bootstrap Grid!
Version: 1.0
Author: Evan Mattson
Author URI: http://evanmattson.pagelines.me
Pagelines: true
*/

class GridShortcodes {

	function __construct() {

		self::add_shortcodes();

		add_filter( 'the_content', array(&$this, 'do_grid_shortcodes'), 7 );

	}


	function do_grid_shortcodes( $content ) {

		global $shortcode_tags;

		// backup
		$_shortcode_tags = $shortcode_tags;

		// clear
		remove_all_shortcodes();

		// add
		self::add_shortcodes();

		// do
		$content = do_shortcode($content);

		// restore
		$shortcode_tags = $_shortcode_tags;

		return $content;

	}

	private function add_shortcodes() {

		$tags = array(
			'row',
			'span1',
			'span2',
			'span3',
			'span4',
			'span5',
			'span6',
			'span7',
			'span8',
			'span9',
			'span10',
			'span11',
			'span12'
		);

		foreach ( $tags as $tag )
			add_shortcode( $tag, array(&$this, 'grid_shortcodes') );

	}

	/**
	 * Callback for all grid shortcodes
	 * @param  array 	$atts
	 * @param  string 	$content
	 * @param  string 	$tag
	 * @return string 	markup
	 */
	function grid_shortcodes( $atts, $content, $tag ) {

		extract( shortcode_atts(self::default_atts(), $atts) );

		$content = trim($content);

		return sprintf('<div%s class="%s%s">%s</div>',
			$id ? " id=\"$id\"" : '',
			$tag,
			$class ? " $class" : '',
			do_shortcode($content)
			);

	}

	/**
	 * Returns array of default attributes for grid shortcodes
	 * @return array defaults
	 */
	function default_atts() {

		return array(
			'id'    => '',
			'class' => ''
			);

	}


} // END OF CLASS

new GridShortcodes;