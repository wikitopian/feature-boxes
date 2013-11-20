<?php
/*
 * Plugin Name: Feature Boxes
 * Plugin URI:  http://www.github.com/wikitopian/feature-boxes
 * Description: Shortcode-activated boxes
 * Version:     0.1.0
 * Author:      @wikitopian
 * Author URI:  http://www.github.com/wikitopian
 * License:     DWTFYWT
 */

class Feature_Boxes {
	const PADDING = 2;

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( get_class(), 'do_style' ) );

		add_shortcode( 'feature_boxes', array( get_class(), 'do_feature_boxes' ) );
	}
	public static function do_style() {
		wp_enqueue_style(
			get_class(),
			plugins_url( '/style/feature-boxes.css', __FILE__ )
		);
	}
	public static function do_feature_boxes( $atts ) {

		$class = get_class();
		$id    = get_class() . '_' . $atts['category'];

		$boxes = self::get_boxes( $atts );

		$box_count = count( $boxes );
		$box_width = floor( ( 95 - ( self::PADDING * 2 * $box_count ) ) / $box_count );
		$box_style = "width: {$box_width}%;\n";

		return self::get_contents( $class, $id, $box_style, $boxes );
	}
	public static function get_boxes( $atts ) {

		extract(
			shortcode_atts(
				array(
					'title'     => true,
					'count'     => 3,
					'post_type' => 'post',
					'taxonomy'  => 'category',
					'category'  => 0,
				),
				$atts
			)
		);

		$field = 'slug';
		if ( intval( $category ) ) {
			$field = 'id';
		}

		$boxes_query = new WP_Query(
			array(
				'posts_per_page' => $count,
				'post_type'      => $post_type,
				'tax_query'      => array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => $field,
						'terms'    => $category,
					),
				),
			)
		);

		$boxes = array();
		foreach ( $boxes_query->posts as $box_key => $box_data ) {
			$box = array();

			if ( $title != 'false' && $title && $title != 'true' && $title != 1 ) {
				$box['title'] = $title;
			} else if ( $title != 'false' ) {
				$box['title'] = $box_data->post_title;
			} else {
				$box['title'] = '';
			}

			$box['contents'] = $box_data->post_content;

			$boxes[] = $box;
		}

		return $boxes;
	}
	public static function get_contents( $class, $id, $box_style, $boxes = array() ) {

		$contents = "<ul class=\"{$class}\" id=\"{$id}\">\n";

		foreach ( $boxes as $box ) {
			$contents .= "\t<li style=\"{$box_style}\">\n";

			if ( !empty( $box['title'] ) ) {
				$contents .= "\t\t<span class=\"{$class}_title\">\n";
				$contents .= "\t\t\t{$box['title']}\n";
				$contents .= "\t\t</span>\n";
			}

			$contents .= "\t\t<span class=\"{$class}_contents\">\n";
			$contents .= "\t\t\t{$box['contents']}\n";
			$contents .= "\t\t</span>\n";

			$contents .= "\t</li>\n";
		}

		$contents .= "\n</ul>\n";

		return $contents;
	}
}
$feature_boxes = new Feature_Boxes();
