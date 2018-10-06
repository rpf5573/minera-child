<?php
/**
 * Add WPBaker Page Builder's shortcode for administrator to customize posts of fromt page.
 *
 * @return void
 */
function rpf_vc_map() {

  /*blog news*/
  $categories = get_terms( 'category' );
  $tags = get_tags( 'category' );
  $tag_arr = $category_arr = $posts_arr = array();
  /*get blog categories*/
  if( !empty( $categories ) && ! is_wp_error( $categories ) ) {
    foreach( $categories as $key ) {
      $category_arr[] = array( 'label' => $key->name, 'value' => $key->slug );
    }
  }
  /*get blog tags*/
  if( !empty( $tags ) && ! is_wp_error( $tags ) ) {
    foreach( $tags as $key ) {
      $tag_arr[] = array( 'label' => $key->name, 'value' => $key->slug );
    }
  }
  /*get blog posts*/
  $args = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => -1
  ));

  $data = $args->posts;
  if( !empty( $args ) && ! is_wp_error( $args ) ) {
    foreach( $data as $key ) {
      $posts_arr[] = array( 'label' => $key->post_title, 'value' => $key->post_name );
    }
  }
  vc_map( array(
    'name' => esc_html__( 'Blog Stories', 'minera' ),
    'base' => 'stories',
    'icon' => 'ion-ios-paper',
    'category' => esc_html__('Minera Theme', 'minera'),
    'description' => esc_html__( 'Add blog stories section', 'minera' ),
    'params' => array(
      /*style*/
      array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Style', 'minera' ),
        'param_name' => 'style',
        'admin_label' => true,
        'value' => array(
          esc_html__('Grid', 'minera') => 'grid'
        ),
        'std' => 'grid'
      ),
      /*list style center*/
      array(
        'type' => 'checkbox',
        'heading' => esc_html__( 'Centering things?', 'minera' ),
        'param_name' => 'center',
        'dependency' => array(
          'element' => 'style',
          'value' => 'list'
        ),
        'value' => array(
          esc_html__('Yes', 'minera') => 'yes'
        ),
        'std' => 'no'
      ),
      /*list style img thumbnail*/
      array(
        'type' => 'checkbox',
        'heading' => esc_html__( 'Basic view?', 'minera' ),
        'description' => esc_html__( 'Image thumbnail and blog post summary will not showing', 'minera' ),
        'param_name' => 'basic',
        'dependency' => array(
          'element' => 'center',
          'value' => 'yes'
        ),
        'value' => array(
          esc_html__('Yes', 'minera') => 'yes'
        ),
        'std' => 'yes'
      ),
      /*grid column*/
      array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Grid column', 'minera' ),
        'param_name' => 'grid_col',
        'dependency' => array(
          'element' => 'style',
          'value' => 'grid'
        ),
        'value' => array(3),
        'std' => '3'
      ),
      /*data source*/
      array(
        'type' => 'dropdown',
        'heading' => esc_html__('Narrow data source', 'minera'),
        'param_name' => 'data',
        'admin_label' => true,
        'value' => array(
          esc_html__('Categories', 'minera') => 'cat',
          esc_html__('Tags', 'minera') => 'tag',
          esc_html__('Posts', 'minera') => 'post',
        ),
        'std' => 'cat'
      ),
      /*cate*/
      array(
        'type' => 'autocomplete',
        'heading' => esc_html__( 'Enter categories', 'minera' ),
        'param_name' => 'data_cat',
        'settings' => array(
          'multiple' => true,
          'min_length' => 1,
          'unique_values' => true,
          'delay' => 200,
          'values' => $category_arr
        ),
        'dependency' => array(
          'element' => 'data',
          'value' => 'cat'
        )
      ),
      /*tag*/
      array(
        'type' => 'autocomplete',
        'heading' => esc_html__( 'Enter tags', 'minera' ),
        'param_name' => 'data_tag',
        'settings' => array(
          'multiple' => true,
          'min_length' => 1,
          'unique_values' => true,
          'delay' => 200,
          'values' => $tag_arr
        ),
        'dependency' => array(
          'element' => 'data',
          'value' => 'tag'
        )
      ),
      /*posts*/
      array(
        'type' => 'autocomplete',
        'heading' => esc_html__( 'Enter post name or post slug', 'minera' ),
        'param_name' => 'data_post',
        'settings' => array(
          'multiple' => true,
          'min_length' => 1,
          'unique_values' => true,
          'delay' => 200,
          'values' => $posts_arr
        ),
        'dependency' => array(
          'element' => 'data',
          'value' => 'post'
        )
      ),
      /*count*/
      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Items per page', 'minera' ),
        'description' => esc_html__('Number of items to show per page.', 'minera'),
        'param_name' => 'count',
        'dependency' => array(
          'element' => 'data',
          'value' => array('tag', 'cat')
        ),
        'value' => 5
      ),
      /*INCLIDE BY DEFAULT*/
      vc_map_add_css_animation(),
      array(
        'type'        => 'textfield',
        'heading'     => esc_html__('Class', 'minera' ),
        'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'minera'),
        'admin_label' => true,
        'param_name'  => 'class',
      ),
      array(
        'type'       => 'css_editor',
        'heading'    => esc_html__( 'CSS', 'minera' ),
        'param_name' => 'inline_css',
        'group'      => esc_html__( 'Design Options', 'minera' ),
      ),
    ),
  ));

}

if(function_exists('vc_map')){
	add_action( 'init', 'rpf_vc_map' );
}