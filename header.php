<?php 
/**
* The header for our theme.
* Displays all of the <head> section
* @package minera
*/ ?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> itemscope itemtype="http://schema.org/WebPage">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
<?php 
    $menu_color = get_field('menu_color');
    $icon_color = get_field('icon_color');
    $title_color = get_field('title_color');
    $cart_count_color = get_field('cart_count_color');
    echo "<style>\n";
    if ( $menu_color ) {
        echo "    #theme-container .theme-primary-menu > li > a {\n";
        echo "        color : {$menu_color};\n";
        echo "    }\n";   
    }
    if ( $icon_color ) {
        echo "    #theme-container .theme-search-btn, #theme-container .theme-login-btn, #theme-container .shopping-cart-icon {\n";
        echo "        color : {$icon_color};\n";
        echo "    }\n";

        echo "    #theme-container .counter-cart {\n";
        echo "        background-color : {$icon_color};\n";
        echo "        color : {$cart_count_color};\n";
        echo "    }\n";
    }
    if ( $title_color ) {
        echo "    #theme-container .bread-title {\n";
        echo "        color : {$title_color};\n";
        echo "    }\n";
    }
    echo "</style>";
?>

</head>

<body <?php body_class(); ?>>
    <?php minera_loading_effect();/*loading effect*/ ?>
    <?php minera_sticky_menu();/*sticky menu*/ ?>
    <?php minera_search_form();/*search form*/ ?>
    <div id="theme-container" class="flw">
    <?php minera_page_content_boxed();/*page content boxed layout*/ ?>
	<?php minera_header_layout();/*header-layout*/ ?>
    