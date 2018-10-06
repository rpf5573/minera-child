<?php
/**
 * Displaying the page template infor
 */

/*Customizer/Metabox variable*/
$c_bread = get_theme_mod('c_bread', '1');
$c_bread_nav = get_theme_mod('c_bread_display', '1');

/*page id*/
$pid = get_queried_object_id();
$p_bread = (function_exists('fw_get_db_post_option')) ? fw_get_db_post_option($pid, 'p_bread') : '1';

/*support product category*/
if(is_tax()){
$pid = get_queried_object()->term_id;
$tax = get_queried_object()->taxonomy;
$p_bread = (function_exists('fw_get_db_term_option')) ? fw_get_db_term_option($pid, $tax,'p_bread') : '1';
}

/*Kirki customizer option*/
$c_header_bg = get_theme_mod( 'c_header_bg', 'bg_color' );
$c_header_bg_color = get_theme_mod( 'c_header_bg_color' , '#0b6070' );
$c_header_bg_image = get_theme_mod( 'c_header_bg_image' , false );
$img = wp_get_attachment_image_src($c_header_bg_image, 'full');

/*set default value*/
$final_bread_display = $c_bread;

$gadget = $spacing_top = $spacing_bot = '';
$final_bread_style = array();

/*bread title*/
$bread_title = get_the_title();

/*shop title*/
$shop_title = get_theme_mod('c_shop_title', 'Shop');

/*blog title*/
$blog_title = get_theme_mod('c_blog_title', 'Blog');

/*bread text align*/
$bread_align = get_theme_mod('c_bread_align', 'center');



if(function_exists('FW')){
if(is_page() || is_single() || is_tax()){
/*variables*/
$gadget = $p_bread['gadget'];

/*page header value*/
if(isset($gadget) && $gadget != 'default'){
if($gadget == 'custom'){
$final_bread_display = '1';
}else{
$final_bread_display = '0';
}
}

/*Override value if enable custom page header*/
if(isset($gadget) && $gadget == 'custom'){

/*bread title*/
$bread_title = !empty($p_bread['custom']['p_bread_title']) ? $p_bread['custom']['p_bread_title'] : get_the_title();

/*bread align*/
$bread_align = $p_bread['custom']['p_bread_align'];

/*bread bg*/
$p_bread_bg = isset($p_bread['custom']['p_bread_bg']) ? $p_bread['custom']['p_bread_bg'] : '';

if(isset($p_bread_bg['gadget']) && $p_bread_bg['gadget'] == 'color_bg'){
$final_bread_style[] = 'background:' . $p_bread_bg['color_bg']['color_bg_data'];
}else{
if(!empty($p_bread_bg['img_bg']['img_bg_data'])):
$final_bread_style[] = 'background-image:url(' . $p_bread_bg['img_bg']['img_bg_data']['url'] . ')';
endif;
}

/*bread margin bottom*/
if(!empty($p_bread['custom']['p_bread_margin_bottom'])){
$final_bread_style[] = 'margin-bottom: ' . $p_bread['custom']['p_bread_margin_bottom'];
}            

/*spacing*/
$spacing_top = $spacing_bot = '';
if(!empty($p_bread['custom']['p_bread_spacing_top'])){
$spacing_top = 'style="height: '. $p_bread['custom']['p_bread_spacing_top'] .'"';
}
if(!empty($p_bread['custom']['p_bread_spacing_bot'])){
$spacing_bot = 'style="height: '. $p_bread['custom']['p_bread_spacing_bot'] .'"';
}
}
}
}

/*output breadcrumbs style*/
$final_bread_style = !empty($final_bread_style) ? 'style="' . implode('; ', $final_bread_style) . '"' : '';

/*text align for single product*/
if(is_singular( 'product' )){
$bread_align = 'left';
}

$signboard_space['top'] = 100;
$signboard_space['bottom'] = 140;

if ( is_singular('post') || is_search() ) {
  $signboard_space['top'] = 30;
  $signboard_space['bottom'] = 30;
} else if ( is_product() ) {
  $signboard_space['top'] = 10;
  $signboard_space['bottom'] = 10;
}


/*not display on 404 page*/
if(!is_404() && $final_bread_display == '1'): ?>
  <nav class="theme-breadcrumb flw" <?php echo wp_kses_post($final_bread_style); ?>>
    <div class="space-120 md-space-60"></div>
    <div class="breadcrumbs">
      <?php /*breadcrumbs*/
        if(function_exists('bcn_display')) {
          bcn_display();
        }
      ?>
    </div>
    <div class="space-<?php echo $signboard_space['top']; ?>"></div>
    <div class="container">
      <div class="bread flw text-<?php echo esc_attr($bread_align); ?>">
        <?php /*page title*/ ?>
        <?php if(!is_singular( 'product' )):/*not showing page title on product single*/ ?>
        <h1 class="bread-title">
          <?php
            if ( is_day() ) :
              printf( esc_html__( 'Daily Archives: %s', 'minera'), get_the_date() );
            elseif ( is_month() ) :
              printf( esc_html__( 'Monthly Archives: %s', 'minera'), get_the_date( esc_html_x( 'F Y', 'monthly archives date format', 'minera')));
            elseif (is_home()) :
              echo esc_html($blog_title);
            elseif(is_author()):
              $author = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
              echo esc_html($author->display_name);
            elseif ( is_year() ) :
              printf( esc_html__( 'Yearly Archives: %s', 'minera'), get_the_date( esc_html_x( 'Y', 'yearly archives date format', 'minera') ) );
            elseif(class_exists( 'WooCommerce' ) && is_shop()):
              echo esc_html($shop_title);
            elseif(class_exists('WooCommerce') && is_product_tag() || is_tag()):
              esc_html_e('Tags: ', 'minera'); single_tag_title();
            elseif(is_page() || is_single()) :
              echo esc_html($bread_title);
            elseif( is_tax() ) :
              global $wp_query;
              $term = $wp_query->get_queried_object();
              $title = $term->name;
              echo esc_html($title);
            elseif( is_search() ):
              esc_html_e('Search results', 'minera');
            elseif( is_category() ):
              $this_category = get_queried_object();
              echo esc_html($this_category->name);
            else :
              esc_html_e( 'Archives', 'minera');
            endif;
          ?>
        </h1>
        <?php endif; ?>
      </div>
    </div>
    <div class="space-<?php echo $signboard_space['bottom']; ?> flw"></div>
    <?php minera_edit_location('bread');/*header edit location*/ ?>
  </nav>
  <?php endif;