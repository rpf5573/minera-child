<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package minera
 */

if ( ! is_active_sidebar( 'blog-widget' ) ) return; ?>

<div id="blog-sidebar" class="widget-area side flw" role="complementary">
	<?php dynamic_sidebar( 'blog-widget' ); ?>
</div>