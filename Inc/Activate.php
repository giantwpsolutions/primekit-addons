<?php 
namespace PrimeKit;

/**
 * Don't allow direct access
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Plugin activation class
 *
 * Contains methods that are triggered when the plugin is activated.
 */
class Activate {

	/**
	 * Perform actions on plugin activation, like setting default options or creating database tables.
	 *
	 * This function is intended to be run when the plugin is activated.
	 * It ensures that WordPress rewrite rules are flushed to account for any changes
	 * in custom post types or taxonomies that the plugin may introduce.
	 */
	public static function activate() {
		flush_rewrite_rules();

		// Skip wizard on multisite or if user already ran it once
		if (is_multisite() || get_option('primekit_wizard_completed')) {
			return;
		}

		// AdminManager::maybe_redirect() picks this up on the next page load
		set_transient('primekit_setup_wizard_redirect', true, 60);
	}
}