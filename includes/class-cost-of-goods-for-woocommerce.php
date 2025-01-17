<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpiron.com/
 * @since      1.0.0
 *
 * @package    Cost_Of_Goods_For_Woocommerce
 * @subpackage Cost_Of_Goods_For_Woocommerce/includes
 */

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
 * @package    Cost_Of_Goods_For_Woocommerce
 * @subpackage Cost_Of_Goods_For_Woocommerce/includes
 * @author     WPiron <info@wpiron.com>
 */
class Cost_Of_Goods_For_Woocommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cost_Of_Goods_For_Woocommerce_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'COST_OF_GOODS_FOR_WOOCOMMERCE_VERSION' ) ) {
			$this->version = COST_OF_GOODS_FOR_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '1.1.1';
		}
		$this->plugin_name = 'cost-of-goods-for-wc';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cost_Of_Goods_For_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Cost_Of_Goods_For_Woocommerce_i18n. Defines internationalization functionality.
	 * - Cost_Of_Goods_For_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Cost_Of_Goods_For_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cost-of-goods-for-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cost-of-goods-for-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cost-of-goods-for-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cost-of-goods-for-woocommerce-public.php';

		$this->loader = new Cost_Of_Goods_For_Woocommerce_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cost_Of_Goods_For_Woocommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Cost_Of_Goods_For_Woocommerce_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Cost_Of_Goods_For_Woocommerce_Admin(
			$this->get_plugin_name(),
			$this->get_version()
		);

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'save_fields' );
		$this->loader->add_action(
			'woocommerce_product_options_general_product_data',
			$plugin_admin,
			'product_custom_fields'
		);

		$this->loader->add_filter(
			'plugin_action_links_cost-of-goods/cost-of-goods-for-woocommerce.php',
			$plugin_admin,
			'premium_link'
		);

		$this->loader->add_action( 'admin_init', $plugin_admin, 'wpiron_costofgoods_ignore_notice_wcmarkup' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'wpiron_costofgoods_admin_notice' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wc_markup_admin_menu_page' );
		$this->loader->add_filter('manage_edit-product_columns', $plugin_admin, 'custom_product_column_wpiron', 10, 1);
		$this->loader->add_action('manage_product_posts_custom_column', $plugin_admin, 'custom_product_column_content_wpiron', 10, 2);
		$this->loader->add_filter('manage_edit-product_sortable_columns', $plugin_admin, 'custom_product_column_sortable_wpiron');
		$this->loader->add_action('pre_get_posts', $plugin_admin, 'custom_product_column_orderby_wpiron');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Cost_Of_Goods_For_Woocommerce_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Cost_Of_Goods_For_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
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