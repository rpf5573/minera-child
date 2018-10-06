<?php

/* ------------------------------------------------------------------------- *
 *  No use anymore but valuable
/* ------------------------------------------------------------------------- */

/**
 * Add price field on user request product type's price tab
 *
 * @return void
 */
function rpf_add_user_request_price_tab_content() { ?>
  <div class="panel woocommerce_options_panel" id="the_price">
    <div class="options_group"> <?php
      woocommerce_wp_text_input( array(
        'id'          => 'user_request_product_price',
        'label'       => 'Price',
        'placeholder' => '',
        'desc_tip'    => 'true',
        'description' => 'Enter product price'
      ) ); ?>
    </div>
  </div>
<?php
}
//add_action( 'woocommerce_product_data_panels', 'rpf_add_user_request_price_tab_content' );

/**
 * Save the user request fields
 *
 * @param [type] $post_id
 * @return void
 */
function rpf_save_user_request_fields($post_id) {
  $price = isset( $_POST['user_request_product_price'] ) ? $_POST['user_request_product_price'] : 'no';
  update_post_meta( $post_id, 'user_request_product_price', $price );
}
//add_action( 'woocommerce_process_product_meta_user_request', 'rpf_save_user_request_fields'  );

/**
 * Add my custom product type to product type list
 *
 * @param [array] $types
 * @return void
 */
function rpf_add_user_request_product_type($types) {
  $types['user_request'] = __('User Request');

  return $types;
}
//add_filter( 'product_type_selector', 'rpf_add_user_request_product_type' );

/**
 * Add price tab on user request product type
 *
 * @param [type] $tabs
 * @return void
 */
function rpf_add_price_tab_related_with_user_request_product_type($tabs) {

  // make price to be top
  $my_tabs = array();
  $my_tabs['user_request'] = array(
    'label'     => 'Price',
    'target'    => 'the_price',
    'class'     => 'show_if_user_request',
    'priority'  => 1
  );
  $tabs['general']['class'][] = 'hide_if_user_request';
  $tabs['inventory']['class'][] = 'hide_if_user_request';
  $tabs['linked_product']['class'][] = 'hide_if_user_request';
  $tabs['attribute']['class'][] = 'hide_if_user_request';
  $tabs['variations']['class'][] = 'hide_if_user_request';
  $tabs['advanced']['class'][] = 'hide_if_user_request';
  $tabs['points']['class'][] = 'hide_if_user_request';

  return array_merge( $my_tabs, $tabs );
}
//add_filter( 'woocommerce_product_data_tabs', 'rpf_add_price_tab_related_with_user_request_product_type' );

/**
 * Register product type class on air
 *
 * @return void
 */
function rpf_register_user_request_product_type() {
  class WC_Product_User_Request extends WC_Product_Simple {
    public function __construct($product) {
      $this->data['user_request'] = false;
      parent::__construct($product);
    }
    /**
     * Set if the product is user request.
     *
     * @since 3.0.0
     * @param bool|string
     */
    public function set_user_request( $user_request ) {
      $this->set_prop( 'user_request', wc_string_to_bool( $user_request ) );
    }
    /**
     * Get user request.
     *
     * @since 3.0.0
     * @param  string $context
     * @return bool
     */
    public function get_user_request( $context = 'view' ) {
      return $this->get_prop( 'user_request', $context );
    }
    /**
     * Checks if a product is downloadable.
     *
     * @return bool
     */
    public function is_user_request() {
      return apply_filters( 'woocommerce_is_user_request', true === $this->get_user_request(), $this );
    }
    /**
     * Set a collection of props in one go, collect any errors, and return the result.
     * Only sets using public methods.
     *
     * @since  3.0.0
     *
     * @param  array $props Key value pairs to set. Key is the prop and should map to a setter function name.
     * @param string $context
     *
     * @return bool|WP_Error
     */
    public function set_props( $props, $context = 'set' ) {
      $props['user_request'] = isset( $_POST['_user_request'] );
      return parent::set_props($props, $context);
    }
  }
}
// add_action( 'init', 'rpf_register_user_request_product_type' );

/**
 * Undocumented function
 *
 * @param [type] $self
 * @param [type] $product_type
 * @param [type] $var
 * @param [type] $product_id
 * @return void
 */
function rpf_add_user_request_product_type_option_before_save($self, $product_type, $var, $product_id) {
  if ( $_POST['_user_request'] ) {
    return 'WC_Product_User_Request';
  }
  return $product_type;
}
add_filter( 'woocommerce_product_class', 'rpf_add_user_request_product_type_option_before_save', 100, 4 );


