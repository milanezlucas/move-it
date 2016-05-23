<?php
// Menu Options
class MI_Options extends MI_Forms
{
	protected $slug;
	protected $title;
	protected $args;
	protected $fields;

	function __construct( $id, $title, $args, $fields )
	{
		$this->slug 	= $id;
		$this->title 	= $title;
		$this->args 	= $args;
		$this->fields 	= $fields;

		add_action( 'admin_menu', array( $this, 'options_init' ) );
	}

	public function options_init()
	{
		$menu_title = $this->args[ 'menu_title' ] ? $this->args[ 'menu_title' ] : $this->title;
		$capability = $this->args[ 'capability' ] ? $this->args[ 'capability' ] : 'administrator';
		$icon 		= !empty( $this->args[ 'icon' ] ) ? $this->args[ 'icon' ] : 'dashicons-book';
		$function 	= $this->args[ 'function' ] ? $this->args[ 'function' ] : array( $this, 'options_fields' );

		if ( !empty( $this->args[ 'parent' ] ) ) {
			add_submenu_page( $this->args[ 'parent' ], $this->title, $menu_title, $capability, $this->slug, $function );
		} else {
			add_menu_page( $this->title, $menu_title, $capability, $this->slug, $function, $icon, $this->args[ 'position' ] );
		}
	}

	public function options_fields()
	{
		$this->options_save();

		$html .= '
			<div class="wrap">
				<h2>' . $this->title . '</h2>
		';
		$html .= '
				<form method="post">
					' . wp_nonce_field( MI_PREFIX . 'options', MI_PREFIX . 'options_nonce' ) . '
					<table class="form-table">
						<tbody>
		';
		foreach ( $this->fields as $id => $field ) {
			$form = $this->field( $id, $field, get_option( MI_PREFIX . $id ) );
			$html .= '
						<tr>
							<th scope="row"><label for="' . $form->name . '">' . $form->label . '</label></th>
							<td>
								' . $form->field . '
			';
			$html .= $form->desc ? '<p class="description">' . $form->desc . '</p>' : '';
			$html .= '
							</td>
						</tr>
			';
		}
		$html .= '
						</tbody>
					</table>
					<p class="submit"><input name="submit" id="submit" class="button button-primary" value="Salvar alterações" type="submit"></p>
				</form>
			</div>
		';

		echo $html;
	}

	protected function options_save()
	{
		if ( $_POST ) {
			if ( !isset( $_POST[ MI_PREFIX . 'options_nonce' ] ) ) {
				wp_die( 'Você não possui permissões suficientes para editar essa página!' );
			}
			if ( !wp_verify_nonce( $_POST[ MI_PREFIX . 'options_nonce' ], MI_PREFIX . 'options' ) ) {
				wp_die( 'Você não possui permissões suficientes para editar essa página!' );
			}

			foreach ( $this->fields as $id => $field ) {
				add_option( MI_PREFIX . $id, $_POST[ $id ] ) or update_option( MI_PREFIX . $id, $_POST[ $id ] );
			}

			echo '<div class="updated"><p>Opções atualizadas com sucesso!</p></div>';
		}
	}
}
