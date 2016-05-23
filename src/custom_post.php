<?php
// Define Custom Posts
define( 'MI_CPT_TEST', 'teste' );

$cpt = new MI_Custom_Post;

// Register Custom Post Type
$cpt->create_cpt( MI_CPT_TEST, array(
    'label'         => 'Teste',
    'label_plural'  => 'Testes',
    'public'        => true,
    'show_ui'       => true,
    'has_archive'   => true,
    'rewrite'       => array( 'slug' => 'teste', 'hierarchical' => true ),
    'supports'      => array( 'title', 'thumbnail', 'editor', 'excerpt' )
));

// Metaboxes
$box_1 = new Metabox( MI_CPT_TEST . '_box', MI_CPT_TEST, 'Informações Adicionais', array(
	'text'	=> array(
		'type'		=> 'text',
		'label'		=> 'Text',
		'desc'		=> 'Campo de Texto',
		'required'	=> true,
	),
    'checkbox' => array(
        'type'      => 'checkbox',
        'label'     => 'Teste Checkbox',
        'multiple'  => true,
        'opt'       => array(
            '1' => 'Item 1',
            '2' => 'Item 2'
        )
    )
));

$box_2 = new Metabox( MI_CPT_TEST . '_box_2', MI_CPT_TEST, 'Informações Adicionais 2', array(
	'text2'	=> array(
		'type'		=> 'text',
		'label'		=> 'Text',
		'desc'		=> 'Campo de Texto',
		'required'	=> true,
	)
));

// -------------------------------

// Register Taxonomy
// Define Taxonomy
define( 'MI_TAX_TEST', MI_CPT_TEST . '-type' );

$cpt->create_tax( MI_TAX_TEST, MI_CPT_TEST, array(
	'post_type'     => MI_CPT_TEST,
    'label'         => 'Tipo de Teste',
    'label_plural'  => 'Tipos de Teste',
    'public'        => true,
    'rewrite'       => array( 'slug' => 'tipos-de-teste', 'hierarchical' => true )
));

// Taxonomy: Term Meta
$term_meta = new MI_Term_Meta( MI_TAX_TEST, array(
	'text'	=> array(
		'type'		=> 'text',
		'label'		=> 'Text',
		'desc'		=> 'Campo de Texto',
		'required'	=> true,
	)
));
