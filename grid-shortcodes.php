<?php
/*
	Plugin Name: Grid Shortcodes
	Version: 1.2.5
	Description: Adds a collection of shortcodes for easy implementation of the responsive Bootstrap Grid!
	Author: Evan Mattson
	Author URI: http://pagelines.aaemnnost.tv
	Plugin URI: http://pagelines.aaemnnost.tv/plugins/grid-shortcodes
	Demo: http://pagelines.aaemnnost.tv/plugins/grid-shortcodes/demo
	PageLines: true
	V3: true
*/

class GridShortcodes
{
	/**
	 * HTML for inserting at the beginning of each spanX
	 * as a fix for wpautop not properly wrapping the first paragraph
	 * @var string
	 */
	static $autopfix = '<p style="display:none;"><!-- autopfix --></p>';

	static $default_atts = array(
		'id'    => '',
		'class' => '',
	);

	function __construct()
	{
		$this->add_shortcodes();

		add_filter( 'the_content', array(&$this, 'do_grid_shortcodes'), 7 );
		add_filter( 'the_content', array(&$this, 'cleanup'), 999 );
	}

	/**
	 * Process all grid shortcodes
	 * 'the_content' filter callback
	 * Only renders grid markup.
	 */
	function do_grid_shortcodes( $content )
	{
		global $shortcode_tags;

		// backup
		$_shortcode_tags = $shortcode_tags;

		// clear
		remove_all_shortcodes();

		// add
		$this->add_shortcodes();

		// do
		$content = do_shortcode( $content );

		// restore
		$shortcode_tags = $_shortcode_tags;

		return $content;
	}

	/**
	 * Remove our autopfix html from output as it is no longer needed
	 */
	function cleanup( $content )
	{
		return str_replace( self::$autopfix, '', $content );
	}

	private function add_shortcodes()
	{
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

		// now we're going to add a LOT of shortcodes (13*(26+1))... 351
		foreach ( $tags as $tag ) {
			add_shortcode( $tag, array(&$this, 'grid_shortcodes') );
			foreach ( $this->get_alphabet_array() as $x )
				add_shortcode( "{$tag}_$x", array(&$this, 'grid_shortcodes') );
		}
	}

	/**
	 * Master callback for all grid shortcodes
	 */
	function grid_shortcodes( $atts, $content, $tag )
	{
		$atts = is_array( $atts ) ? array_map('esc_attr', $atts) : $atts;
		extract( shortcode_atts( self::$default_atts, $atts) );

		$grid_class = $this->get_grid_class( $tag );
		$content = trim( $content );

		// grid css targets spanX with > selector
		if ( 'row' != $grid_class )
			$content = $this->maybe_wrap_content( $atts, $content, $tag );

		$classes = $class ? "$grid_class $class" : $grid_class;
		$inner = do_shortcode( $content );

		// build grid
		$grid = "<div class=\"$classes\"";

		if ( $id )
			$grid .= " id=\"$id\"";

		$grid .= ">$inner</div>";

		return $grid;
	}

	function get_grid_class( $tag )
	{
		if ( false !== strpos($tag, '_') )
		{
			$_tag = explode('_', $tag);
			return $_tag[0];
		}
		else
			return $tag;
	}

	function maybe_wrap_content( $atts, $content, $tag )
	{
		// prepending here so it is inside the pad if it will be wrapped
		$content = self::$autopfix . $content; // wpautop fix

		if ( $this->to_wrap_or_not_to_wrap( $atts ) ) {

			// if the pad class is set use it, otherwise give it a default
			// pad="" will give the wrapping div an empty class
			return sprintf('<div class="%s">%s</div>',
				isset( $atts['pad'] )
					? $atts['pad']
					: sprintf('span-pad %s-pad', $this->get_grid_class( $tag ) ),
				$content
			);
		}
		else
			return $content;
	}

	function to_wrap_or_not_to_wrap( $atts )
	{
		if ( ! is_array($atts) )
			return false;

		if ( isset( $atts['pad'] ) )
			return true;

		// check to see if it was used without an attribute: value-only
		foreach ( $atts as $key => $value )
			if ( is_int( $key ) && 'pad' == $value )
				return true;

		return false;
	}

	function get_alphabet_array()
	{
		$alpha = 'a-b-c-d-e-f-g-h-i-j-k-l-m-n-o-p-q-r-s-t-u-v-w-x-y-z';
		return explode('-', $alpha);
	}

} // GridShortcodes

new GridShortcodes;