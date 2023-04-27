<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array( 'astra-theme-css' ), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );


/**
 * Redirect the user away from the site if their IP address starts with 77.29
 */
function redirect_users_away() {
	$ip = $_SERVER['REMOTE_ADDR'];
	if ( substr( $ip, 0, 5 ) === "77.29" ) {
		wp_redirect( 'https://www.not-allowed.com/', 301 );
		exit;
	}
}

add_action( 'template_redirect', 'redirect_users_away' );

/**
 * Register post type called "projects"
 */

function register_projects_post_type() {
	register_post_type( 'projects',
		array(
			'labels'      => array(
				'name'          => __( 'Projects' ),
				'singular_name' => __( 'Project' )
			),
			'public'      => true,
			'has_archive' => true,
			'supports'    => array( 'title', 'editor', 'thumbnail' ),
			'taxonomies'  => array( 'project_type' ),
			'rewrite'     => array( 'slug' => 'projects', 'with_front' => false, ),
		)
	);
}

add_action( 'init', 'register_projects_post_type' );


/**
 * Register taxonomy called "project_type"
 */

function register_project_type_taxonomy() {
	register_taxonomy( 'project_type', 'projects',
		array(
			'labels'       => array(
				'name'          => __( 'Project Types' ),
				'singular_name' => __( 'Project Type' )
			),
			'public'       => true,
			'rewrite'      => array( 'slug' => 'project_type' ),
			'hierarchical' => true,
		)
	);
}

add_action( 'init', 'register_project_type_taxonomy' );


/**
 * Create an Ajax endpoint that will output the last three published "Projects" that belong in the "Project Type"
 * called "Architecture" If the user is not logged in. If the user is logged In it should return the last six published "Projects" in the project type call. "Architecture".
 * Results should be returned in the following JSON format {success: true, data: [{object}, {object}, {object}, {object}, {object}]}.
 * The object should contain three properties (id, title, link).
 */
function get_projects() {
	$projects = array();
	$args     = array(
		'post_type'      => 'projects',
		'posts_per_page' => is_user_logged_in() ? 6 : 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => array(
			array(
				'taxonomy' => 'project_type',
				'field'    => 'slug',
				'terms'    => 'architecture',
			),
		),
	);
	$query    = new WP_Query( $args );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$projects[] = array(
				'id'    => get_the_ID(),
				'title' => get_the_title(),
				'link'  => esc_url( get_the_permalink() ),
			);
		}
		wp_reset_postdata();
	}

	// We can do with this way
	//	wp_send_json_success( $projects );

	/**
	 * Alternate way for pretty print of JSON
	 */
	header( 'Content-Type: application/json' );
	echo json_encode( array(
		'success' => true,
		'data'    => $projects,
	), JSON_PRETTY_PRINT );
	wp_die();
}

add_action( 'wp_ajax_get_projects', 'get_projects' );
add_action( 'wp_ajax_nopriv_get_projects', 'get_projects' );

/**
 *  Use the WordPress HTTP API to create a function called hs_give_me_coffee() that will return a direct link to a cup of coffee. for us using the Random Coffee API [JSON].
 */

function hs_give_me_coffee() {
	$response = wp_remote_get( 'https://random-data-api.com/api/coffee/random_coffee' );
	if ( is_wp_error( $response ) ) {
		return false;
	}
	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body );
	if ( ! empty( $data->image ) ) {
		return $data->image;
	}

	return false;
}

/**
 *  Use this API https://api.kanye.rest/ and show 5 quotes on a page.
 */

function hs_get_kanye_quotes() {
	$response = wp_remote_get( 'https://api.kanye.rest/' );
	if ( is_wp_error( $response ) ) {
		return false;
	}
	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( $body );
	if ( ! empty( $data->quote ) ) {
		return $data->quote;
	}

	return false;
}