/* ------ called before woo save user input data ------ */
$type = 'shipping';
if ( isset($_POST['billing_first_name']) ) {
  $type = 'billing';
}
if ( $_POST['action'] == 'edit_address' && $_POST["save_address"] == 'Save address' ) {
  if ( $_POST["{$type}_country"] == 'KR' ) {
  }
  else if ( $_POST["{$type}_country"] == 'AU' || $_POST["{$type}_country"] == 'AU' ) {}
}

/**
 * add wild card endpoint to myaccount page
 *
 * @param [array] $items
 * @return void
 */
function rpf_add_wild_card_tab_to_myaccount($items) {
  $items['wild_card'] = 'Wild Card';
  return $items;
}
// add_filter( 'woocommerce_account_menu_items', 'rpf_add_wild_card_tab_to_myaccount', 10, 1 );

/**
 * add wild card rewrite endpoint
 *
 * @return void
 */
function rpf_add_wild_card_rewrite_endpoint() {
  add_rewrite_endpoint( 'wild_card', EP_ROOT | EP_PAGES );
}
// add_action( 'init', 'rpf_add_wild_card_rewrite_endpoint' );

/**
 * show content on wild card endpoint(tab)
 *
 * @return void
 */
function rpf_wild_card_content() {
  wc_get_template( 'myaccount/wild_card.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) );
}
// add_action( 'woocommerce_account_wild_card_endpoint', 'rpf_wild_card_content' );

/**
 * add user request product type option when product save
 *
 * @param [WC_Product] $product
 * @return void
 */
function rpf_add_wild_card_type_option_before_product_save($product, $data_store) {
  $product_id = $product->get_id();
  $is_wild_card = ( isset($_POST['_wild_card']) ) ? 'yes' : 'no';
  update_post_meta( $product_id, '_wild_card', $is_wild_card );
}
//add_action( 'woocommerce_before_product_object_save', 'rpf_add_wild_card_type_option_before_product_save', 100, 2 );

/**
 * when query product, never get user request product
 *
 * @param [WP_Query] $query
 * @return void
 */
function rpf_add_query_var_not_to_show_wild_card_product($query) {
  $post_type = $query->get( 'post_type' );
  if ( ! is_admin() && ! is_null($post_type) && $post_type == 'product' ) {
    $query->set( 'meta_key', '_wild_card' );
    $query->set( 'meta_value', 'no' );
    $query->set( 'meta_compare', '=' ); // default
  }
}
add_action( 'pre_get_posts', 'rpf_add_query_var_not_to_show_wild_card_product', 100, 1 );

/**
 * add user request product type option to `add new product` page
 *
 * @param [array] $type_options
 * @return void
 */
function rpf_add_wild_card_product_type_option($type_options) {
  $checked = 'no';
  // check is edit
  // if ( isset($_GET['post']) && isset($_GET['action']) && ($_GET['action'] == 'edit') ) {
  //   $product_id = (int)$_GET['post'];
  //   $is_wild_card = get_post_meta( $product_id, '_wild_card', true );
  //   if ( $is_wild_card == 'yes' ) {
  //     $checked = 'yes';
  //   }
  // }
  $type_options['wild_card'] = array(
    'id'            => '_wild_card',
    'wrapper_class' => 'show_if_simple',
    'label'         => __( 'Wild Card', 'woocommerce' ),
    'description'   => __( 'Please check if this product is wild card', 'woocommerce' ),
    'default'       => $checked
  );
  return $type_options;
}
add_filter( 'product_type_options', 'rpf_add_wild_card_product_type_option', 100, 1 );

function rpf_create_custom_product_type_class(){
  // declare the product class
  class WC_Queens_Product extends WC_Simple_Product{
    public function __construct( $product ) {
      $this->product_type = 'queens_product';
      parent::__construct( $product );
      // add additional functions here
    }
  }
}
add_action( 'plugins_loaded', 'rpf_create_custom_product_type_class' );

// add a product type
function rpf_add_custom_product_type( $types ){
  $types[ 'queens_product' ] = __( 'Queens Product' );
  return $types;
}
add_filter( 'product_type_selector', 'rpf_add_custom_product_type' );

function rpf_localize_related_ids($meta_value, $custom_filed_key) {
  echo $custom_filed_key;
  return $meta_value;
}
add_filter( 'wcml_meta_value_before_add', 'rpf_localize_related_ids', 100, 2 );

function my_custom_checkout_field_process() {
  // Check if set, if its not set add an error.
  if ( ! $_POST['my_field_name'] )
    wc_add_notice( __( 'Please enter something into this new shiny field.' ), 'error' );
}
//add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

/**
 * add discount point on mini cart
 *
 * @param [type] $item_meta
 * @param [type] $key
 * @param [type] $val
 * @return void
 */
function rpf_add_discount_point_on_mini_cart($item_meta, $cart_item) {
  global $mwb_wpr_front_end;
  if ( isset($mwb_wpr_front_end) && !is_cart() && isset($cart_item['product_meta']['meta_data']['pro_purchase_by_points']) ) { // mini cart check
    $general_settings = get_option('mwb_wpr_settings_gallery',true);
    $purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
    $product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
    $rate = $product_purchase_price/$purchase_points;
    $pro_purchase_by_points = $cart_item['product_meta']['meta_data']['pro_purchase_by_points'];
    $discount_price = $rate*$pro_purchase_by_points;
    $discount_price = get_woocommerce_currency_symbol() . $discount_price;
    $item_meta[] = array(
      'name'   =>  __('Discount', 'minera'),
      'value' => '-' . $discount_price,
    );
  }
  return $item_meta;
}
// add_filter( 'woocommerce_get_item_data', 'rpf_add_discount_point_on_mini_cart', 20, 2 );

function rpf_calculate_mini_cart_subtotal_with_discount_point($cart_subtotal, $compound, $cart) {
  if ( !is_cart() ) {
    $general_settings = get_option('mwb_wpr_settings_gallery',true);
    $purchase_points = (isset($general_settings['mwb_wpr_purchase_points']) && $general_settings['mwb_wpr_purchase_points'] != null) ? $general_settings['mwb_wpr_purchase_points'] : 1;
    $product_purchase_price = (isset($general_settings['mwb_wpr_product_purchase_price']) && $general_settings['mwb_wpr_product_purchase_price'] != null) ? intval($general_settings['mwb_wpr_product_purchase_price']) : 1;
    $rate = $product_purchase_price/$purchase_points;
    $total_price = $cart->get_cart_contents_total();
    $total_discount_price = 0;
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
      $pro_purchase_by_points = $cart_item['product_meta']['meta_data']['pro_purchase_by_points'];
      $discount_price = $rate*$pro_purchase_by_points;
      $total_discount_price += $cart_item['quantity'] * $discount_price;
    }

    $discounted_total_price = $total_price - $total_discount_price;
    $discounted_total_price = wc_price( $discounted_total_price );
    $cart_subtotal = sprintf("<span><del>%s</del> <ins>%s</ins></span>", $cart_subtotal, $discounted_total_price);
  }
  return $cart_subtotal;
}
// add_filter( 'woocommerce_cart_subtotal', 'rpf_calculate_mini_cart_subtotal_with_discount_point', 100, 3 );

