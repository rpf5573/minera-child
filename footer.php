<?php
/**
 * The template for displaying the footer.
 * Contains the closing of the #content div and all content after
 * @package minera
 */

# Remove complex options
?>

	<footer class="theme-footer flw">
    <div class="footer-default">
      <a href="<?php echo esc_url(home_url( '/' )); ?>"><?php esc_html_e('QueensMallo.', 'minera'); ?></a>
      <p><?php echo '&copy; ' . date('Y'); ?> <a href="<?php echo esc_url(home_url( '/' )); ?>"><?php esc_html_e('QueensMallo.', 'minera') ?> </a><?php esc_html_e('All Rights Reserved.', 'minera'); ?></p>
    </div>
		<span class="scroll-to-top ion-ios-arrow-up" aria-label="<?php esc_attr_e('Back to top', 'minera'); ?>" title="<?php esc_attr_e('Scroll To Top', 'minera'); ?>"></span>
	</footer>

<?php if(class_exists('Woocommerce' )):/*quick view content*/ ?>
	<div id="ht-quick-view-popup" class="c-ht-qvp" role="dialog">
		<div class="ht-qvo"></div>
		<div class="quick-view-box">
			<a href="#" id="ht-qvc" class="c-ht-qvc ion-ios-close-empty"></a>
		</div>
	</div>
    <div id="ht-cart-sidebar">
        <div class="cart-sidebar-head">
            <h3 class="cart-sidebar-title"><?php esc_html_e( 'Shopping cart', 'minera' ); ?></h3>
            <button class="cart-sidebar-close-btn ion-ios-close-outline" aria-label="<?php esc_attr_e('Close', 'minera'); ?>"></button>
        </div>
        <div class="cart-sidebar-content">
            <?php woocommerce_mini_cart(); ?>
        </div>
    </div>
    <div class="ht-cart-overlay"></div>
<?php endif; ?>

<?php minera_header_layout_six($start = false);/*header-layout-6*/ ?>
<?php minera_page_content_boxed($start = false);/*page content boxed layout*/ ?>

</div>

<?php if(get_theme_mod('loading', false) == true):/*loading effect*/ ?>
    <span class="is-loading-effect"></span>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>