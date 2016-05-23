<?php 
/**
* Create Custom Posts Type and Taxonomy
*/
class MI_Custom_Post
{
	public function create_cpt( $slug, $args )
	{ 
		$cpts[ $slug ] = $args;

	    foreach ( $cpts as $cpt => $attr ) {
	        $label = ( isset( $attr[ 'label' ] ) ) ? $attr[ 'label' ] : ucfirst( $cpt );
	        $label_plural = ( isset( $attr[ 'label_plural' ] ) ) ? $attr[ 'label_plural' ] :  $label . 's';

	        $attr[ 'labels' ] = array(
	            'name'                  => $label_plural,
	            'add_new'               => 'Adicionar',
	            'add_new_item'          => 'Adicionar ' . $label,
	            'edit_item'             => 'Editar ' . $label,
	            'new_item'              => 'Adicionar ' . $label,
	            'view_item'             => 'Visualizar ' . $label,
	            'search_items'          => 'Pesquisar ' . $label_plural,
	            'not_found'             => 'Nenhum conteúdo foi encontrado',
	            'not_found_in_trash'    => 'Nenhum conteúdo foi encontrado na lixeira',
	            'all_items'             => 'Tudo'
	        );
	        register_post_type( $cpt, $attr );
	    }
	}

	public function create_tax( $slug, $post_type, $args )
	{
	    $taxs[ $slug ] = $args;

	    $attr_default = array(
	        'show_in_nav_menus'     => true,
	        'show_ui'               => true,
	        'show_tagcloud'         => false,
	        'hierarchical'          => true
	    );

	    foreach ( $taxs as $tax => $attr ) {
	        extract( $attr );
	        $attr[ 'labels' ] = array(
	            'name'              => $label_plural,
	            'singular_name'     => $label,
	            'search_items'      => 'Pesquisar ' . $label_plural,
	            'all_items'         => 'Tudo ' . $label_plural,
	            'parent_item'       => $label . ' acima',
	            'parent_item_colon' => $label . ' acima:',
	            'edit_item'         => 'Editar ' . $label_plural,
	            'update_item'       => 'Atualizar ' . $label,
	            'add_new_item'      => 'Adicionar ' . $label,
	            'new_item_name'     => 'Adicionar ' . $label,
	            'menu_name'         => $label_plural
	        );
	        $attr = array_merge( $attr_default, $attr );
	        register_taxonomy( $tax, $post_type, $attr );
	    }
	}
}
?>