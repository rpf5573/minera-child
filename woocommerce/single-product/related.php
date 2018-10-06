<?php
/**
 * Related Products
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( $related_products ) :

  $fw_page_layout = function_exists('fw_ext_sidebars_get_current_position') ? fw_ext_sidebars_get_current_position() : 'full';
  if(empty($fw_page_layout)){
      $fw_page_layout = 'full';
  } ?>

	<section class="related products">
        <?php if($fw_page_layout == 'full'){ echo '<div class="container">'; }/*open container tag*/ ?>
		<h2 class="related-title"><?php esc_html_e( 'Related products', 'minera' ); ?></h2>
		<?php woocommerce_product_loop_start(); ?>
			<?php foreach ( $related_products as $related_product ) : ?>
				<?php
				 	$post_object = get_post( $related_product->get_id() );
					setup_postdata( $GLOBALS['post'] =& $post_object );
					wc_get_template_part( 'content', 'product' ); ?>
			<?php endforeach; ?>
		<?php woocommerce_product_loop_end(); ?>
        <?php if($fw_page_layout == 'full'){ echo '</div>'; }/*close container tag*/ ?>
	</section>

<?php endif;

wp_reset_postdata();
