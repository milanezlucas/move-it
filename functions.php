<?php
// Constants Framework
define( 'MI_URL',           get_home_url() );
define( 'MI_URL_WP',        get_site_url() );
define( 'MI_URL_THEME',     get_template_directory_uri() );
define( 'MI_PREFIX',        'mi_' );
define( 'MI_SITE_NAME',     get_bloginfo( 'title' ) );
define( 'MI_SITE_EMAIL',    get_bloginfo( 'admin_email' ) );
define( 'MI_TEMPLATEPATH', 	get_template_directory() );

// Mailchimp
define( 'MI_MC_API',    'MAILCHIMP API' );
define( 'MI_MC_DOUBLE', 'LIST ID' );
define( 'MI_MC_SINGLE', 'LIST ID' );

// Framework
require_once( MI_TEMPLATEPATH . '/dashboard/all.php' );

// Screens
require_once( MI_TEMPLATEPATH . '/screen/theme.php' );
require_once( MI_TEMPLATEPATH . '/screen/widgets.php' );

// Src Theme
require_once( MI_TEMPLATEPATH . '/src/custom_post.php' );
require_once( MI_TEMPLATEPATH . '/src/options.php' );
// require_once( MI_TEMPLATEPATH . '/src/widgets.php' );
require_once( MI_TEMPLATEPATH . '/src/sidebar.php' );
require_once( MI_TEMPLATEPATH . '/src/shortcode.php' );
require_once( MI_TEMPLATEPATH . '/src/ajax.php' );

// -----------------------------------------------------
add_action( 'after_setup_theme', 'mi_theme_setup' );

function mi_theme_setup ()
{
    add_filter( 'show_admin_bar', '__return_false' );

    add_post_type_support( 'page', 'excerpt' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );

    // Scripts (css, javascript)
    add_action( 'wp_print_scripts', 'mi_back_end_formats' );

    // Remove itens menu (Dashboard)
    add_action( 'admin_menu', 'mi_remove_menus' );

    // Remove code head
    remove_action( 'wp_head',           'wp_generator' );
    remove_action( 'wp_head',           'rsd_link' );
    remove_action( 'wp_head',           'wlwmanifest_link' );
    remove_action( 'wp_head',           'index_rel_link' );
    remove_action( 'wp_head',           'start_post_rel_link', 10, 0 );
    remove_action( 'wp_head',           'adjacent_posts_rel_link', 10, 0 );
    remove_action( 'wp_head',           'parent_post_rel_link', 10, 0 );
    remove_action( 'wp_head',           'feed_links', 2 );
    remove_action( 'wp_head',           'feed_links_extra', 3 );
    remove_action( 'wp_head',           'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles',   'print_emoji_styles' );


    // Widgets (Sidebar)
    add_action( 'widgets_init', 'unregister_widgets' );
    add_action( 'widgets_init', 'register_widgets' );

    // Shortcodes
    add_action( 'init', 'unregister_shortcode' );
    add_action( 'init', 'register_shortcode' );
}

// Configs

// Images Size
mi_images( array(
    'teste'  => array( 800, 768 )
));

// Menus
register_nav_menus( array(
    'menu-header'   => 'Menu do Cabeçalho',
));

// Remove itens menu (Dashboard)
function mi_remove_menus ()
{
    global $menu;
    $restricted = array(
        __( 'Posts' ),
        __( 'Comments' ),
    );
    end( $menu );
    while ( prev( $menu ) ) {
        $value = explode( ' ', $menu[ key( $menu ) ][ 0 ] );
        if ( in_array( $value[ 0 ] != NULL ? $value[ 0 ] : '', $restricted ) ) {
            unset( $menu[ key( $menu ) ] );
        }
    }
}

// CSS/JS
function mi_front_end_formats()
{
    if ( !is_admin() ) {
        // wp_enqueue_style( 'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/le-frog/jquery-ui.css', true );

        // wp_register_script( 'plugins', MI_URL_THEME . '/js/plugins.js', array(), '', true );
        // wp_enqueue_script( 'script', MI_URL_THEME . '/js/script.js', array( 'jquery', 'jquery-ui-datepicker', 'plugins' ), '', true );
    }
}
add_action( 'wp_print_scripts', 'mi_front_end_formats' );

// Screens
function get_screen( $screen )
{
    require_once( MI_TEMPLATEPATH . '/screen/' . $screen . '.php' );
}

// Remove editor from page
// function mi_remove_support() {
//     remove_post_type_support( 'page', 'editor' );
// }
// add_action( 'init', 'mi_remove_support' );

// Ajax
mi_ajax( array(
    ''
));
