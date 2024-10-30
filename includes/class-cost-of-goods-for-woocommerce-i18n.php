<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wpiron.com/
 * @since      1.0.0
 *
 * @package    Cost_Of_Goods_For_Woocommerce
 * @subpackage Cost_Of_Goods_For_Woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Cost_Of_Goods_For_Woocommerce
 * @subpackage Cost_Of_Goods_For_Woocommerce/includes
 * @author     WPiron <info@wpiron.com>
 */
class Cost_Of_Goods_For_Woocommerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cost-of-goods-for-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
