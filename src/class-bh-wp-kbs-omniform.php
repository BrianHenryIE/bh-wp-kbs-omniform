<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * frontend-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    brianhenryie/bh-wp-kbs-omniform
 */

namespace BrianHenryIE\WP_KBS_OmniForm;

use BrianHenryIE\WP_KBS_OmniForm\Admin\Admin_Assets;
use BrianHenryIE\WP_KBS_OmniForm\OmniForm\All_Forms;
use BrianHenryIE\WP_KBS_OmniForm\OmniForm\View_Responses;
use BrianHenryIE\WP_KBS_OmniForm\WP_Includes\I18n;
use BrianHenryIE\WP_KBS_OmniForm\WP_Includes\Post;

/**
 * Hooks the plugin's classes to WordPress's actions and filters.
 */
class BH_WP_KBS_OmniForm {

	/**
	 * The plugin settings.
	 *
	 * @var Settings
	 */
	protected Settings $settings;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the frontend-facing side of the site.
	 *
	 * @since    1.0.0
	 *
	 * @param Settings $settings The plugin settings, to pass to classes as they are instantiated.
	 */
	public function __construct( Settings $settings ) {

		$this->settings = $settings;

		$this->set_locale();
		$this->define_admin_hooks();

		$this->define_post_hooks();
		$this->define_view_responses_screen_hooks();
		$this->define_all_forms_screen_hooks();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	protected function set_locale(): void {

		$plugin_i18n = new I18n();

		add_action( 'init', array( $plugin_i18n, 'load_plugin_textdomain' ) );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	protected function define_admin_hooks(): void {

		$admin_assets = new Admin_Assets( $this->settings );

		add_action( 'admin_enqueue_scripts', array( $admin_assets, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $admin_assets, 'enqueue_scripts' ) );

	}

	/**
	 * Define hooks triggered by WP_Post actions.
	 */
	public function define_post_hooks(): void {

		$post = new Post();

		add_action( 'wp_after_insert_post', array( $post, 'create_ticket_from_form' ), 10, 4 );
	}

	/**
	 * Define hooks related to the View Responses screen.
	 */
	public function define_view_responses_screen_hooks(): void {

		$view_responses = new View_Responses();

		add_filter( 'manage_omniform_response_posts_columns', array( $view_responses, 'add_ticket_to_columns' ), 999 );
		add_action( 'manage_omniform_response_posts_custom_column', array( $view_responses, 'print_ticket_column' ), 10, 2 );
	}

	/**
	 * Define hooks for the All Forms screen.
	 */
	public function define_all_forms_screen_hooks(): void {

		$all_forms = new All_Forms();

		add_filter( 'manage_omniform_posts_columns', array( $all_forms, 'add_ticket_to_columns' ), 999 );
		add_action( 'manage_omniform_posts_custom_column', array( $all_forms, 'print_ticket_column' ), 10, 2 );
		add_filter( 'default_hidden_columns', array( $all_forms, 'add_column_to_hidden_list' ), 10, 2 );

		add_action( 'quick_edit_custom_box', array( $all_forms, 'quick_edit' ), 10, 2 );

		// Early because the regular one ends with die.
		add_action( 'wp_ajax_inline-save', array( $all_forms, 'ajax_save' ), 1 );
	}
}
