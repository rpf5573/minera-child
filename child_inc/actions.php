<?php
/**
 * default setup
 *
 * @return void
 */
function rpf_default_setup() {
  global $wpdb;

  // add image size
  add_image_size( 'widget_product_thumbnail', 70, 70, true );

  /*Make Theme available for translation*/
  load_theme_textdomain( 'minera', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'rpf_default_setup', 11 );

/**
 * enqueue scripts
 *
 * @return void
 */
function rpf_enqueue_css_js() {
  //wp_dequeue_style( 'minera-theme-style' );
  wp_enqueue_style( 'parent_theme', get_template_directory_uri().'/style.css', array(), false, 'all' );
  //wp_enqueue_style( 'rpf-main', get_stylesheet_directory_uri().'/style.css', array(), '0.001', 'all' );
  wp_enqueue_style( 'balloon', get_stylesheet_directory_uri().'/assets/library/balloon.css', array(), '0.001', 'all' );
  // wp_enqueue_style( 'load-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '0.001', 'all' );
  //wp_enqueue_style( 'addressy-example', 'http://api.addressy.com/css/examples-1.00.min.css', array(), '0.001', 'all' );
  //wp_enqueue_style( 'addressy', 'http://api.addressy.com/css/address-3.50.css', array(), '0.001', 'all' );

  // wp_dequeue_script( 'minera-theme-script' ); // dequeue minera-theme-script because of customizing
  // wp_enqueue_script( 'minera-theme-script', get_stylesheet_directory_uri().'/assets/custom/js/minera_custom.js', array('jquery'), true );

  wp_enqueue_script( 'rpf-functions', get_stylesheet_directory_uri().'/assets/custom/js/functions.js', array('jquery'), true );
  wp_enqueue_script( 'sticky', get_stylesheet_directory_uri().'/assets/library/sticky/jquery.sticky.js', array('jquery'), true );

  if ( is_account_page() || is_checkout() ) {
    wp_enqueue_script( 'postcode', get_stylesheet_directory_uri().'/assets/library/daum_postcode.js', array(), null, true );
    wp_enqueue_script( 'clicktoaddress', 'https://cc-cdn.com/generic/scripts/v1/cc_c2a.min.js', array(), null, false );
  }

  if ( is_account_page() ) {
    rpf_enqueue_intl_tel_input();
    wp_enqueue_script( 'rpf-account', get_stylesheet_directory_uri().'/assets/custom/js/account.js', array('jquery', 'intl_tel_input_utils'), true );
    wp_localize_script( 'rpf-account', 'ajaxData', array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) );
  }

  if ( is_cart() ) {
    rpf_enqueue_nice_select();
    wp_enqueue_script( 'rpf-cart', get_stylesheet_directory_uri().'/assets/custom/js/cart.js', array('jquery', 'rpf-functions', 'nice_select'), true );
  }

  if ( is_checkout() ) {
    rpf_enqueue_nice_select();
    rpf_enqueue_intl_tel_input();
    wp_enqueue_script( 'rpf-checkout', get_stylesheet_directory_uri().'/assets/custom/js/checkout.js', array('jquery', 'rpf-functions', 'intl_tel_input_utils', 'nice_select'), true );
    wp_localize_script('rpf-checkout', 'ajaxData', array(
      'url'   => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce( 'ajax-nonce_send_point' )
    ));
  }

  wp_enqueue_script( 'rpf-main', get_stylesheet_directory_uri().'/assets/custom/js/main.js', array('jquery'), '0.001', true );

}
add_action( 'wp_enqueue_scripts', 'rpf_enqueue_css_js', 1 );

/**
 * Enqueue scripts on admin
 *
 * @return void
 */
function rpf_enqueue_scripts_on_admin() {
  wp_enqueue_script( 'jquery' );
}
add_action( 'admin_enqueue_scripts', 'rpf_enqueue_scripts_on_admin' );

/**
 * override parent theme's settings
 *
 * @return void
 */
function rpf_override_parent_theme_settings() {
  // remove post-format theme support
  remove_theme_support( 'post-formats' );
}
add_action( 'after_setup_theme', 'rpf_override_parent_theme_settings', 12 ); // more high than parent

/**
 * override plugin features - SMS, Point & Reward
 *
 * @return void
 */
function rpf_override_plugin_features() {
  global $wc_settings_reputesms;
  global $mwb_wpr_front_end;

  // move sms update checkbox from under order note to billing forms
  if ( !is_null($wc_settings_reputesms) ) {
    remove_action( 'woocommerce_after_order_notes', array( $wc_settings_reputesms, 'repute_sms_checkout_fields' ) );
    add_action( 'woocommerce_after_checkout_billing_form', array( $wc_settings_reputesms, 'repute_sms_checkout_fields' ) );
  }
  if ( !is_null($mwb_wpr_front_end) ) {
    remove_action('woocommerce_single_product_summary', array($mwb_wpr_front_end,'mwb_display_product_points'),7);
    remove_action( 'woocommerce_before_add_to_cart_button', array($mwb_wpr_front_end, "mwb_wpr_woocommerce_before_add_to_cart_button"), 10);
  }
}
add_action( 'after_setup_theme', 'rpf_override_plugin_features', 12 ); // more high than parent

