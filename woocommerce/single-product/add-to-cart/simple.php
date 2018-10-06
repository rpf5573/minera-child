<?php
/**
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

global $product;

if ( ! $product->is_purchasable() ) return;

$pid = $product->get_id();

$stock_mng = get_post_meta( get_the_ID(), '_manage_stock', true );/*stock manager `yes` or `no`*/
$stock_qty = $product->get_stock_quantity();/*stock quan tity `INT`*/
$qty_hidden = ($stock_mng == 'yes' && $stock_qty < 2) ? 'hidden' : '';/*class*/

/*check product already in cart*/
$in_cart_qty = minera_product_in_cart($pid) ? minera_product_qty_in_cart($pid) : '0';

$not_enough = $out_of_stock = '';
if($stock_mng == 'yes'){
    $not_enough = esc_html__( 'You cannot add that amount of this product to the cart because there is not enough stock.', 'minera' );
    $out_of_stock = sprintf(esc_html__( 'You cannot add that amount to the cart - we have %1$s in stock and you already have %1$s in your cart', 'minera' ), $stock_qty);
}

echo wc_get_stock_html( $product );

if ( $product->is_in_stock() ) : ?>
	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>
  <form class="cart" method="post" enctype='multipart/form-data'>
    <div class="line-1"> <?php
     do_action( 'queensmallo_woocommerce_before_quantity', $product ); ?>
    </div>
    <div class="line-2">
      <div class="w-quantity <?php echo esc_attr($qty_hidden); ?>">
        <span class="w-minus w-quantity-btn ion-minus"></span>
        <?php
          do_action( 'woocommerce_before_add_to_cart_button' );
          do_action( 'woocommerce_before_add_to_cart_quantity' );
          woocommerce_quantity_input( array(
            'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
            'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
            'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity(),
          ) );
          do_action( 'woocommerce_after_add_to_cart_quantity' );
        ?>
        <span class="w-plus w-quantity-btn ion-plus"></span>
      </div>
      <input type="hidden" value="<?php echo esc_attr($in_cart_qty); ?>" data-out_of_stock="<?php echo esc_attr($out_of_stock); ?>" data-not_enough="<?php echo esc_attr($not_enough); ?>">
      <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt" data-stock_qty="<?php echo esc_attr($stock_qty); ?>"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
    </div>
    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>
	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
<?php endif; ?>