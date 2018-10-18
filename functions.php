<?php
/**
 * Enable ACF 5 early access
 * Requires at least version ACF 4.4.12 to work
 */
define('ACF_EARLY_ACCESS', '5');

RPF_Helper::log( "Hello World !" );

die();

require_once( 'child_inc/helper.php' );
require_once( 'child_inc/shortcodes.php' );
require_once( 'child_inc/actions.php' );
require_once( 'child_inc/filters.php' );
require_once( 'child_inc/user_avatar.php' );
require_once( 'child_inc/user_nick_name.php' );
if ( class_exists( 'WPBakeryVisualComposerAbstract' ) ) {
  function rpf_require_VC() {
    get_template_part('child_inc/vc', 'include');
  }
  add_action( 'init', 'rpf_require_VC', 2 );
}
// check if WPML works
if ( ! isset($wpml_active) ) {
  $wpml_active = function_exists( 'icl_object_id' );
}


/* ------------------------------------------------------------------------- *
 * Functions
/* ------------------------------------------------------------------------- */

/**
 * show price ( cart weight * weight price per 1kg )
 *
 * @param [float] $rate_cost_per_weight_unit
 * @return
 */
function rpf_wc_cart_totals_weight_price_html( $rate_cost_per_weight_unit ) {
  echo rpf_get_cart_weight_price( $rate_cost_per_weight_unit );
}

/**
 * get cart weight price ( cart weight * weight price per 1kg )
 *
 * @param [float] $rate_cost_per_weight_unit
 * @return html
 */
function rpf_get_cart_weight_price( $rate_cost_per_weight_unit ) {
  $contents_weight = WC()->cart->get_cart_contents_weight();
  //$role = wc_price( (float)$rate_cost_per_weight_unit ) . ' per kg';
  $weight_price = wc_price( (float)$contents_weight * (float)$rate_cost_per_weight_unit );

  return $weight_price;
}

/**
 * get rate cost per 1kg
 * this cost determined by manager
 *
 * @return void
 */
function rpf_get_rate_cost_per_weight_unit() {
  global $wpdb;
  $rate_cost_per_weight_unit = 0;

  $packages = WC()->cart->get_shipping_packages(); // asume that this is only one package
  $zone = WC_Shipping_Zones::get_zone_matching_package( $packages[0] );
  $zone_id = $zone->get_id();

  // tr -> wp_woocommerce_shipping_table_rates
  // m  -> wp_woocommerce_shipping_zone_methods
  $results = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT tr.rate_cost_per_weight_unit
      FROM {$wpdb->prefix}woocommerce_shipping_zone_methods AS m , {$wpdb->prefix}woocommerce_shipping_table_rates AS tr
      WHERE m.zone_id = %d AND m.is_enabled = 1 AND tr.rate_condition = 'weight'", $zone_id
    )
  );

  if ( ! empty($results) && is_array($results) ) {
    $rate_cost_per_weight_unit = $results[0]->rate_cost_per_weight_unit;
  }

  return $rate_cost_per_weight_unit;
}

/**
 * get customer's orders page url in my-account page
 *
 * @return url
 */
function rpf_get_customer_orders_page_url() {
  $orders_endpoint = get_option( 'woocommerce_myaccount_orders_endpoint', 'orders' );
  $orders_page_url = get_permalink( get_option('woocommerce_myaccount_page_id') ) . '/' . $orders_endpoint;
  return $orders_page_url;
}

/**
 * enqueue intl tel input library
 *
 * @return void
 */
function rpf_enqueue_intl_tel_input() {
  // intl tel input library
  wp_enqueue_style( 'intl_tel_input', get_stylesheet_directory_uri().'/assets/library/intl-tel-input/css/intlTelInput.css', '0.001', false );
  //wp_enqueue_style( 'intl_tel_input_theme', get_stylesheet_directory_uri().'/assets/library/intl-tel-input/css/demo.css', '0.001', false );
  wp_enqueue_script( 'intl_tel_input_utils', get_stylesheet_directory_uri().'/assets/library/intl-tel-input/js/utils.js', array('jquery'), true );
  wp_enqueue_script( 'intl_tel_input', get_stylesheet_directory_uri().'/assets/library/intl-tel-input/js/intlTelInput.js', array('jquery', 'intl_tel_input_utils'), true );
}

/**
 * enqueue nice select library
 *
 * @return void
 */
function rpf_enqueue_nice_select() {
  wp_enqueue_style( 'nice_select', get_stylesheet_directory_uri().'/assets/library/nice_select/nice-select.css', '0.001', false );
  wp_enqueue_script( 'nice_select', get_stylesheet_directory_uri().'/assets/library/nice_select/jquery.nice-select.js', array('jquery'), true );
}

/**
 * get exchage rate between point and dollar
 *
 * @return void
 */
function rpf_get_mwb_exchange_rate() {
  $general_settings = get_option('mwb_wpr_settings_gallery',true);
  $purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
  $product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
  $rate = $product_purchase_price/$purchase_points;
  return $rate;
}

/**
 * get custom number from order
 *
 * @param [type] $order
 * @return void
 */
function rpf_get_custom_number(&$order) {
  $billing_custom_number = get_post_meta( $order->id, '_billing_custom_number', true );
  $shipping_custom_number = get_post_meta( $order->id, '_shipping_custom_number', true );
  $custom_number = ($shipping_custom_number ? $shipping_custom_number : $billing_custom_number);
  if ( $custom_number && (strlen($custom_number) > 3) ) {
    return $custom_number;
  }
  return false;
}