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
	public function __construct() {
		add_shortcode( 'feature_boxes', array( get_class(), 'do_feature_boxes' ) );
	}
	public static function do_feature_boxes( $atts ) {

		$boxes = self::get_boxes( $atts );

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
		if( intval( $category ) ) {
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
		foreach( $boxes_query->posts as $box_key => $box_data ) {
			$box = array();

			if( $title != 'false' && $title && $title != 'true' && $title != 1 ) {
				$box['title'] = $title;
			} else if( $title != 'false' ) {
				$box['title'] = $box_data->post_title;
			} else {
				$box['title'] = '';
			}

			$box['content'] = $box_data->post_content;

			$boxes[] = $box;
		}

		return $boxes;
	}
}
$feature_boxes = new Feature_Boxes();
