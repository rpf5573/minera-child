<?php
/**
 * (the_content) Add rating to content's bottom
 *
 * @param [string] $content
 * @return html
 */
function rpf_add_rating_to_post_bottom( $content ) {
  
  // show post-ratings on blog only
  if ( RPF_Helper::is_blog() ) {
    if ( function_exists('the_ratings') ) {
      $open = '<div class="post_rating">';
      $body = '<span class="post_rating__please"> Rate this story </span>' . the_ratings('div',0,false);
      $close = '</div>';
      $html = $open . $body . $close;
      $content = $content . $html;
    }
  }
  return $content;
}
add_filter( 'the_content', 'rpf_add_rating_to_post_bottom', 97 ); // 98 -> add_to_any plugin

/**
 * Edit related products number
 *
 * @param [dictionaly] $args
 * @return void
 */
function rpf_edit_related_products_number( $args ) {
  global $product;
  $related_ids = get_post_meta( $product->id, '_related_ids', true );
  if ( ! empty($related_ids) && is_array($related_ids) ) {
    $args['posts_per_page'] = count($related_ids);
    $args['columns'] = $args['posts_per_page'];
  }
  return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'rpf_edit_related_products_number' );

/**
 * add national formated phone number input field and hide former input field.
 *
 * @param [dictionary] $fields
 * @return void
 */
function rpf_add_phone_field_on_billing($fields) {
  // add phone field
  //$fields['billing_phone_national_format'] = $fields['billing_phone'];
  //$fields['billing_phone_national_format']['type'] = 'text';
  //$fields['billing_phone_national_format']['required'] = false;
  $fields['billing_phone']['class'] = array('form-row-first');
  $fields['billing_phone']['priority'] = 90;
  //$fields['billing_phone']['required'] = false;

  // set email field and phone field on one row
  //$fields['billing_phone_national_format']['class'] = array('form-row-first');
  //$fields['billing_phone_national_format']['priority'] = 90;
  $fields['billing_email']['class'] = array('form-row-last');
  $fields['billing_email']['priority'] = 100;

  return $fields;
}
add_filter('woocommerce_billing_fields', 'rpf_add_phone_field_on_billing');

/**
 * add random version to style file ref to prevent css from cashed by browser
 *
 * @param [url] $src
 * @param [string] $handle
 * @return void
 */
function rpf_prevent_css_cache($src, $handle) {
  if ( $handle == 'minera-theme-style' ) {
    $src .= '?ver='.time();
  }
  return $src;
}
add_filter( 'style_loader_src', 'rpf_prevent_css_cache', 100, 2 );

/**
 * add random version to js file ref to prevent css from cashed by browser
 *
 * @param [url] $src
 * @param [string] $handle
 * @return void
 */
function rpf_prevent_js_cache($src, $handle) {
  if ( $handle == 'rpf-main' || $handle == 'rpf-functions' ) {
    $src .= '?ver='.time();
  }
  return $src;
}
add_filter( 'script_loader_src', 'rpf_prevent_js_cache', 100, 2 );

/**
 * uncheck different shipping option forcely
 *
 * @param [type] $var
 * @return void
 */
function rpf_force_uncheck_different_shipping($var) {
  //return 0;
}
add_filter( 'woocommerce_ship_to_different_address_checked', 'rpf_force_uncheck_different_shipping', 100, 1 );

/**
 * change myaccount address order
 *
 * @param [type] $addresses
 * @return void
 */
function rpf_change_myaccount_address_order($addresses) {
  $new_addresses['shipping'] = $addresses['shipping'];
  $new_addresses['billing'] = $addresses['billing'];

  return $new_addresses;
}
add_filter( 'woocommerce_my_account_get_addresses', 'rpf_change_myaccount_address_order', 100, 1 );

/**
 * change address fields property(hidden, required, label ...)
 *
 * cases for which options set here apply
 * 1. select country
 * 2. validation before save user address
 * 
 * @param [type] $nations
 * @return void
 */
function rpf_change_kr_address_fields_props($nations) {
  $nations['KR']['city']['hidden'] = true;
  $nations['KR']['city']['required'] = false;
  $nations['KR']['first_name']['label'] = 'Name';
  $nations['KR']['last_name']['required'] = false;
  return $nations;
}
add_filter( 'woocommerce_get_country_locale', 'rpf_change_kr_address_fields_props', 100, 1 );

