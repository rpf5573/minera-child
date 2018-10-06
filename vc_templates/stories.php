<?php
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

extract( $atts );

$class_to_filter = vc_shortcode_custom_css_class( $inline_css, ' ' ) . $this->getExtraClass( $class ) . $this->getCSSAnimation( $css_animation );
$all_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

if(empty($count)) $count = -1;

/*grid column*/
if($style == 'grid'){
    $g_col = 'grid-col-' . $grid_col;
} ?>

<div class="theme-blog-news theme-blog-news--stories blog-news-<?php echo esc_attr($style . ' ' . $g_col . ' ' . ' ' . $all_class); ?>">
    
<?php /*query*/
  if ( get_query_var('paged') ) {
    $paged = get_query_var('paged');
  }elseif ( get_query_var('page') ){
    $paged = get_query_var('page');
  }else {
    $paged = 1;
  }

  $args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'paged' => $paged
  );

  /*post data - convert to array*/
  $data_post = explode(', ', $data_post);

  if($data == 'tag') {
    /*get posts by tags*/
    $args['tag'] = $data_tag;
    $args['posts_per_page'] = $count;
  } elseif($data == 'cat') {
    /*get posts by categories*/
    $args['category_name'] = $data_cat;
    $args['posts_per_page'] = $count;
  } elseif($data == 'post' && !empty($data_post[0])) {
    /*get posts by order that admin set*/
    $args['post_name__in'] = $data_post;
    $args['orderby'] = 'post_name__in';
    // this is for post re-order plugin. The plugin add wp_posts.menu_order and this make problem on query.
    $args['ignore_custom_sort'] = true;
  }

  $query = new WP_Query($args);

  if( $query->have_posts() ):
    while($query->have_posts()): $query->the_post();
    global $post; ?>
    <article class="blog-article blog-article--story" itemscope itemtype="https://schema.org/Blog">
      <div id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/BlogPosting">
        <div class="flw" itemprop="mainEntityOfPage">
          <?php minera_structured_data();/*support structured data - SEO optimize*/ ?>
          <?php /*blog thumbnail*/
            $img_id  = get_post_thumbnail_id($post->ID);
            $thumbnail_initial = get_the_post_thumbnail_url($post->ID);
            /*default thumbnail image for blog list*/
            $thumbnail = function_exists('FW') ? fw_resize($img_id, 405, 405, true) : $thumbnail_initial;

            if($style == 'list-center'){
                $thumbnail = get_the_post_thumbnail_url($post->ID, 'full');
            }elseif($style == 'zigzag'){
                $thumbnail = function_exists('FW') ? fw_resize($img_id, 570, 400, true) : $thumbnail_initial;
            }elseif($style == 'masonry'){
                $thumbnail = function_exists('FW') ? fw_resize($img_id, 370) : $thumbnail_initial;
                minera_add_available_scripts('masonry');
            }

            $img_alt = minera_img_alt($img_id, 'Blog thumbnail');
          ?>
          <?php if(!empty($img_id)): ?>
            <a href="<?php the_permalink() ?>" class="blog-news-thumbnail blog-stories-thumbnail">
              <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($img_alt); ?>" itemprop="image">
            </a>
          <?php endif; ?>
          <?php /*post content*/ ?>
          <div class="post-content">
            <?php minera_post_info(); ?>
            <h2 class="pure-heading" itemprop="headline">
              <a class="blog-news-tit blog-stories-tit" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
            </h2>
            <p class="blog-post-sumary blog-story-sumary" itemprop="description"><?php echo wp_trim_words(get_the_excerpt(), 28, '...'); ?></p>
            <a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Read More', 'minera'); ?><span class="screen-reader-text"><?php esc_html_e('about an interesting article to read', 'minera') ?></span></a>
          </div>
        </div>
      </div>
    </article>
    <?php endwhile;
      if($style == 'masonry'){
          echo '<div class="gutter-sizer"></div>';
      }
      minera_paging($query);
      endif;
      wp_reset_postdata(); ?>
</div>