function rpf_woocommerce_thankyou($order_id) {
  $order = wc_get_order( $order_id );
  $customer_id = $order->get_customer_id();
}
add_action( 'woocommerce_thankyou', 'rpf_woocommerce_thankyou', 10, 1 );


<?php if ( $GLOBALS['is_table_rate_shipping_active'] && $rate_cost_per_weight_unit > 0 ) : $rate_cost_per_weight_unit_as_string = wc_price($rate_cost_per_weight_unit); ?>
<tr class="cart-weight_price">
  <th><?php _e( "Weight Price ({$rate_cost_per_weight_unit_as_string} per kg)", 'woocommerce' ); ?></th>
  <td data-title="<?php esc_attr_e( 'Weight Price', 'woocommerce' ); ?>"><?php rpf_wc_cart_totals_weight_price_html( $rate_cost_per_weight_unit ); ?></td>
</tr>
<?php endif; ?>

// for checking woocommerce-table-rate-shpping plugin is active
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$GLOBALS['is_table_rate_shipping_active'] = false;
$rate_cost_per_weight_unit = 0;
if ( is_plugin_active( 'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php' ) ) {
	$GLOBALS['is_table_rate_shipping_active'] = true;
	$rate_cost_per_weight_unit = rpf_get_rate_cost_per_weight_unit();
}

<?php
  // default - pick up
  $tip = 'This is pick up help tip';
  // standard
  if ( $number == 2 ) {
    $tip = 'This is standard help tip';
  } else if ( $number == 3 ) {
    $tip = 'This is fast help tip';
  }
?>
<option data-tip="<?php echo $tip; ?>" value="<?php echo esc_attr($method->id); ?>" <?php selected($method->id, $chosen_method); ?>><?php echo wp_kses_post(wc_cart_totals_shipping_method_label($method)); ?></option>