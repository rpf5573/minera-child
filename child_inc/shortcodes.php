<?php
/**
 * @snippet       Display All Products Purchased by User - WooCommerce
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=22004
 * @author        Rodolfo Melogli
 * @compatible    Woo 3.1.2
 */ 
function bbloomer_user_products_bought() {
	global $product, $woocommerce, $woocommerce_loop;
	$columns = 6;
	$current_user = wp_get_current_user();
	$args = array(
    'post_type'             => 'product',
    'post_status'           => 'publish'
	);
  $loop = new WP_Query($args);
	
	ob_start();
	
	woocommerce_product_loop_start();
	
	while ( $loop->have_posts() ) : $loop->the_post();
	$theid = get_the_ID();
	if ( wc_customer_bought_product( $current_user->user_email, $current_user->ID, $theid ) ) {
	wc_get_template_part( 'content', 'product' ); 
	} 
	endwhile; 
	
	woocommerce_product_loop_end();
	
	woocommerce_reset_loop();
	wp_reset_postdata();
	
	return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
}
add_shortcode( 'product_history', 'bbloomer_user_products_bought' );