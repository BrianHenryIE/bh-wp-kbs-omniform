<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           brianhenryie/bh-wp-kbs-omniform
 *
 * @wordpress-plugin
 * Plugin Name:       BH WP KBS OmniForm
 * Plugin URI:        http://github.com/username/bh-wp-kbs-omniform/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Requires PHP:      7.4
 * Author:            BrianHenryIE
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wp-kbs-omniform
 * Domain Path:       /languages
 *
 * GitHub Plugin URI: https://github.com/username/bh-wp-kbs-omniform/
 * Release Asset:     true
 */

namespace BrianHenryIE\WP_KBS_OmniForm;

use BrianHenryIE\WP_KBS_OmniForm\Alley_Interactive\Autoloader\Autoloader;
use BrianHenryIE\WP_KBS_OmniForm\WP_Includes\Activator;
use BrianHenryIE\WP_KBS_OmniForm\WP_Includes\Deactivator;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	throw new \Exception( 'WordPress required but not loaded.' );
}

// Load strauss classes after autoload-classmap.php so classes can be substituted.
require_once __DIR__ . '/vendor-prefixed/autoload.php';

Autoloader::generate( 'BrianHenryIE\WP_KBS_OmniForm', __DIR__ . '/src', )->register();

define( 'BH_WP_KBS_OMNIFORM_VERSION', '1.0.0' );
define( 'BH_WP_KBS_OMNIFORM_BASENAME', plugin_basename( __FILE__ ) );
define( 'BH_WP_KBS_OMNIFORM_PATH', plugin_dir_path( __FILE__ ) );
define( 'BH_WP_KBS_OMNIFORM_URL', trailingslashit( plugins_url( plugin_basename( __DIR__ ) ) ) );

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function instantiate_bh_wp_kbs_omniform(): BH_WP_KBS_OmniForm {

	$settings = new Settings();

	$plugin = new BH_WP_KBS_OmniForm( $settings );

	return $plugin;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and frontend-facing site hooks.
 */
$GLOBALS['bh_wp_kbs_omniform'] = instantiate_bh_wp_kbs_omniform();
