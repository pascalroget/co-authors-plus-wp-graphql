<?php
/**
 * Plugin Name:     Co-Authors Plus for WP GraphQL
 * Plugin URI:      https://github.com/pascalroget/co-authors-plus-wp-graphql
 * Description:     Adds Co-Authors Plus Support to WPGraphQL
 * Author:          Pascal Roget
 * Author URI:      https://github.com/pascalroget
 * Update URI:      https://github.com/pascalroget/co-authors-plus-wp-graphql
 * License: GPL-3
 * Requires at least: 5.4.1
 * Tested up to: 6.1
 * Requires PHP: 7.4
 * WPGraphQL requires at least: 1.8.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Version:         0.0.1
 */

namespace PascalRoget\CAPWPGQL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initialize the plugin
 */
function init() {
	/**
	 * If WPGraphQL is not active, show the admin notice
	 */
	if ( ! class_exists( 'WPGraphQL' ) ) {
		add_action( 'admin_init', __NAMESPACE__ . '\show_admin_notice' );
	} else {
        add_action( 'graphql_register_types', __NAMESPACE__ .'\register_coauthors_plus_connection' );
	}
}

add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Show admin notice to admins if this plugin is active but WPGraphQL
 * is not active
 *
 * @return bool
 */
function show_admin_notice() {
	/**
	 * For users with lower capabilities, don't show the notice
	 */
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	add_action(
		'admin_notices',
		function () {
			?>
            <div class="error notice">
                <p>WPGraphQL must be active for Co-Authors Plus for WPGraphQL to work'</p>
            </div>
			<?php
		}
	);

	return true;
}


/**
 * Registers a connection to Co Authors Plus in WPGraphQL
 * @throws \Exception
 */
function register_coauthors_plus_connection() {

	if ( function_exists( 'get_coauthors' ) ) {
		register_graphql_connection(
			[
				'fromType'           => 'Post',
				'toType'             => 'User',
				'fromFieldName'      => 'authors',
				'connectionTypeName' => 'PostToAuthorsConnection',
				'resolve'            => function ( \WPGraphQL\Model\Post $source, $args, $context, $info ) {
					$resolver = new \WPGraphQL\Data\Connection\UserConnectionResolver( $source, $args, $context, $info );

					$coauthor_ids = array_map(
						function( $coauthor ) {
							return $coauthor->ID;
						},
						get_coauthors( $source->ID )
					);

					$resolver->set_query_arg( 'include', $coauthor_ids );

					return $resolver->get_connection();
				},
			]
		);
	}

}
