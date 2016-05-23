<?php
// Metaboxes
class Metabox extends MI_Forms
{
	protected $id;
	protected $title;
	protected $post_type;
	protected $fields;

	function __construct( $id, $post_type, $title='Informações Adicionais', $fields )
	{
		$this->id 			= $id;
		$this->title 		= $title;
		$this->post_type 	= $post_type;
		$this->fields     	= $fields;

		add_action( 'add_meta_boxes', 	array( $this, 'metabox_init' ) );
		add_action( 'save_post', 		array( $this, 'metabox_save' ) );
	}

	public function metabox_init()
	{
		if ( $this->post_type == get_post_type() ) {
			add_meta_box( $this->id, $this->title, array( 'Metabox', 'metabox_edit' ), $this->post_type, 'normal', 'high', array( 'id' => $this->id, 'fields' => $this->fields ) );
		}
	}

	public function metabox_edit( $post, $metabox )
	{
		wp_nonce_field( MI_PREFIX . 'meta_box', MI_PREFIX . 'meta_box_nonce' );

		$html .= '
			<input type="hidden" name="metabox[]" id="metabox[]" value="' . $metabox[ 'id' ] . '">
			<table class="form-table">
				<tbody>
		';
		$mi_forms = new MI_Forms;
		foreach ( $metabox[ 'args' ][ 'fields' ] as $id => $field ) {
			$form = $mi_forms->field( $id, $field, get_post_meta( $post->ID, MI_PREFIX . $id, true ) );

			$html .= '
						<tr>
							<th scope="row"><label for="' . $form->name . '">' . $form->label . '</label></th>
							<td>
			';
			$html .= $form->field;
			$html .= $form->desc ? '<p class="description">' . $form->desc . '</p>' : '';
			$html .= '
							</td>
						</tr>
			';
		}
		$html .= '
				</tbody>
			</table>
		';

		echo $html;
	}

	public function metabox_save( $post_id )
	{
		if ( !isset( $_POST[ MI_PREFIX . 'meta_box_nonce' ] ) ) { return; }
		if ( !wp_verify_nonce( $_POST[ MI_PREFIX . 'meta_box_nonce' ], MI_PREFIX . 'meta_box' ) ) { return; }
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
		if ( isset( $_POST[ 'post_type' ] ) && 'page' == $_POST[ 'post_type' ] ) {
			if ( !current_user_can( 'edit_page', $post_id ) ) { return; }
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) ) { return; }
		}

		foreach ( $_POST[ 'metabox' ] as $metabox_id ) {
			foreach ( $this->fields as $id => $field ) {
				add_post_meta( $post_id, MI_PREFIX . $id, $_POST[ $id ], true ) or update_post_meta( $post_id, MI_PREFIX . $id, $_POST[ $id ] );
			}
		}

		// foreach ( $_POST[ 'metabox' ] as $metabox_id ) {
		// 	if ( $this->boxes->$metabox_id->fields ) {
		// 		foreach ( $this->boxes->$metabox_id->fields as $id => $field ) {
		// 			add_post_meta( $post_id, MI_PREFIX . $id, $_POST[ $id ], true ) or update_post_meta( $post_id, MI_PREFIX . $id, $_POST[ $id ] );
		// 		}
		// 	}
		// }
	}
}
