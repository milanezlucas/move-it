<?php
// Form Fiedls for dashboard

class MI_Forms
{
	protected $id;
	protected $field;
	protected $value;
	protected $slug;

	public function field( $form_id, $form_field, $form_value=null, $form_widget_id=null )
	{
		$this->id 			= $form_id;
		$this->value 		= $form_value ? $form_value : '';
		$this->slug 		= $form_widget_id ? '--' . $form_widget_id : '';

		$this->fields_supports( $form_field );

		$form = new stdClass();
		switch ( $this->field->type ) {
			case 'text':
				$form->field = $this->text_field();
				break;
			case 'textarea':
				$form->field = $this->textarea();
				break;
			case 'editor':
				$form->field = $this->editor();
				break;
			case 'date':
				$form->field = $this->date();
				break;
			case 'select':
				$form->field = $this->select();
				break;
			case 'radio':
				$form->field = $this->radio();
				break;
			case 'checkbox':
				$form->field = $this->checkbox();
				break;
			case 'number':
				$form->field = $this->number();
				break;
			case 'file':
				$form->field = $this->file();
				break;
		}

		$form->label 	= $this->field->label;
		$form->desc 	= $this->field->desc;
		$form->name 	= $this->id;

		return $form;
	}

	// Text field
	protected function text_field()
	{
		$html .= '<input name="' . $this->id . '" id="' . $this->field->name . '" value="' . esc_attr( stripslashes( $this->value ) ) . '" class="regular-text" type="text" ' . $this->fields->required . '>';

		return $html;
	}

	// Textarea
	protected function textarea()
	{
		$html .= '
			<textarea name="' . $this->id . '" id="' . $this->fields->name . '" class="large-text" rows="3" ' . $this->fields->required . '>' . esc_attr( stripslashes( $this->value ) ) . '</textarea>
		';

		return $html;
	}

	// editor
	protected function editor()
	{
		ob_start();
		wp_editor( stripslashes( $this->value ), $this->id . $this->slug, array( 'media_buttons' => true, 'editor_class' => 'widget_editor' ) );
		$html .= ob_get_clean();
		ob_end_flush();
		$html .= '<input type="hidden" name="' . $this->id . $this->slug . '_text" id="' . $this->field->name . $this->slug . '_text">';

		return $html;
	}

	// Date
	protected function date()
	{
		$html .= '<input name="' . $this->id . $this->slug . '" id="' . $this->field->name . $this->slug . '" value="' . esc_attr( $this->value ) . '" class="regular-text date-field" type="text" ' . $this->required . '>';

		return $html;
	}

	// Select
	protected function select()
	{
		$opt_name_array = $this->field->multiple ? '[]' : '';

		$html .= '
			<select name="' . $this->id . $opt_name_array .'" id="' . $this->field->name . '" ' . $this->field->multiple . ' class="postform">
				<option value=""></option>
		';
		$values = ( is_array( $this->value ) ) ? $this->value : array( $this->value );
		foreach ( $this->field->opt as $label => $val ) {
			$html .= '<option value="' . $val . '"';
			if ( $values ) {
				$html .= ( in_array( $val, $values ) ) ? 'selected="selected"' : '';
			}
			$html .= '>' . $label . '</option>';
		}
		$html .= '
			</select>
		';

		return $html;
	}

	// Radio
	protected function radio()
	{
		foreach ( $this->field->opt as $label => $val ) {
			$html .= '
				<p>
					<label>
						<input name="' . $this->id . '" value="' . $val . '" class="tog"';
			$html .= ( $val == $this->value ) ? 'checked="checked"' : '';
			$html .= 'type="radio">
						' . $label . '
					</label>
				</p>
			';
		}

		return $html;
	}

	// Checkbox
	protected function checkbox()
	{
		$values = ( is_array( $this->value ) ) ? $this->value : array( $this->value );
		if ( $this->field->opt ) {
			foreach ( $this->field->opt as $label => $val ) {
				$html .= '
					<p>
						<label>
							<input name="' . $this->id;
				$html .= $this->field->multiple ? '[]' : '';
				$html .= '" value="' . $val . '" class="tog" ';
				if ( $values ) {
					$html .= ( in_array( $val, $values ) ) ? 'checked="checked"' : '';
				}
				$html .= ' type="checkbox">
							' . $label . '
						</label>
					</p>
				';
			}
		}

		return $html;
	}

	// Number
	protected function number()
	{
		$min = $field[ 'min' ] ? 'min="' . $field[ 'min' ] . '"' : '';
		$max = $field[ 'max' ] ? 'max="' . $field[ 'max' ] . '"' : '';
		$html .= '
			<input name="' . $this->id . '" id="' . $this->field->name . '" value="' . esc_attr( $this->value ) . '"
		';
		$html .= $this->field->min ? 'min="' . $this->field->min . '"' : '';
		$html .= $this->field->max ? 'max="' . $this->field->max . '"' : '';
		$html .= ' class="small-text" type="number" ' . $this->field->required . '>';

		return $html;
	}

	// File (upload)
	protected function file()
	{
		$html .= '
			<ul class="upload-field-view-' . $this->field->name . $this->slug . ' upload-sortable">
		';
		$html .= $this->value ? mi_get_upload_button( $this->value, $this->field->name . $this->slug ) : '';
		$html .= '
			</ul>
			<input type="hidden" name="' . $this->id . $this->slug . '" id="' . $this->field->name . $this->slug . '" value="' . esc_attr( $this->value ) . '" class="upload_field">
			<input id="' . $this->field->name . $this->slug . '_button" type="button" class="button button_upload" value="Biblioteca" />
		';

		return $html;
	}

	/*
		Fields supports

		type: text, textarea, editor, select, checkbox, radio, file, date, number
		label: Label for field
		opt: array, select, checkbox, radio
		desc: Description for field
		required: Field required
		name: Nem id for field
		min: Min number
		max: Max number
	*/
	private function fields_supports( $form_field )
	{
		$this->field = new stdClass();
		foreach ( $form_field as $prop => $value ) {
			switch ( $prop ) {
				case 'opt':
					if ( $value ) {
						$this->field->$prop = new stdClass();
						foreach ( $value as $val => $label ) {
							if ( !empty( $val ) ) {
								$this->field->$prop->$label = $val;
							}
						}
					}
					break;
				case 'required':
					$this->field->$prop = $value ? 'required="true"' : '';
					break;
				case 'name':
					$this->field->$prop = $value ? $value : $this->id;
					break;
				case 'multiple':
					$this->field->$prop = $value ? 'multiple="true"' : '';
					break;

				default:
					$this->field->$prop = $value;
					break;
			}
		}
	}
}
