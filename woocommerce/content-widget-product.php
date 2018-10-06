<?php
/**
 * The template for displaying product widget entries
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product; ?>

<li>
	<a class="recently_viewed_product" href="<?php echo esc_url( $product->get_permalink() ); ?>">
		<div class="recently_viewed_product__thumbnail">
			<?php echo $product->get_image(70); ?>
		</div>
		<div class="recently_viewed_product__text">
			<span class="recently_viewed_product__text__product-title"><?php echo $product->get_name(); ?></span>
			<div class="recently_viewed_product__text__price">
				<?php echo $product->get_price_html(); ?>
			</div>
		</div>
	</a>
</li>