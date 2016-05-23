<?php
// Creat Term Meta in Taxonomy
class MI_Term_Meta extends MI_Forms
{
	protected $id;
	protected $fields;

	function __construct( $id, $fields )
	{
		$this->id 		= $id;
		$this->fields 	= $fields;

		add_action( $id . '_add_form_fields', array( $this, 'add' ) );
        add_action( $id . '_edit_form',       array( $this, 'edit' ) );
        add_action( 'created_' . $id,         array( $this, 'save' ) );
        add_action( 'edit_' . $id,            array( $this, 'save' ) );
	}

	public function add()
	{
		foreach ( $this->fields as $id => $field ) {
			$form = $this->field( $id, $field );

			$html .= '
				<div class="form-field">
					<label for="' . $form->name . '">' . $form->label . '</label>
					' . $form->field . '
			';
			$html .=  $firm->desc ? '<p>' . $firm->desc . '</p>' : '';
			$html .= '
				</div>
			';
		}

		echo $html;
	}

	public function edit( $tax )
	{
		$html .= '
			<table class="form-table">
				<tbody>
		';
		foreach ( $this->fields as $id => $field ) {
			$form = $this->field( $id, $field, get_term_meta( $tax->term_id, MI_PREFIX . $id, true ) );

			$html .= '
					<tr class="form-field">
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
		';

		echo $html;
	}

	public function save( $term_id )
	{
		foreach ( $this->fields as $id => $field ) {
			if ( $field ) {
				add_term_meta( $term_id, MI_PREFIX . $id, $_POST[ $id ], true ) or update_term_meta( $term_id, MI_PREFIX . $id, $_POST[ $id ], get_term_meta( $term_id, MI_PREFIX . $id, true ) );
			}
		}
	}
}