/**
 * return address template on myaccount page
 *
 * @return void
 */
function rpf_get_address_template_on_myaccount() {
  ob_start();
  if ( ! empty($_POST['country']) ) { ?>
    <div class="hello" style="width: 400px; height: 400px; background-color:yellow;"></div>
  <?php
    $data = ob_get_clean();
    wp_send_json_success( $data );
  } else {
    wp_send_json_error('country is empty');
  }
}
add_action( 'wp_ajax_rpf_get_address_template_on_myaccount', 'rpf_get_address_template_on_myaccount' );

/**
 * add customer number on admin order page
 *
 * @param [type] $order
 * @return void
 */
function rpf_add_custom_number_display_admin_order_meta($order){
  $custom_number = rpf_get_custom_number($order);
  if ( $custom_number ) {
    echo sprintf( esc_html__("<p>Customs ID Number : %s</p>", 'minera'), $custom_number );
  }
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'rpf_add_custom_number_display_admin_order_meta', 10, 1 );


/**
 * add customer number on order detail page
 *
 * @param [type] $order
 * @return void
 */
function rpf_add_custom_number_display_order_details($order) {
  $custom_number = rpf_get_custom_number($order);
  if ( $custom_number ) {
    echo sprintf( esc_html__("<strong>Customs ID Number</strong> : %s", 'minera'), $custom_number );
  }
}
add_action('woocommerce_order_items_table', 'rpf_add_custom_number_display_order_details', 20, 1);

/**
 * add customer number on email
 *
 * @param [type] $order
 * @return void
 */
function rpf_add_custom_number_display_emails($order) {
  $custom_number = rpf_get_custom_number($order);
  if ( $custom_number ) {
    echo sprintf( esc_html__("<strong>Customs ID Number</strong> : %s", 'minera'), $custom_number );
  }
}
add_action( 'woocommerce_email_customer_details', 'rpf_add_custom_number_display_emails', 11, 4 );

/**
 * add point buy field
 *
 * @param [type] $product
 * @return void
 */
function rpf_add_point_buy_field($product) {
  global $mwb_wpr_front_end; 
  if ( !isset($mwb_wpr_front_end) ) { return; }

  $mwb_wpr_front_end->mwb_wpr_woocommerce_before_add_to_cart_button($product);
}
add_action( 'queensmallo_woocommerce_before_quantity', 'rpf_add_point_buy_field', 10, 1 );

function rpf_add_point_input_box_on_checkout($wccm_autocreate_account) { 
  global $mwb_wpr_front_end;
  if ( ! isset($mwb_wpr_front_end) ) {return;} 
  $point = (int)get_user_meta(get_current_user_id(), 'mwb_wpr_points', true);
  $rate_dollar = rpf_get_mwb_exchange_rate() . get_woocommerce_currency_symbol();
  if ( $point > 0 ) { ?>
    <div class="woocommerce-info"> <?php _e('Do you want use your point?', 'minera'); ?> <a class="showpoint"> <?php _e('Click here to enter your point', 'minera'); ?> </a></div>
    <div class="rpf-discount_point">
      <p class="rpf-discount_point__info"> <?php
        $point = (int)get_user_meta(get_current_user_id(), 'mwb_wpr_points', true);
        echo sprintf(__('Your current point is %s. How many points will you use?', 'minera'), $point.__('points', 'minera')) . ' (1' . __('point', 'minera') . ' => ' . $rate_dollar . ')'; ?>
        <?php wp_nonce_field( 'send_discount_point', 'rpf_nonce' ); ?>
      </p>
      <div class="rpf-discount_point__apply">
        <label for="rpf-discount_point__apply__input"> <?php echo __('Point', 'minera') . ' : '; ?> </label>
        <input type="text" id="rpf-discount_point__apply__input" class="rpf-discount_point__apply__input" autocomplete="off">
        <input type="button" class="rpf-discount_point__apply__btn" value="<?php _e('APPLY', 'minera'); ?>">
      </div>
    </div> <?php 
  } ?>
<?php
}
add_action( 'woocommerce_before_checkout_form', 'rpf_add_point_input_box_on_checkout', 1 );

/**
 * save discount point to session
 *
 * @return void
 */
function rpf_save_discount_point_to_session() {
  global $mwb_wpr_front_end; 
  if ( !isset($mwb_wpr_front_end) ) { return; }

  if ( isset($_POST['discount_point']) && defined( 'DOING_AJAX' ) ) {
    $discount_point = (int)$_POST['discount_point'];
    $get_points = (int)get_user_meta(get_current_user_id(), 'mwb_wpr_points', true);
    if ( check_ajax_referer( 'send_discount_point', 'rpf_nonce' ) ) {
      if ( $get_points < $discount_point ) {
        WC()->session->set( 'discount_point' , null );
        wp_send_json_error( 'Sorry, you do not have enough points' );
      }
      WC()->session->set( 'discount_point' , $discount_point );
      wp_send_json_success();
    }
  }
  wp_send_json_error();
}
add_action( 'wp_ajax_rpf_save_discount_point_to_session', 'rpf_save_discount_point_to_session' );

