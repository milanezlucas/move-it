<?php 
// Scripts (css, javascript)
function mi_back_end_formats()
{
    if ( is_admin() ) {
        wp_enqueue_style( 'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css', true );      
        wp_enqueue_script( 'admin', MI_URL_THEME . '/dashboard/js/admin.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' ), '', true );
        wp_enqueue_style( 'admin', MI_URL_THEME . '/dashboard/css/admin.css', true );

        // Media Uploader
        wp_enqueue_script( 'media-upload' );
        if ( function_exists( 'wp_enqueue_media' ) ) { wp_enqueue_media(); }
    }
}

// Register Ajax
function mi_ajax( $ajax )
{
	$action = array(
        'upload_image',
        'add_widget',
        'form_widget',
        'exclude_widget'
    );

    $i = count( $action );
    foreach ( $ajax as $key => $name ) {
    	$action[ $i ] = $name;
    	$i++;
    }

    for ( $i=0; $i < count( $action ); $i++ ) { 
        add_action( 'wp_ajax_' . $action[ $i ],        'send_' . $action[ $i ] );
        add_action( 'wp_ajax_nopriv_' . $action[ $i ], 'send_' . $action[ $i ] );
    }
}

// Images Size
function mi_images( $imgs )
{
    $sizes = array( 'thumbnail', 'medium', 'large' );
    $i = count( $sizes );
    foreach ( $imgs as $name => $size ) {
        add_image_size( $name, $size[ 0 ], $size[ 1 ], true );
        $sizes[ $i ] = $name;
        $i++;
    }
    
    return mi_images_sizes( $sizes );
}
function mi_images_sizes( $sizes ) { return $sizes; }
add_filter( 'intermediate_image_sizes', 'mi_images_sizes', 10, 3 );

// Async javascript
function mi_async_javascript( $url )
{
    if ( strpos( $url, '#async' ) === false ) {
        return $url;
    } elseif ( is_admin() ) {
        return str_replace( '#async', '', $url );
    } else {
        return str_replace( '#async', '', $url ) . "' async='async"; 
    }
}
add_filter( 'clean_url', 'mi_async_javascript', 11, 1 );

// Defer javascript
function mi_defer_javascript( $url )
{
    if ( strpos( $url, '#defer' ) === false ) {
        return $url;
    } elseif ( is_admin() ) {
        return str_replace( '#defer', '', $url );
    } else {
        return str_replace( '#defer', '', $url ) . "' defer='defer"; 
    }
}
add_filter( 'clean_url', 'mi_defer_javascript', 11, 1 );

// Upload Input File
function send_upload_image()
{
    $response = array(
        'msg'   => mi_get_upload_button( $_POST[ 'post_id' ], $_POST[ 'input_id' ] ),
    );
    echo json_encode( $response );

    exit();
}

function mi_get_upload_button( $post_id, $input_id )
{
    $img = ( is_array( $post_id ) ) ? $post_id : explode( ',', $post_id );
    for ( $i=0; $i < count( $img ); $i++ ) { 
        if ( $img[ $i ] ) {
            $attachments = get_posts(array(
                'post_type'         => 'attachment',
                'posts_per_page'    => -1,
                'post_status'       => 'any',
                'post_parent'       => null,
                'include'           => array( $img[ $i ] )
            ));

            foreach ( $attachments as $post ) {
                $ext = pathinfo( $post->guid, PATHINFO_EXTENSION );
                $file_name = ( $ext != 'jpg' && $ext != 'gif' && $ext != 'png' ) ? '<br><small>' . basename( $post->guid ) . '</small>' : '';

                $image_uploaded = wp_get_attachment_image_src( $post->ID, 'thumbnail', true );

                $html .= '
                    <li class="upload_' . $input_id . '_'  . $post->ID . '" rel="' . $input_id . '" id="' . $post->ID . '" style="margin: 5px;">
                        <img src="' . $image_uploaded[ 0 ] . '" alt="' . $post->post_title . '" title="' . $post->post_title . '" width="75" height="75"  class="upload-handle">
                        ' . $file_name . '
                        <br><a href="#" class="remove-file" rel="' . $post->ID . '" data-input="' . $input_id . '">Remover</a>
                    </li>
                ';
            }
        }
    }

    return $html;
}

// Widgets
function send_add_widget()
{
    $wdg = new MI_Widgets;
    $widget_id = $wdg->widget_name( $_POST[ 'widget' ] );

    $response = array(
        'widget_id' =>  $widget_id
    );

    echo json_encode( $response );
    exit();
}

function send_form_widget()
{
    $wdg = new MI_Widgets;
    
    $merges = json_encode( $_POST );
    $wdg->widget_save( $_POST[ 'widget_id' ], $merges );

    exit();
}

function send_exclude_widget()
{
    $wdg = new MI_Widgets;
    $wdg->exclude_widget( $_POST[ 'widget' ] );

    exit();
}

// Get all posts of one or more type of post
function mi_get_posts( $post_type )
{
    $rs_pages = new WP_Query( array(
        'post_type'         => $post_type,
        'posts_per_page'    => -1
    ));
    while ( $rs_pages->have_posts() ) {
        $rs_pages->the_post();

        $pages[ get_the_ID() ] = get_the_title();
    }
    wp_reset_postdata();

    return $pages;
}

// Get all categories of a taxonomy/category
function mi_get_tax( $tax )
{
    $terms = get_terms( $tax, array( 
        'hide_empty'    => false, 
    ));
    $cat[ 0 ] = '';
    foreach ( $terms as $term ) {
        $cat[ $term->term_id ] = $term->name;
    }

    return $cat;
}
?>