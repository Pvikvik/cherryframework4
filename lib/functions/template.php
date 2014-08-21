<?php
/**
 * Functions for loading template parts. These functions are helper functions or more flexible functions
 * than what core WordPress currently offers with template part loading.
 *
 * @package    Cherry_Framework
 * @subpackage Functions
 * @author     Cherry Team <support@cherryframework.com>
 * @copyright  Copyright (c) 2012 - 2014, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

add_action( 'cherry_get_header',         'cherry_get_header_template' );
add_action( 'cherry_get_footer',         'cherry_get_footer_template' );
// add_action( 'cherry_content',            'cherry_get_page_template' );

add_action( 'cherry_post',               'cherry_get_content_template' );
add_action( 'cherry_page',               'cherry_get_content_template' );

add_action( 'cherry_get_sidebar',        'cherry_get_sidebar_template' );
add_action( 'cherry_get_footer_sidebar', 'cherry_get_sidebar_template' );

add_action( 'cherry_get_comments',       'cherry_get_comments_template' );

add_action( 'cherry_loop_else',          'cherry_noposts' );

/**
 * This is a replacement function for the WordPress `get_header()` function.
 *
 * @since  4.0.0
 * @param  string $name
 * @return void
 */
function cherry_get_header_template( $name = null ) {

	if ( '' !== $name ) :

		get_header( $name );

	else :

		if ( function_exists( 'cherry_template_base' ) ) {

			get_header( cherry_template_base() );

		} else {

			get_header();

		}

	endif;
}

/**
 * This is a replacement function for the WordPress `get_footer()` function.
 *
 * @since  4.0.0
 * @param  string $name
 * @return void
 */
function cherry_get_footer_template( $name = null ) {

	if ( '' !== $name ) :

		get_footer( $name );

	else :

		if ( function_exists( 'cherry_template_base' ) ) {

			get_footer( cherry_template_base() );

		} else {

			get_footer();

		}

	endif;
}

/**
 * Loads appropriate page template file.
 *
 * @since 4.0.0
 */
function cherry_get_page_template() {
	include cherry_template_path();
}

/**
 * Loads a post content template based on the post type and/or the post format.
 *
 * @since  4.0.0
 * @return string
 */
function cherry_get_content_template() {

	// Set up an empty array and get the post type.
	$templates = array();
	$post_type = get_post_type();

	// If the post type supports 'post-formats', get the template based on the format.
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		// Get the post format.
		$post_format = get_post_format() ? get_post_format() : 'standard';

		// Template based on post type and post format.
		$templates[] = "content-{$post_type}-{$post_format}.php";
		$templates[] = "content/{$post_type}-{$post_format}.php";

		// Template based on the post format.
		$templates[] = "content-{$post_format}.php";
		$templates[] = "content/{$post_format}.php";
	}

	// Template based on the post type.
	$templates[] = "content-{$post_type}.php";
	$templates[] = "content/{$post_type}.php";

	// Fallback 'content.php' template.
	$templates[] = 'content.php';
	$templates[] = 'content/content.php';

	// Allow devs to filter the content template hierarchy.
	$templates = apply_filters( 'cherry_content_template_hierarchy', $templates );

	// Apply filters and return the found content template.
	include( apply_filters( 'cherry_content_template', locate_template( $templates, false, false ) ) );
}

/**
 * Loads template for sidebar by $id.
 *
 * @since  4.0.0
 * @param  string $id Sidebar id
 * @return string     The template filename
 */
function cherry_get_sidebar_template( $id ) {

	if ( function_exists( 'cherry_display_sidebar' ) && cherry_display_sidebar( $id ) ) {

		if ( function_exists( 'cherry_sidebar_path' ) ) :

			include cherry_sidebar_path( $id );

		endif;

	} elseif ( !function_exists( 'cherry_display_sidebar' ) ) {

		$templates = array();

		if ( '' !== $id ) {

			$templates[] = "{$id}.php";
			$templates[] = "templates/{$id}.php";

		} else {

			$templates[] = 'sidebar-main.php';
			$templates[] = 'templates/sidebar-main.php';

		}

		locate_template( $templates, true, false );
	}
}

/**
 * Loads template for comments.
 *
 * @since  4.0.0
 */
function cherry_get_comments_template() {

	if ( !post_type_supports( get_post_type(), 'comments' ) ) {
		return;
	}

	// If viewing a single post/page/CPT.
	if ( is_singular() ) :

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :

			// Loads the comments.php template.
			comments_template( '/templates/comments.php', true );

		endif;

	endif;
}

/**
 * Loads template if no posts were found.
 *
 * @since  4.0.0
 */
function cherry_noposts() {
	get_template_part( 'content/none' );
}