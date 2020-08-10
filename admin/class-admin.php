<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Code_Inserter
 * @subpackage Code_Inserter/Admin
 */

namespace Code_Inserter\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Code_Inserter
 * @subpackage Code_Inserter/admin
 * @author     VO3DA Team
 */
class Admin {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	private $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of the plugin.
	 */
	private $version;

	/**
	 * The plugin settings
	 *
	 * @var array $options
	 */
	private $options;

	/**
	 * Current page name string
	 *
	 * @var mixed
	 */
	private $page;
	/**
	 * Domains of the current site
	 *
	 * @var array
	 */
	private $mirrors;

	/**
	 * Admin constructor.
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version     Plugin version.
	 * @param array  $options     Plugin options.
	 */
	public function __construct( string $plugin_name, string $version, array $options ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->options     = $options;
		$this->page        = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
		register_setting(
			'code-insert-settings-group',
			'code_inserter'
		);
		$this->mirrors = vo3da_get_mirrors( get_current_blog_id() );
		if ( $this->check_migration() === false ) {
			$this->migration();
		}
	}

	/**
	 * Run admin actions and filters
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_plugin_position' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'wp_ajax_code_inserter', [ $this, 'get_domain_options' ] );
		add_action( 'wp_ajax_code_inserter_save_form', [ $this, 'save_form' ] );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 *
	 * @add_action('admin_enqueue_scripts', 'enqueue_styles')
	 */
	public function enqueue_styles() {
		if ( ! empty( $this->page ) && $this->page === $this->plugin_name ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/code-inserter-admin.min.css', [], $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 *
	 * @add_action('admin_enqueue_scripts', 'enqueue_scripts')
	 */
	public function enqueue_scripts() {
		if ( ! empty( $this->page ) && $this->page === $this->plugin_name ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/code-inserter-admin-min.js', [ 'jquery' ], $this->version, false );
		}
	}

	/**
	 * Register the JavaScript for plugin position function.
	 *
	 * @since 1.0.0
	 *
	 * @add_action('admin_enqueue_scripts', 'enqueue_plugin_position')
	 */
	public function enqueue_plugin_position() {
		if ( ! wp_script_is( 'vo3da-plugin-position-script' ) ) {
			wp_enqueue_script( 'vo3da-plugin-position-script', plugin_dir_url( __FILE__ ) . 'js/vo3da-plugin-position.js', [ 'jquery' ], '1.0', true );
		}
	}

	/**
	 * Register plugin page in menu
	 *
	 * @add_action('admin_menu', 'add_menu')
	 */
	public function add_menu() {
		$parent_menu_name = 'VO3DA Plugins';
		$parent_menu_slug = 'vo3da-plugins';
		global $admin_page_hooks;
		if ( empty( $admin_page_hooks[ $parent_menu_slug ] ) ) {
			add_menu_page(
				$parent_menu_name,
				$parent_menu_name,
				'manage_options',
				$parent_menu_slug,
				'',
				'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHdpZHRoPSIxOXB0IiBoZWlnaHQ9IjE3cHQiIHZpZXdCb3g9IjAgMCAxOSAxNyIgdmVyc2lvbj0iMS4xIj48ZyBpZD0ic3VyZmFjZTEiPjxwYXRoIHN0eWxlPSIgc3Ryb2tlOm5vbmU7ZmlsbC1ydWxlOm5vbnplcm87ZmlsbDojYTBhNWFhO2ZpbGwtb3BhY2l0eToxOyIgZD0iTSAxOC40NzI2NTYgMC45MTAxNTYgQyAxOC42NDQ1MzEgMC42MTMyODEgMTguODE2NDA2IDAuMzE2NDA2IDE4Ljk5NjA5NCAwIEwgMTQuMzg2NzE5IDAgQyAxNC4yOTY4NzUgMCAxNC4yMDcwMzEgMCAxNC4xMTcxODggMCBDIDE0LjA4NTkzOCAtMC4wMDM5MDYyNSAxNC4wNTQ2ODggMC4wMTE3MTg4IDE0LjAzOTA2MiAwLjAzOTA2MjUgQyAxMy44Nzg5MDYgMC4zMTY0MDYgMTMuNzIyNjU2IDAuNTkzNzUgMTMuNTYyNSAwLjg3MTA5NCBDIDEzLjQyNTc4MSAxLjA4OTg0NCAxMy4yOTI5NjkgMS4zMTI1IDEzLjE3NTc4MSAxLjUzOTA2MiBDIDEzLjA1NDY4OCAxLjczNDM3NSAxMi45Mzc1IDEuOTMzNTk0IDEyLjgyODEyNSAyLjE0MDYyNSBDIDEyLjY5NTMxMiAyLjM1MTU2MiAxMi41NzAzMTIgMi41NzAzMTIgMTIuNDUzMTI1IDIuNzkyOTY5IEMgMTIuMzI0MjE5IDMgMTIuMTk5MjE5IDMuMjA3MDMxIDEyLjA4OTg0NCAzLjQyMTg3NSBDIDExLjk4NDM3NSAzLjU4NTkzOCAxMS44ODY3MTkgMy43NSAxMS43OTY4NzUgMy45MjU3ODEgQyAxMS43MTQ4NDQgNC4wNTA3ODEgMTEuNjM2NzE5IDQuMTc5Njg4IDExLjU3MDMxMiA0LjMxNjQwNiBDIDExLjQ4MDQ2OSA0LjQ1MzEyNSAxMS4zOTg0MzggNC41OTM3NSAxMS4zMjQyMTkgNC43MzgyODEgQyAxMS4yMjY1NjIgNC44OTA2MjUgMTEuMTM2NzE5IDUuMDQ2ODc1IDExLjA1NDY4OCA1LjIxMDkzOCBDIDEwLjk2ODc1IDUuMzM1OTM4IDEwLjg5MDYyNSA1LjQ2ODc1IDEwLjgyNDIxOSA1LjYwNTQ2OSBDIDEwLjczNDM3NSA1Ljc0MjE4OCAxMC42NTIzNDQgNS44ODI4MTIgMTAuNTc4MTI1IDYuMDI3MzQ0IEMgMTAuNDk2MDk0IDYuMTU2MjUgMTAuNDE3OTY5IDYuMjg1MTU2IDEwLjM1MTU2MiA2LjQyMTg3NSBDIDEwLjI1IDYuNTc0MjE5IDEwLjE2MDE1NiA2LjczNDM3NSAxMC4wODIwMzEgNi44OTg0MzggQyAxMC4wMTE3MTkgNi45NzY1NjIgOS45NzI2NTYgNy4wNzgxMjUgOS45MTc5NjkgNy4xNjc5NjkgQyA5Ljg5MDYyNSA3LjIxMDkzOCA5Ljg3NSA3LjIxNDg0NCA5Ljg0Mzc1IDcuMTY3OTY5IEMgOS44MDg1OTQgNy4xMTMyODEgOS43ODkwNjIgNy4wNDI5NjkgOS43NDIxODggNi45OTYwOTQgQyA5LjY3MTg3NSA2Ljg1MTU2MiA5LjU5NzY1NiA2LjcxNDg0NCA5LjUxMTcxOSA2LjU4MjAzMSBDIDkuNDI1NzgxIDYuNDA2MjUgOS4zMjgxMjUgNi4yMzQzNzUgOS4yMjI2NTYgNi4wNjY0MDYgQyA5LjEzNjcxOSA1LjkzMzU5NCA5LjA4OTg0NCA1Ljc3NzM0NCA4Ljk5MjE4OCA1LjY1MjM0NCBDIDguOTIxODc1IDUuNSA4LjgzNTkzOCA1LjM1MTU2MiA4Ljc0NjA5NCA1LjIxMDkzOCBDIDguNjc5Njg4IDUuMDY2NDA2IDguNjAxNTYyIDQuOTI5Njg4IDguNTE1NjI1IDQuNzk2ODc1IEMgOC40Mjk2ODggNC42NjQwNjIgOC4zODI4MTIgNC41MTE3MTkgOC4yODkwNjIgNC4zODI4MTIgQyA4LjE5OTIxOSA0LjIzNDM3NSA4LjEzMjgxMiA0LjA3MDMxMiA4LjAzNTE1NiAzLjkyOTY4OCBDIDcuOTQxNDA2IDMuNzQ2MDk0IDcuODQzNzUgMy41NjI1IDcuNzM0Mzc1IDMuMzkwNjI1IEMgNy41OTc2NTYgMy4xMzI4MTIgNy40NjQ4NDQgMi44Nzg5MDYgNy4zMTI1IDIuNjMyODEyIEMgNy4yMTg3NSAyLjQ0NTMxMiA3LjExNzE4OCAyLjI2MTcxOSA3LjAwNzgxMiAyLjA4MjAzMSBDIDYuOTE0MDYyIDEuODk0NTMxIDYuODEyNSAxLjcwNzAzMSA2LjY5NTMxMiAxLjUzMTI1IEMgNi41ODIwMzEgMS4zMjgxMjUgNi40ODgyODEgMS4xMDU0NjkgNi4zNTU0NjkgMC45MTAxNTYgQyA2LjIwNzAzMSAwLjY0MDYyNSA2LjA1NDY4OCAwLjM2NzE4OCA1LjkxMDE1NiAwLjA5Mzc1IEMgNS44ODI4MTIgMC4wMzEyNSA1LjgyMDMxMiAtMC4wMDc4MTI1IDUuNzUzOTA2IDAgQyAzLjg3NSAwLjAwMzkwNjI1IDEuOTk2MDk0IDAuMDAzOTA2MjUgMC4xMTMyODEgMC4wMDM5MDYyNSBMIDAuMDAzOTA2MjUgMC4wMDM5MDYyNSBMIDAuNTQyOTY5IDAuOTUzMTI1IEMgMC42NDQ1MzEgMS4xNTYyNSAwLjc1NzgxMiAxLjM1MTU2MiAwLjg3MTA5NCAxLjU0Njg3NSBDIDAuOTcyNjU2IDEuNzM0Mzc1IDEuMDc0MjE5IDEuOTE3OTY5IDEuMTc5Njg4IDIuMTAxNTYyIEMgMS4yODUxNTYgMi4zMDQ2ODggMS4zOTQ1MzEgMi41MDM5MDYgMS41MTE3MTkgMi42OTUzMTIgQyAxLjY0MDYyNSAyLjk0NTMxMiAxLjc3NzM0NCAzLjE4NzUgMS45MjE4NzUgMy40Mjk2ODggQyAyLjAxMTcxOSAzLjYxMzI4MSAyLjExMzI4MSAzLjc5Mjk2OSAyLjIxODc1IDMuOTY4NzUgQyAyLjI4OTA2MiA0LjExNzE4OCAyLjM3MTA5NCA0LjI1NzgxMiAyLjQ1NzAzMSA0LjM5ODQzOCBMIDIuNjg3NSA0LjgxNjQwNiBMIDIuOTMzNTk0IDUuMjUzOTA2IEMgMy4wMDM5MDYgNS40MDIzNDQgMy4wODU5MzggNS41NDY4NzUgMy4xNzU3ODEgNS42ODc1IEMgMy4yNDIxODggNS44MjQyMTkgMy4zMTI1IDUuOTU3MDMxIDMuMzk0NTMxIDYuMDg1OTM4IEwgMy42Nzk2ODggNi42MDE1NjIgQyAzLjc1NzgxMiA2Ljc0NjA5NCAzLjgzMjAzMSA2Ljg5NDUzMSAzLjkyNTc4MSA3LjAzNTE1NiBDIDQuMDAzOTA2IDcuMTk5MjE5IDQuMDkzNzUgNy4zNTkzNzUgNC4xOTE0MDYgNy41MTE3MTkgQyA0LjI2OTUzMSA3LjY4MzU5NCA0LjM2NzE4OCA3Ljg0Mzc1IDQuNDYwOTM4IDguMDAzOTA2IEwgNC42NzE4NzUgOC4zODI4MTIgQyA0Ljc0NjA5NCA4LjUzMTI1IDQuODI4MTI1IDguNjc1NzgxIDQuOTE3OTY5IDguODE2NDA2IEwgNS4xNjQwNjIgOS4yNzM0MzggTCA1LjQ1MzEyNSA5Ljc4OTA2MiBDIDUuNTUwNzgxIDkuOTcyNjU2IDUuNjQ0NTMxIDEwLjE1NjI1IDUuNzU3ODEyIDEwLjMyODEyNSBDIDUuODI0MjE5IDEwLjQ2ODc1IDUuODk4NDM4IDEwLjYwOTM3NSA1Ljk4NDM3NSAxMC43NDIxODggQyA2LjA1MDc4MSAxMC44ODY3MTkgNi4xMjg5MDYgMTEuMDIzNDM4IDYuMjE0ODQ0IDExLjE1NjI1IEMgNi4yOTI5NjkgMTEuMzIwMzEyIDYuMzgyODEyIDExLjQ4MDQ2OSA2LjQ4NDM3NSAxMS42MzI4MTIgQyA2LjU1ODU5NCAxMS43ODkwNjIgNi42NDA2MjUgMTEuOTQxNDA2IDYuNzM0Mzc1IDEyLjA4NTkzOCBDIDYuNzk2ODc1IDEyLjIxODc1IDYuODY3MTg4IDEyLjM0Mzc1IDYuOTQxNDA2IDEyLjQ2NDg0NCBDIDcuMDE1NjI1IDEyLjYxNzE4OCA3LjA5NzY1NiAxMi43NjE3MTkgNy4xOTE0MDYgMTIuOTAyMzQ0IEMgNy4yNjk1MzEgMTMuMDcwMzEyIDcuMzU5Mzc1IDEzLjIzNDM3NSA3LjQ2MDkzOCAxMy4zOTQ1MzEgQyA3LjU1NDY4OCAxMy41ODIwMzEgNy42NjAxNTYgMTMuNzY5NTMxIDcuNzY1NjI1IDEzLjk0OTIxOSBDIDcuODU5Mzc1IDE0LjEzMjgxMiA3Ljk2MDkzOCAxNC4zMTY0MDYgOC4wNzQyMTkgMTQuNDg4MjgxIEMgOC4xNDQ1MzEgMTQuNjQ4NDM4IDguMjMwNDY5IDE0Ljc5Njg3NSA4LjMyMDMxMiAxNC45NDUzMTIgQyA4LjQxMDE1NiAxNS4xMjEwOTQgOC41MDM5MDYgMTUuMjkyOTY5IDguNjA5Mzc1IDE1LjQ2MDkzOCBDIDguNzIyNjU2IDE1LjY3OTY4OCA4LjgzOTg0NCAxNS45MDIzNDQgOC45NzI2NTYgMTYuMTEzMjgxIEMgOS4wMTE3MTkgMTYuMTg3NSA5LjA1MDc4MSAxNi4yNjU2MjUgOS4wODk4NDQgMTYuMzM5ODQ0IEMgOS4xOTkyMTkgMTYuNTM1MTU2IDkuMzA4NTk0IDE2LjczNDM3NSA5LjQyMTg3NSAxNi45MjU3ODEgQyA5LjQ4MDQ2OSAxNy4wMjczNDQgOS40OTIxODggMTcuMDIzNDM4IDkuNTQ2ODc1IDE2LjkyMTg3NSBDIDkuNTg1OTM4IDE2Ljg1MTU2MiA5LjYyNSAxNi43ODUxNTYgOS42NjQwNjIgMTYuNzE0ODQ0IEMgOS43Njk1MzEgMTYuNTE1NjI1IDkuODk0NTMxIDE2LjMyNDIxOSA5Ljk4ODI4MSAxNi4xMTMyODEgQyAxMC4xMjg5MDYgMTUuODk4NDM4IDEwLjI1IDE1LjY2Nzk2OSAxMC4zNzEwOTQgMTUuNDQxNDA2IEMgMTAuNDY4NzUgMTUuMjg5MDYyIDEwLjU1ODU5NCAxNS4xMjg5MDYgMTAuNjM2NzE5IDE0Ljk2NDg0NCBDIDEwLjcyMjY1NiAxNC44MDQ2ODggMTAuODMyMDMxIDE0LjY1NjI1IDEwLjkwNjI1IDE0LjQ4ODI4MSBMIDExLjIwNzAzMSAxMy45NTMxMjUgTCAxMS41MTU2MjUgMTMuMzk4NDM4IEMgMTEuNjA1NDY5IDEzLjIzNDM3NSAxMS43MDMxMjUgMTMuMDc0MjE5IDExLjc4NTE1NiAxMi45MDIzNDQgQyAxMS44NzUgMTIuNzYxNzE5IDExLjk1NzAzMSAxMi42MTcxODggMTIuMDMxMjUgMTIuNDY0ODQ0IEMgMTIuMTA1NDY5IDEyLjM1NTQ2OSAxMi4xNzE4NzUgMTIuMjM0Mzc1IDEyLjIyNjU2MiAxMi4xMTMyODEgQyAxMi4yOTY4NzUgMTIuMDI3MzQ0IDEyLjMzMjAzMSAxMS45MTc5NjkgMTIuMzk0NTMxIDExLjgyODEyNSBDIDEyLjQxMDE1NiAxMS44MDQ2ODggMTIuNDEwMTU2IDExLjc3MzQzOCAxMi4zOTQ1MzEgMTEuNzUzOTA2IEwgMTIuMjk2ODc1IDExLjU5Mzc1IEMgMTIuMjI2NTYyIDExLjQ0MTQwNiAxMi4xNDQ1MzEgMTEuMjk2ODc1IDEyLjA1NDY4OCAxMS4xNTYyNSBDIDExLjk2NDg0NCAxMS4wMDc4MTIgMTEuOTA2MjUgMTAuODM5ODQ0IDExLjgwMDc4MSAxMC42OTkyMTkgQyAxMS43MzQzNzUgMTAuNTU4NTk0IDExLjY1NjI1IDEwLjQxNzk2OSAxMS41NzQyMTkgMTAuMjg1MTU2IEMgMTEuNDg4MjgxIDEwLjEwNTQ2OSAxMS4zOTA2MjUgOS45MzM1OTQgMTEuMjg5MDYyIDkuNzY5NTMxIEMgMTEuMjAzMTI1IDkuNTkzNzUgMTEuMTA1NDY5IDkuNDIxODc1IDExLjAwMzkwNiA5LjI1MzkwNiBDIDEwLjkxNzk2OSA5LjEwMTU2MiAxMC44NDc2NTYgOC45NDE0MDYgMTAuNzUgOC43OTY4NzUgQyAxMC42NzU3ODEgOC42NDQ1MzEgMTAuNTkzNzUgOC40OTIxODggMTAuNSA4LjM0Mzc1IEMgMTAuNDQxNDA2IDguMjE0ODQ0IDEwLjM3MTA5NCA4LjA4OTg0NCAxMC4yODkwNjIgNy45Njg3NSBDIDEwLjIwMzEyNSA3LjgwODU5NCAxMC4xMjg5MDYgNy42NDA2MjUgMTAuMDI3MzQ0IDcuNDkyMTg4IEMgMTAuMDAzOTA2IDcuNDQxNDA2IDkuOTYwOTM4IDcuNDAyMzQ0IDkuOTY0ODQ0IDcuMzM5ODQ0IEMgOS45NzI2NTYgNy4zMzU5MzggOS45ODA0NjkgNy4zMzIwMzEgOS45OTIxODggNy4zMzIwMzEgQyAxMC40MjU3ODEgNy4zMjAzMTIgMTAuODYzMjgxIDcuMzA0Njg4IDExLjI5Njg3NSA3LjI5Njg3NSBDIDExLjgzOTg0NCA3LjI4OTA2MiAxMi4zNzg5MDYgNy4yNzM0MzggMTIuOTE3OTY5IDcuMjU3ODEyIEMgMTMuNTAzOTA2IDcuMjQyMTg4IDE0LjA4OTg0NCA3LjIzNDM3NSAxNC42NzU3ODEgNy4yMTQ4NDQgQyAxNC45NTMxMjUgNy4yMDcwMzEgMTQuOTUzMTI1IDcuMjEwOTM4IDE1LjA4MjAzMSA2Ljk1MzEyNSBDIDE1LjA4NTkzOCA2Ljk0NTMxMiAxNS4wODU5MzggNi45NDE0MDYgMTUuMDg5ODQ0IDYuOTMzNTk0IEMgMTUuMTc5Njg4IDYuNzg5MDYyIDE1LjI2NTYyNSA2LjYzNjcxOSAxNS4zMzk4NDQgNi40ODA0NjkgQyAxNS40MzM1OTQgNi4zMzk4NDQgMTUuNTE1NjI1IDYuMTk1MzEyIDE1LjU4OTg0NCA2LjA0Mjk2OSBMIDE1LjgxMjUgNS42NDg0MzggQyAxNS44OTQ1MzEgNS41MTk1MzEgMTUuOTY0ODQ0IDUuMzg2NzE5IDE2LjAzMTI1IDUuMjUzOTA2IEMgMTYuMTI4OTA2IDUuMTA1NDY5IDE2LjIxMDkzOCA0Ljk1MzEyNSAxNi4yOTI5NjkgNC43OTY4NzUgTCAxNi41MzkwNjIgNC4zNTkzNzUgTCAxNi43Njk1MzEgMy45NDE0MDYgTCAxNy4wMzkwNjIgMy40Njg3NSBDIDE3LjE1NjI1IDMuMjU3ODEyIDE3LjI4OTA2MiAzLjA1NDY4OCAxNy4zODY3MTkgMi44MzIwMzEgQyAxNy41MjczNDQgMi42MTcxODggMTcuNjUyMzQ0IDIuMzkwNjI1IDE3Ljc2NTYyNSAyLjE2MDE1NiBDIDE3Ljg4NjcxOSAxLjk3NjU2MiAxNy45OTYwOTQgMS43ODUxNTYgMTguMDg5ODQ0IDEuNTg1OTM4IEMgMTguMjI2NTYyIDEuMzY3MTg4IDE4LjM1OTM3NSAxLjE0NDUzMSAxOC40NzI2NTYgMC45MTAxNTYgWiBNIDE4LjQ3MjY1NiAwLjkxMDE1NiAiLz48L2c+PC9zdmc+'
			);
		}

		add_submenu_page(
			$parent_menu_slug,
			'Code Inserter',
			'Code Inserter',
			'manage_options',
			$this->plugin_name,
			[
				$this,
				'page_options',
			]
		);
	}

	/**
	 * Page options view
	 */
	public function page_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html( 'You do not have sufficient permissions to access this page.' ) );
		}
		$version = $this->version;
		require_once plugin_dir_path( __FILE__ ) . 'partials/page-options.php';
	}

	/**
	 * Check plugin active
	 *
	 * @param string $plugin Name of plugin.
	 *
	 * @return bool
	 */
	private function check_if_plugin_active( string $plugin ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( $plugin );
	}

	/**
	 * Ajax callback for get settings
	 *
	 * @add_action('wp_ajax_code_inserter', 'get_domain_options')
	 */
	public function get_domain_options() {
		$domain = filter_input( INPUT_POST, 'domain', FILTER_SANITIZE_STRING );
		if ( ! empty( $domain ) ) {
			die( wp_json_encode( $this->options[ $domain ] ) );
		}
		die();
	}

	/**
	 * Ajax callback for saving form
	 *
	 * @add_action('wp_ajax_code_inserter_save_form', 'save_form')
	 */
	public function save_form() {

		$nonce = filter_input( INPUT_POST, '_nonce', FILTER_SANITIZE_STRING );
		if ( wp_verify_nonce( $nonce, 'code_inserter' ) ) {
			$domains = filter_input_array(
				INPUT_POST,
				[
					'domain' => [
						'filter' => FILTER_SANITIZE_STRING,
						'flags'  => FILTER_FORCE_ARRAY,
					],
				]
			)['domain'];
			if ( strcmp( $domains[0], '*' ) === 0 ) {
				$domains = $this->mirrors;
			}
			$new_data = filter_input( INPUT_POST, 'form_data', FILTER_DEFAULT );
			$new_data = $this->to_array( json_decode( $new_data ) );
			if ( ! empty( $new_data ) && array_key_exists( 'code_inserter[gtm]', $new_data ) && is_numeric( $new_data['code_inserter[gtm]']['content'] ) ) {
				$return = [
					'message' => esc_html__( "Google Tag Manager code can't contain only numbers", 'vo3da-code-inserter' ),
					'caption' => esc_html__('Error', 'vo3da-code-inserter'),
					'status'  => 'error',
				];
				wp_send_json( $return );
			}
			if ( ! empty( $new_data ) && array_key_exists( 'code_inserter[header_after]', $new_data ) && false !== strpos( $new_data['code_inserter[header_after]']['content'], '<div' ) || array_key_exists( 'code_inserter[header]', $new_data ) && false !== strpos( $new_data['code_inserter[header]']['content'], '<div' ) ) {
				$return = [
					'message' => esc_html__( "Head can't contain tag", 'vo3da-code-inserter' ) . ' &#60;div&#62;',
					'caption' => esc_html__('Error', 'vo3da-code-inserter'),
					'status'  => 'error',
				];
				wp_send_json( $return );
			}
			if ( is_array( $domains ) ) {
				foreach ( $domains as $domain ) {
					$old_options              = $this->options[ $domain ];
					$new_options              = [
						'header_after'         => $this->check_data( $new_data['code_inserter[header_after]'], $old_options['header_after'] ),
						'header'               => $this->check_data( $new_data['code_inserter[header]'], $old_options['header'] ),
						'body'                 => $this->check_data( $new_data['code_inserter[body]'], $old_options['body'] ),
						'body_before'          => $this->check_data( $new_data['code_inserter[body_before]'], $old_options['body_before'] ),
						'title_after_post'     => $this->check_data( $new_data['code_inserter[title_after_post]'], $old_options['title_after_post'] ),
						'title_after_category' => $this->check_data( $new_data['code_inserter[title_after_category]'], $old_options['title_after_category'] ),
						'title_after_page'     => $this->check_data( $new_data['code_inserter[title_after_page]'], $old_options['title_after_page'] ),
						'footer'               => $this->check_data( $new_data['code_inserter[footer]'], $old_options['footer'] ),
						'before_content'       => $this->check_data( $new_data['code_inserter[before_content]'], $old_options['before_content'] ),
						'after_content'        => $this->check_data( $new_data['code_inserter[after_content]'], $old_options['after_content'] ),
						'gtm'                  => $this->check_data( $new_data['code_inserter[gtm]'], $old_options['gtm'] ),
					];
					$this->options[ $domain ] = $new_options;
				}
				$this->save_options();
			}
			$return = [
				'message' => esc_html__( 'Your data saved', 'vo3da-code-inserter' ),
				'caption' => esc_html__('Success', 'vo3da-code-inserter'),
				'status'  => 'success',
			];
			wp_send_json( $return );
		} else {
			$return = [
				'message' => esc_html__( 'Error, try reload page!', 'vo3da-code-inserter' ),
				'caption' => esc_html__('Success', 'vo3da-code-inserter'),
				'status'  => 'error',
			];
			wp_send_json( $return );
		}

	}

	/**
	 * Migration options to new data structure
	 */
	private function migration() {
		$new_options = [];
		$amp_disable = 1;
		if ( ! $this->options['disable_on_amp'] || 0 === $this->options['disable_on_amp'] ) {
			$amp_disable = 0;
		}
		foreach ( $this->mirrors as $mirror ) {
			foreach ( $this->options as $index => $value ) {
				switch ( 0 ) {
					case strcmp( $index, 'beforecontent' ):
						$index = 'before_content';
						break;
					case strcmp( $index, 'aftercontent' ):
						$index = 'after_content';
						break;
					case strcmp( $index, 'gtm' ):
						if ( ! empty( $value ) && false === strpos( $value, 'GTM-' ) ) {
							$value = 'GTM-' . $value;
						}
						break;
				}

				$new_options[ $mirror ][ $index ]['content']        = $value;
				$new_options[ $mirror ][ $index ]['disable_on_amp'] = $amp_disable;

			}
		}
		if ( ! empty( $new_options ) ) {
			update_option( 'code_inserter', $new_options, true );
			update_option( 'code_inserter_migration', 1 );
		}
	}

	/**
	 * This function check migrated database.
	 */
	private function check_migration() {
		$status = get_option( 'code_inserter_migration' );
		if ( empty( $status ) || 0 === $status ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * The function checks the old and new data and returns it depending on the availability of new data
	 *
	 * @param array $new_data Array with new data.
	 * @param array $old_data Array with old data.
	 *
	 * @return array
	 */
	private function check_data( $new_data, $old_data ) {
		if ( ! isset( $new_data ) ) {
			return $old_data;
		} elseif ( empty( $new_data['content'] ) ) {
			return [
				'content'        => '',
				'disable_on_amp' => 0,
			];
		} else {
			return $new_data;
		}
	}

	/**
	 *  Save options to DB
	 */
	private function save_options() {
		update_option( 'code_inserter', $this->options );
	}

	/**
	 * Transform multi dimension object in multi dimension array
	 *
	 * @param object $object Object.
	 *
	 * @return array
	 */
	private function to_array( $object ) {
		if ( is_object( $object ) ) {
			$object = (array) $object;
		}
		if ( is_array( $object ) ) {
			$new_arr = [];
			foreach ( $object as $index => $value ) {
				$new_arr[ $index ] = self::to_array( $value );
			}
		} else {
			$new_arr = $object;
		}

		return $new_arr;
	}

}