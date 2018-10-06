<?php global $post;
$args = array(
	'posts_per_page' => 3,
	'post_type' => 'post',
	'ignore_sticky_posts' => 1,
	'post__not_in' => array( $post->ID )
);

$rel_query = new WP_Query( $args );
if(empty($rel_query->posts)) return;
?>

<div class="row single-blog-related">
	<h4 class="col-xs-12 single-blog-related__title"> Related posts </h4>
	<?php if( $rel_query->have_posts() ) :
while ( $rel_query->have_posts() ) : $rel_query->the_post(); ?>
	<div class="col-md-4 col-lg-4 b-related-item">
		<?php /*post thumbnail*/
$img_id = get_post_thumbnail_id($post->ID);
$thumbnail = function_exists('FW') ? fw_resize($img_id, 405, 405, true) : get_the_post_thumbnail_url($post->ID);
$img_alt = minera_img_alt($img_id, 'Blog thumbnail');
if(!empty($thumbnail)): ?>
		<a class="b-related-img" href="<?php the_permalink(); ?>">
			<img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($img_alt); ?>">
		</a>
		<?php endif; ?>
		<div class="b-related-content">
			<span class="b-related-date">
				<?php echo minera_post_date();/*post date*/ ?>
			</span>
			<a href="<?php the_permalink(); ?>" class="b-related-tit">
				<?php the_title(); ?>
			</a>
			<a href="<?php the_permalink(); ?>" class="read-more">
				<?php esc_html_e('Read More', 'minera'); ?>
				<span class="screen-reader-text">
					<?php esc_html_e('about an interesting article to read', 'minera') ?>
				</span>
			</a>
		</div>
	</div>
	<?php endwhile;
endif;

wp_reset_postdata(); ?>

</div>