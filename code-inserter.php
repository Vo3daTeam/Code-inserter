<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              /
 * @since             1.0.0
 * @package           Code_Inserter
 *
 * @wordpress-plugin
 * Plugin Name:       Code inserter
 * Plugin URI:        /
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            VO3DA Team
 * Author URI:        vo3da.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       code-inserter
 * Domain Path:       code-inserter/core/languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use Code_Inserter\Core\Main;
use Code_Inserter\Core\Activator;
use Code_Inserter\Core\Deactivator;

require_once plugin_dir_path( __FILE__ ) . 'core/libs/vo3da-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VO3DA_CODE_INSERTER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
function activate_vo3da_code_inserter() {
	Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivation_vo3da_code_inserter() {
	Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vo3da_code_inserter' );
register_deactivation_hook( __FILE__, 'deactivation_vo3da_code_inserter' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

$main = new Main();
$main->init();
