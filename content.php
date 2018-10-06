<?php $related = (function_exists('fw_get_db_post_option') ? fw_get_db_post_option($post->ID, 'p_related') : 'disable');/*blog posts related*/ ?>

<div class="blog-article" itemscope itemtype="https://schema.org/Blog">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/BlogPosting">
		<div class="flw" itemprop="mainEntityOfPage">
			<?php minera_structured_data();/*support structured data - SEO optimize*/ ?>
			<?php if ( !is_single() ) { minera_post_thumbnail(); }/*blog post thumbnail*/ ?>
			<?php /*post content*/ ?>
			<div class="post-content">
				<?php if ( !is_single() ) { minera_single_post_info();/*blog single option for post info*/ } ?>
				<?php if(!is_single()): ?>
					<p class="blog-post-sumary" itemprop="description"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
					<a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Read More', 'minera'); ?><span class="screen-reader-text"><?php esc_html_e('about an interesting article to read', 'minera') ?></span></a>
				<?php else: ?>
					<div class="blog-post-single" itemprop="articleBody">
							<?php the_content(); minera_wp_link_pages(); ?>
					</div>
					<?php minera_blog_tags(); ?>
				<?php endif; ?>
			</div>
			<?php if(is_single()): ?>
				<?php if($related == 'top') get_template_part('post', 'related');/*blog posts related*/ ?>
				<?php minera_author_card();/*author card*/ ?>
				<?php /*comment*/
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				if($related == 'bot') get_template_part('post', 'related');/*blog posts related*/ ?>
			<?php endif; ?>
		</div>
	</article>
</div>