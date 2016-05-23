<?php 
$opt_test = new MI_Options(
	'test',
	'Teste',
	array(
		'menu_title'	=> 'Teste',
		'capability'	=> 'administrator',
		'icon'			=> 'dashicons-book',
		'function'		=> '', // Custom theme option
		'position'		=> '81'
	),
	array(
		'text_test'	=> array(
			'type'		=> 'text',
			'label'		=> 'Text',
			'desc'		=> 'Campo de Texto',
			'required'	=> true,
		)
	)
);

// Submenu page
$opt_submenu_test = new MI_Options(
	'sub_test',
	'Test Submenu',
	array(
		'parent'		=> 'test',
		'menu_title'	=> 'Teste Submenu',
		'capability'	=> 'administrator',
		'function'		=> '', // Custom theme option
	),
	array(
		'text_sub'	=> array(
			'type'		=> 'text',
			'label'		=> 'Text',
			'desc'		=> 'Campo de Texto',
			'required'	=> true,
		),
		'text_sub2'	=> array(
			'type'		=> 'text',
			'label'		=> 'Text2',
			'desc'		=> 'Campo de Texto 2',
			'required'	=> true,
		)
	)
);

?>