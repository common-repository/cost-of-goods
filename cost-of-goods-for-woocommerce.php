<?php
/**
 * @link              https://wpiron.com/products/cost-of-goods-for-woocommerce/
 * @since             1.0.0
 * @package           Cost_Of_Goods
 *
 * @wordpress-plugin
 * Plugin Name:       Cost Of Goods For WooCommerce
 * Plugin URI:        https://wpiron.com
 * Description:       add your cost of goods to your products and track your profit in reports
 * Version:           1.6.5
 * Author:            WP Iron
 * Author URI:        https://wpiron.com/
 * Text Domain:       cost-of-goods
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('COST_OF_GOODS_FOR_WOOCOMMERCE_VERSION', '1.6.5');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cost-of-goods-for-woocommerce-activator.php
 */
function COGWPIRON_activate_cost_of_goods_for_woocommerce()
{
	if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true)) {
		require_once plugin_dir_path(__FILE__) . 'includes/class-cost-of-goods-for-woocommerce-activator.php';
		Cost_Of_Goods_For_Woocommerce_Activator::activate();
	} else{
		deactivate_plugins(plugin_basename(__FILE__));
		wp_die('Cost Of Goods For WooCommerce requires WooCommerce to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cost-of-goods-for-woocommerce-deactivator.php
 */
function COGWPIRON_deactivate_cost_of_goods_for_woocommerce()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-cost-of-goods-for-woocommerce-deactivator.php';
    Cost_Of_Goods_For_Woocommerce_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'COGWPIRON_activate_cost_of_goods_for_woocommerce');
register_deactivation_hook(__FILE__, 'COGWPIRON_deactivate_cost_of_goods_for_woocommerce');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-cost-of-goods-for-woocommerce.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cost_of_goods_for_woocommerce()
{
    $plugin = new Cost_Of_Goods_For_Woocommerce();
    $plugin->run();
}


run_cost_of_goods_for_woocommerce();
