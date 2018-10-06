<?php
/**
 * The main template file.
 */

get_header(); ?>

	<main id="main" class="blog_content flw">
		<div class="container">
			<div class="row">
        <?php get_template_part('content', 'posts'); ?>
			</div>
		</div>
	</main>

<?php get_footer(); ?>