/**
 * add address search button
 *
 * @param [type] $fields
 * @return void
 */
function rpf_edit_default_address_fields($fields) {
  /* ------ remove company name field ------ */
  unset($fields['company']);

  /* ------ add daum address search button ------ */
  $fields['address_search'] = array(
    'label'        => '<input type="button" value="우편번호 찾기" class="btn postcode_search" style="height: 40px; margin-top:20px;">',
    'required'     => false,
    'hidden'       => true,
    'class'        => array( 'form-row-wide', 'address-field' ),
    'autocomplete' => 'address-level2',
    'priority'     => 35,
  );

  /* ------ reordering ------ */
  $fields['country']['priority'] = 10;
  $fields['first_name']['priority'] = 20;
  $fields['last_name']['priority'] = 30;
  $fields['address_1']['priority'] = 40;
  $fields['address_2']['priority'] = 50;
  $fields['city']['priority'] = 60;
  $fields['state']['priority'] = 70;
  $fields['postcode']['priority'] = 80;

  return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'rpf_edit_default_address_fields', 100, 1 );

/**
 * make phone field required on billing fields when user want to get sms update on checkout page
 *
 * @param [type] $fields
 * @return void
 */
function rpf_make_phone_field_required( $fields ) {
  // if user want to get sms update message, phone fields should be filled
  $fields['billing_phone']['required'] = true;
  // $fields['billing_phone_national_format']['required'] = false;
	return $fields;
}
// add_filter( 'woocommerce_billing_fields', 'rpf_make_phone_field_required', 100, 1 );

/**
 * Add customer number field on checkout page
 *
 * @param [type] $fields
 * @return void
 */
function rpf_override_checkout_field( $fields ) {
  // default is true, because we need required html mark on label
  $is_custom_number_required = true;

  // check billing country on checkout processing
  if ( isset($_POST['billing_country']) && $_POST['billing_country'] != 'KR' ) {
    $is_custom_number_required = false;
  }
  $custom_id_number_url = ot_get_option('custom_id_number_url');
  if ( !$custom_id_number_url ) {
    $custom_id_number_url = '#';
  }
  $custom_id_number_anchor = '<a href="' . $custom_id_number_url . '"> here </a>';

  // add customer number field
  $custom_number = array(
    'label'         => esc_html__( 'Customs ID Number', 'minera' ),
    'placeholder'   => esc_html__( 'please enter customs id number', 'minera' ),
    'required'      => $is_custom_number_required,
    'class'         => array('form-row-wide'),
    'clear'         => true,
    'priority'      => 110,
    'description'   => __( 'Please refer to the Customs Number ID guidance page', 'minera' )
  );
  $fields['billing']['billing_custom_number'] = $custom_number;
  $fields['shipping']['shipping_custom_number'] = $custom_number;

  return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'rpf_override_checkout_field' );

/**
 * Change credit card icons on Checkout page
 *
 * @param array $icons
 * @return void
 */
function rpf_change_creditcard_icons( $icons ) {
  // var_dump( $icons ); to show all possible icons to change.
  $icon_url = get_site_url().'/wp-content/plugins/woocommerce/assets/images/icons';
  $icons['visa'] = '<img src="'.$icon_url.'/credit-cards/visa.svg" />';
  $icons['mastercard'] = '<img src="'.$icon_url.'/credit-cards/mastercard.svg" />';
  $icons['amex'] = '<img src="'.$icon_url.'/credit-cards/amex.svg" />';
  return $icons;
}
add_filter( 'wc_stripe_payment_icons', 'rpf_change_creditcard_icons' );

/**
 * Remove product types in product edit page of admin
 *
 * @param [type] $types
 * @return void
 */
function rpf_remove_product_type_selectors( $types ) {
  unset( $types['grouped'] );
  unset( $types['external'] );

  return $types;
}
add_filter( 'product_type_selector', 'rpf_remove_product_type_selectors' );

/**
 * Remove product type options in backend to simplify admin page
 *
 * @param array $options
 * @return void
 */
function rpf_remove_product_type_options( $options ) {
  unset( $options['downloadable'] );
  unset( $options['virtual'] );

  return $options;
}
add_filter( 'product_type_options', 'rpf_remove_product_type_options' );