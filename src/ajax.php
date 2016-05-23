<?php 

function send_form_test()
{
    $theme = new Theme;
    $response = $theme->form_test();

    echo json_encode( $response );
    exit();
}
?>