/**
 * add_calculate_fees
 *
 * @param [type] $cart
 * @return void
 */
function rpf_add_calculate_fees($cart) {
  global $mwb_wpr_front_end;
  if ( is_admin() || !isset($mwb_wpr_front_end) || !is_checkout() ) return;

  $discount_point = (int)WC()->session->get( 'discount_point' );
  $get_points = (int)get_user_meta(get_current_user_id(), 'mwb_wpr_points', true);
  if ( $discount_point && ($get_points >= $discount_point) ) {
    // apply exchange rate
    $rate = rpf_get_mwb_exchange_rate();
    $discount_price = $rate*$discount_point;
    $cart->add_fee(__('Point Discount', 'minera'), -$discount_price);
  }
}
add_action( 'woocommerce_cart_calculate_fees', 'rpf_add_calculate_fees');


function rpf_add_weight_fee($cart) {
  // RPF_Helper::log( $cart );
  if ( is_plugin_active( 'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php' ) && (is_cart() || is_checkout()) ) {
    $weight = $cart->get_cart_contents_weight();
    $rate_cost_per_weight_unit = rpf_get_rate_cost_per_weight_unit();
    $rate_cost_per_weight_unit_as_string = get_woocommerce_currency_symbol().$rate_cost_per_weight_unit;
    if ( $rate_cost_per_weight_unit > 0 ) {
      // delete because of duplicate
      // $cart->add_fee(__('Weight Price', 'minera')." ({$rate_cost_per_weight_unit_as_string} per kg)" , $rate_cost_per_weight_unit * $weight );
    }
  }
}
add_action( 'woocommerce_cart_calculate_fees', 'rpf_add_weight_fee');

/**
 * unset discount point on checkout page's header
 *
 * @return void
 */
function rpf_unset_discount_point() {
  global $mwb_wpr_front_end; 
  if ( !isset($mwb_wpr_front_end) ) { return; }

  if ( class_exists('WooCommerce') && !is_admin() && is_checkout() ) {
    WC()->session->set('discount_point', null);
  }
}
add_action( 'wp_head', 'rpf_unset_discount_point');

/**
 * remove from the user's pocket as many points as the user has used.
 *
 * @param [string] $order_get_id
 * @return void
 */
function rpf_reflect_discount_point_on_user($order_id) {
  global $mwb_wpr_front_end; 
  if ( !isset($mwb_wpr_front_end) ) { return; }

  $discount_point = WC()->session->get('discount_point');
  if ( !is_null($discount_point) ) {
    $order = wc_get_order( $order_id );
    $customer_id = $order->get_customer_id();
    $point_log = get_user_meta( $customer_id, 'points_details', true);
    $point_log['pur_by_points'][] = array(
      'pur_by_points' => $discount_point,
      'date'          => $today_date = date_i18n("Y-m-d h:i:sa")
    );
    update_user_meta( $customer_id, 'points_details', $point_log );
    $get_points = (int)get_user_meta(get_current_user_id(), 'mwb_wpr_points', true);
    $new_point = $get_points - $discount_point;
    update_user_meta( $customer_id, 'mwb_wpr_points', $new_point );
    WC()->session->set('discount_point', null);
  }
}
add_action('woocommerce_payment_complete', 'rpf_reflect_discount_point_on_user', 1, 1);

/**
 * Validate if cart has Wild Card Product
 *
 * @return void
 */
function rpf_validate_product_before_add_to_cart() {
  $cart = WC()->cart->get_cart();
  if ( count($cart) > 1 ) {
    foreach( $cart as $cart_item ){
      $product_id = $cart_item['product_id'];
      if ( get_field( "wild_card", $product_id ) ) {
        WC()->cart->empty_cart();
        WC()->cart->add_to_cart( $product_id, $cart_item['quantity'] );
        $message = "wild cart product should be purchased independently";
        echo '<div class="global_shipping_error_message" style="font-size:1.4rem; line-height:1.4rem;">';
          wc_print_notice( __( 'Global Shipping Product should be purchased independently', 'minera' ), 'error' );
        echo '</div>';
        die();
      }
    }
  }
}
add_action( 'woocommerce_before_checkout_form', 'rpf_validate_product_before_add_to_cart', 0 );

/**
 * Change admin login logo
 *
 * @return void
 */
function rpf_custom_loginlogo() {
  $c_lg = get_theme_mod('logo_img', '');
  if ( $c_lg ) {
    echo '<style type="text/css"> .login h1 a {background-image: url(' . $c_lg . ') !important; min-height:100px !important; background-size:auto !important; width:100% !important; } </style>';
  }
}
add_action('login_head', 'rpf_custom_loginlogo');