<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Code_Inserter
 * @subpackage Code_Inserter/core
 */

namespace Code_Inserter\Core;

use Code_Inserter\Admin\Admin;
use Code_Inserter\Front\Front;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Code_Inserter
 * @subpackage Code_Inserter/core
 * @author     Vo3da
 */
class Main {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The plugin options
	 *
	 * @var array $options
	 */
	private $options;

	/**
	 * Main constructor.
	 */
	public function __construct() {
		if ( defined( 'VO3DA_CODE_INSERTER_VERSION' ) ) {
			$this->version = VO3DA_CODE_INSERTER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'vo3da-code-inserter';
		$options           = get_option( 'code_inserter' );
		$this->options     = ! empty( $options ) ? $options : [];
		$this->amp         = $this->check_amp();
	}

	/**
	 * Initialization method. Runs admin and front side of plugin.
	 */
	public function init() {
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new I18n( $this->get_plugin_name() );
		$plugin_i18n->hooks();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$admin = new Admin( $this->get_plugin_name(), $this->get_version(), $this->options );
		$admin->hooks();

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$front = new Front( $this->get_plugin_name(), $this->get_version(), $this->options, $this->amp );
		$front->hooks();
	}

	/**
	 * Check if is amp page now
	 *
	 * @return bool
	 */
	public function check_amp() {
		$uri      = filter_var( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', FILTER_SANITIZE_STRING );
		$uri_a    = explode( '/', $uri );
		$count_a  = count( $uri_a );
		$last     = $count_a - 1;
		$pre_last = $last - 1;
		$amp      = 'amp';

		if ( $uri_a[ $last ] === $amp ) {
			return true;
		} elseif ( $uri_a[ $pre_last ] === $amp && '' === $uri_a[ $last ] ) {
			return true;
		}
		if ( '?amp' === $uri_a[ $last ] ) {
			return true;
		}
		if ( '?amp=1' === $uri_a[ $last ] ) {
			return true;
		}
		if ( stristr( $uri_a[ $last ], '?amp' ) ) {
			return true;
		}
		if ( in_array( 'amp', $uri_a, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
