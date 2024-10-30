<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpiron.com/
 * @since      1.0.0
 *
 * @package    Cost_Of_Goods_For_Woocommerce
 * @subpackage Cost_Of_Goods_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cost_Of_Goods_For_Woocommerce
 * @subpackage Cost_Of_Goods_For_Woocommerce/admin
 * @author     WPiron <info@wpiron.com>
 */
class Cost_Of_Goods_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cost_Of_Goods_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cost_Of_Goods_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/cost-of-goods-for-woocommerce-admin.min.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(  // jQuery
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/cost-of-goods-for-woocommerce-admin.min.js',
			array( 'jquery' ),
			$this->version,
			true
		);
	}

	public function save_fields( $postId ) {
		$product = wc_get_product( $postId );

		$costOfGoodPrice            = filter_var( $_POST['cost_of_goods'], FILTER_SANITIZE_STRING );
		$profitFinalPrice           = filter_var( $_POST['profit'], FILTER_SANITIZE_STRING );
		$rewriteRegularPriceChecked = isset( $_POST['rewrite_regular_price'] ) ? 'yes' : 'no';
		$regularMinusVat            = filter_var( $_POST['regular_price_vat'], FILTER_SANITIZE_STRING );

		$costOfGoodPriceFiltered = isset( $costOfGoodPrice ) ? $costOfGoodPrice : false;
		$profitPriceFiltered     = isset( $profitFinalPrice ) ? $profitFinalPrice : false;

		$error = false;

		if ( ! $costOfGoodPrice ) {
			$error      = true;
			$error_type = new \WP_Error(
				'cog_for_woocommerce_string_error',
				__( "Cost Of Good price is not correct", 'cost-of-goods-for-woocommerce' ),
				array( 'status' => 400 )
			);
		}

		if ( ! $profitFinalPrice ) {
			$error      = true;
			$error_type = new \WP_Error(
				'cog_for_woocommerce_number_error',
				__( "Cost Of Goods profit price is not correct", 'cost-of-goods-for-woocommerce' ),
				array( 'status' => 400 )
			);
		}

		if ( ! $error ) {
			$product->update_meta_data( 'cost_of_goods', sanitize_text_field( $costOfGoodPriceFiltered ) );
			$product->update_meta_data( 'profit', sanitize_text_field( $profitPriceFiltered ) );
			$product->update_meta_data( 'regular_price_vat', sanitize_text_field( $regularMinusVat ) );
			$product->update_meta_data( 'rewrite_regular_price', $rewriteRegularPriceChecked );
			$product->save();
		} else {
			return $error_type;
		}
	}

	public function product_custom_fields() {
		global $woocommerce, $post;
		echo '<div class="product_custom_field">';
		echo '<br/>';

		woocommerce_wp_text_input(
			array(
				'id'    => 'cost_of_goods',
				'label' => __( 'Cost Of Good', 'cost-of-goods-for-woocommerce' ),
				'value' => get_post_meta( $post->ID, 'cost_of_goods', true ),
			)
		);

		$rewrite_regular_price_checked = get_post_meta( $post->ID, 'rewrite_regular_price', true ) === 'yes';
		woocommerce_wp_checkbox( array(
			'id'    => 'rewrite_regular_price',
			'label' => __( 'Rewrite Regular Price?', 'cost-of-goods-for-woocommerce' ),
			'value' => $rewrite_regular_price_checked ? 'yes' : 'no',
			'checked' => $rewrite_regular_price_checked, // This ensures the checkbox is checked if needed
		) );

		// Determine if the Regular Price - VAT field should be disabled
		$regular_price_vat_disabled = $rewrite_regular_price_checked ? array() : array( 'disabled' => 'disabled' );
		woocommerce_wp_text_input(
			array(
				'id'                => 'regular_price_vat',
				'label'             => __( 'Regular Price - VAT', 'cost-of-goods-for-woocommerce' ),
				'custom_attributes' => $regular_price_vat_disabled,
				'value'             => get_post_meta( $post->ID, 'regular_price_vat', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => 'profit',
				'custom_attributes' => array( 'readonly' => 'readonly' ),
				'label'             => __( 'Profit', 'cost-of-goods-for-woocommerce' ),
				'desc_tip'          => 'true',
				'value'             => get_post_meta( $post->ID, 'profit', true ),
			)
		);

		echo '<script type="text/javascript">
        jQuery(document).ready(function($) {
            function toggleRegularPriceVatField() {
                if($("#rewrite_regular_price").is(":checked")) {
                    $("#regular_price_vat").removeAttr("disabled");
                } else {
                    $("#regular_price_vat").attr("disabled", "disabled");
                }
            }
            toggleRegularPriceVatField(); // Call on page load to set initial state

            $("#rewrite_regular_price").change(toggleRegularPriceVatField); // Bind change event handler
        });
    </script>';

		echo '</div>';
	}

	public function premium_link( $links ) {
		$url  = "https://wpiron.com/products/cost-of-goods-for-woocommerce/#pricing";
		$url2 = "admin.php?page=cost-of-goods-for-wc";

		$settings_link = "<a href='$url2' >" . __( 'Settings' ) . '</a> | ';
		$settings_link .= "<a href='$url' style='font-weight: bold; color: green;' target='_blank'>" . __( 'Get Premium' ) . '</a>';

		$links[] = $settings_link;

		return $links;
	}


	public function wc_markup_admin_menu_page() {
		add_menu_page(
			$this->plugin_name,
			'COG For WC',
			'administrator',
			$this->plugin_name,
			array( $this, 'displayPluginAdminDashboard' ),
			'dashicons-money',
			26
		);
	}

	public function custom_product_column_wpiron($columns) {
		$columns['cogs_column'] = __( 'Cost Of Goods', 'wpiron_cogs' );
		return $columns;
	}

	public function custom_product_column_content_wpiron($column, $postid) {
		if ( 'cogs_column' === $column ) {
			$custom_field_value = get_post_meta( $postid, 'cost_of_goods', true );
			if ( $custom_field_value ) {
				echo wc_price( $custom_field_value );
			} else {
				echo '-';
			}
		}
	}

	public function custom_product_column_sortable_wpiron($columns) {
		$columns['cogs_column'] = 'cogs_column';
		return $columns;
	}

	public function custom_product_column_orderby_wpiron($query) {
		if ( ! is_admin() )
			return;

		$orderby = $query->get( 'orderby' );

		if ( 'cogs_column' === $orderby ) {
			$query->set( 'meta_key', 'cost_of_goods' );
			$query->set( 'orderby', 'meta_value' );
		}
	}

	public function displayPluginAdminDashboard() {
		require_once 'partials/' . $this->plugin_name . '-admin-display.php';
	}

	public function displayPluginAdminSettings() {
		$tab = filter_var( $_GET['tab'], FILTER_SANITIZE_STRING );

		$active_tab = $tab ?? 'general';
		if ( isset( $_GET['error_message'] ) ) {
			add_action( 'admin_notices', array( $this, 'pluginNameSettingsMessages' ) );
			do_action( 'admin_notices', $_GET['error_message'] );
		}
		require_once 'partials/' . $this->plugin_name . '-admin-settings-display.php';
	}

	function wpiron_costofgoods_admin_notice() {
		global $current_user;

		$siteUrl      = site_url();
		$uniqueUserId = md5( $siteUrl );

		$api_url = 'https://uwozfs6rgi.execute-api.us-east-1.amazonaws.com/prod/notifications';
		$body    = wp_json_encode( [
			'pluginName' => 'wpiron-wc-cog-free',
			'status'     => true,
			'user_id'    => $uniqueUserId
		], JSON_THROW_ON_ERROR );

		$args = [
			'body'        => $body,
			'headers'     => [
				'Content-Type' => 'application/json',
			],
			'method'      => 'POST',
			'data_format' => 'body',
		];

		$response = wp_remote_post( $api_url, $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();

			return;
		}

		$body        = wp_remote_retrieve_body( $response );
		$data        = json_decode( $body, true, 512 );
		$status_code = $data['statusCode'];

		if ( ! empty( $data ) && $status_code === 200 && $data['body'] !== '[]' ) {
			$dataEncoded = json_decode( $data['body'], true )[0];
			if ( $dataEncoded['content'] && $dataEncoded['dismissed'] === false ) {
				$content    = $dataEncoded['content'];
				$message_id = $dataEncoded['message_id']; // Get the message ID

				?>
                <div class="notice notice-success is-dismissible">
					<?php
					echo $content; ?>
                    <hr>
                    <a style="margin-bottom: 10px; position: relative; display: block;"
                       href="?cost_of_goods_-notice&message_id=<?php
					   echo urlencode( $message_id ); ?>"><b>Dismiss this notice</b></a>
                </div>
				<?php
			}
		}
	}

	public function wpiron_costofgoods_ignore_notice_wcmarkup() {
		global $current_user;

		$siteUrl      = site_url();
		$uniqueUserId = md5( $siteUrl );

		if ( isset( $_GET['cost_of_goods_-notice'] ) ) {
			$message_id     = $_GET['message_id'];
			$apiRequestBody = wp_json_encode( array(
				'user_id'     => $uniqueUserId,
				'plugin_name' => 'wpiron-wc-cog-free',
				'message_id'  => $message_id,
			) );

			$apiResponse = wp_remote_post(
				'https://uwozfs6rgi.execute-api.us-east-1.amazonaws.com/prod/notifications',
				array(
					'body'    => $apiRequestBody,
					'headers' => array(
						'Content-Type' => 'application/json',
					),
				)
			);

			if ( is_wp_error( $apiResponse ) ) {
				$error_message = $apiResponse->get_error_message();

				return;
			}
		}
	}

}