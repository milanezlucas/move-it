<?php 
function unregister_shortcode()
{
	remove_shortcode( 'gallery' );
}

function register_shortcode()
{
    $add = array(
        'test'   => 'st_test'
    );
    foreach ( $add as $id => $fn ) {
        add_shortcode( $id, $fn );
    }
}

// [test ids="25, 30"]
function st_test( $atts )
{
    extract( shortcode_atts( array(
        'ids' => array(),
    ), $atts ) );

    $html .= '';

    return $html;
}

?>