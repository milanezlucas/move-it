<?php 
$wdg = new MI_Widgets;

$wdg->add_post_widget( array(
	MI_CPT_TEST,
	'page'
));

$wdg->register_widget( 'Teste', 'wi_test', array(
	'text'	=> array(
		'type'		=> 'text',
		'label'		=> 'Text',
		'desc'		=> 'Campo de Texto',
		'required'	=> true,
	)
));

function wi_test( $inst )
{
	return $inst->text;
}

?>