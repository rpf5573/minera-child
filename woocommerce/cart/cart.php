<?php
/**
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

wc_print_notices();

do_action( 'woocommerce_before_cart' );

global $woocommerce;
$cart_empty_url = wc_get_cart_url() . '?empty-cart';
$count = $woocommerce->cart->cart_contents_count; ?>


<span class="my-cart-total"><?php echo sprintf( esc_html__( 'My cart (%s Items)', 'minera'), $count ); ?></span>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
        <th class="product-name"><?php esc_html_e( 'Product Name', 'minera' ); ?></th>

				<!-- Template Edit - add weight row -->
        <th class="product-weight"><?php esc_html_e( 'Weight', 'minera' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'minera' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'minera' ); ?></th>
				<th class="product-subtotal"><?php esc_html_e( 'Total Price', 'minera' ); ?></th>
				<th class="product-remove">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'minera' ); ?>">
							<span class="item-cart-img">
								<?php /*product thumbnail*/
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
								if ( ! $product_permalink ) {
									echo esc_url($thumbnail);
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
								} ?>
							</span>
							<span class="item-cart-cont">
							<?php /*product name*/
							if ( ! $product_permalink ) {
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;';
							} else {
								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key );
							}

							// Meta data
							echo WC()->cart->get_item_data( $cart_item );

							// Backorder notification
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'minera' ) . '</p>';
							} ?>
							</span>
            </td>
            
						<!-- Template Edit - add product weight row -->
            <td class="product-weight" data-title="<?php esc_attr_e( 'Weight', 'minera' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_weight', $_product->get_weight(), $cart_item, $cart_item_key );
							?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'minera' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'minera' ); ?>">
							<span class="w-quantity">
							<?php
								echo '<span class="w-minus w-quantity-btn ion-minus"></span>';
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input( array(
										'input_name'  => "cart[{$cart_item_key}][qty]",
										'input_value' => $cart_item['quantity'],
										'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
										'min_value'   => '0',
									), $_product, false );
								}
								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
								echo '<span class="w-plus w-quantity-btn ion-plus"></span>';
							?>
							</span>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'minera' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
							?>
            </td>
            
						<td class="product-remove">
							<?php
								echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
									'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s" title="%s"></a>',
									esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
									esc_attr__( 'Remove this item', 'minera' ),
									esc_attr( $product_id ),
									esc_attr( $_product->get_sku() ),
									esc_attr__( 'Remove this item', 'minera' )
								), $cart_item_key );
							?>
            </td>
            
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<tr class="tr-action">
				<td colspan="5" class="actions">

					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon">
							<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Enter your coupon...', 'minera' ); ?>" />
							<input type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply', 'minera' ); ?>" />
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>
					<span class="cart-action">
						<a class="cart-empty-btn" href="<?php echo esc_url($cart_empty_url); ?>"><?php esc_html_e( 'Clear Shopping Cart', 'minera' ); ?></a>
						<input type="submit" class="button cart-update-btn" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'minera' ); ?>" />
					</span>
					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart' ); ?>
				</td>
			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>
<div class="cart-collaterals">
	<?php do_action( 'woocommerce_cart_collaterals' ); ?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
