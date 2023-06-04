<?php
/**
 * A plain object abstracting settings.
 *
 * @package brianhenryie/bh-wp-kbs-omniform
 */

namespace BrianHenryIE\WP_KBS_OmniForm;

/**
 * Typed settings.
 */
class Settings {

	/**
	 * The current plugin version, as defined in the root plugin file, or a string hopefully in sync with the root file.
	 *
	 * @used-by Admin_Assets::enqueue_scripts()
	 * @used-by Admin_Assets::enqueue_styles()
	 * @used-by Frontend_Assets::enqueue_scripts()
	 * @used-by Frontend_Assets::enqueue_styles()
	 *
	 * @return string
	 */
	public function get_plugin_version(): string {
		return defined( 'BH_WP_KBS_OMNIFORM_VERSION' )
			? BH_WP_KBS_OMNIFORM_VERSION
			: '1.0.0';
	}

	/**
	 * The plugin basename, as defined in the root plugin file, or a string hopefully in sync with the true basename.
	 *
	 * @used-by Admin_Assets::enqueue_scripts()
	 * @used-by Admin_Assets::enqueue_styles()
	 * @used-by Frontend_Assets::enqueue_scripts()
	 * @used-by Frontend_Assets::enqueue_styles()
	 *
	 * @return string
	 */
	public function get_plugin_basename(): string {
		return defined( 'BH_WP_KBS_OMNIFORM_BASENAME' )
			? BH_WP_KBS_OMNIFORM_BASENAME
			: 'bh-wp-kbs-omniform/bh-wp-kbs-omniform.php';
	}
}
