<?php
class Widgets_Fields
{
	// Test
	public static function wi_test( $inst )
	{
		$response = array(
			'text'	=> $inst->text,
		);

		return json_decode( json_encode( $response ) );
		exit();
	}
}
?>
