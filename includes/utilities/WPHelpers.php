<?php

namespace WPGraphQL\ContentBlocks\Utilities;

use stdClass;
use WP_Post;
use WP_Block_Editor_Context;
use WPGraphQL\Utils\Utils;

/**
 * Class WPHelpers
 *
 * @package WPGraphQL\ContentBlocks\Utilities
 */
final class WPHelpers {
	/**
	 * Gets Block Editor supported post types
	 *
	 * @return array<string>
	 */
	public static function get_supported_post_types() {
		$supported_post_types = array();
		// Get Post Types that are set to Show in GraphQL and Show in REST
		// If it doesn't show in REST, it's not block-editor enabled
		$block_editor_post_types = \WPGraphQL::get_allowed_post_types( 'objects' );

		if ( empty( $block_editor_post_types ) || ! is_array( $block_editor_post_types ) ) {
			return;
		}
		// Iterate over the post types
		foreach ( $block_editor_post_types as $block_editor_post_type ) {
			// If the post type doesn't support the editor, it's not block-editor enabled
			if ( ! post_type_supports( $block_editor_post_type->name, 'editor' ) ) {
				continue;
			}

			if ( ! isset( $block_editor_post_type->graphql_single_name ) ) {
				continue;
			}

			$supported_post_types[] = Utils::format_type_name( $block_editor_post_type->graphql_single_name );
		}

		return $supported_post_types;
	}

	/**
	 * Gets the get_block_editor_context of a specific Post Type
	 *
	 * @param string $post_type The Post Type to use.
	 * @param number $id The Post Id to use.
	 *
	 * @return WP_Block_Editor_Context The Block Editor Context
	 */
	public static function get_block_editor_context( $post_type, $id = -99 ) {
		$post_id              = $id;
		$post                 = new stdClass();
		$post->ID             = $post_id;
		$post->post_author    = 1;
		$post->post_date      = current_time( 'mysql' );
		$post->post_date_gmt  = current_time( 'mysql', 1 );
		$post->post_title     = '';
		$post->post_content   = '';
		$post->post_status    = '';
		$post->comment_status = 'closed';
		$post->ping_status    = 'closed';
		$post->post_name      = 'fake-post-' . rand( 1, 99999 );

		$post->post_type      = $post_type;
		$post->filter         = 'raw';
		$block_editor_context = new WP_Block_Editor_Context( array( 'post' => new WP_Post( $post ) ) );
		return $block_editor_context;
	}
}
