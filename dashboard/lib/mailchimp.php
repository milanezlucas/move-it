<?php
function mi_mailchimp_subscribe( $email, $merges, $id, $double )
{
	global $wpdb;
	$wpdb->mailchimp = $wpdb->prefix . 'mailchimp';
    $table_exists = get_option( MI_PREFIX . 'mailchimp_exists' );
    if ( !$table_exists ) {
        add_option( MI_PREFIX . 'mailchimp_exists', true );
        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->mailchimp}` (
            `id_mailchimp` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `merges` text NOT NULL,
            `date` datetime NOT NULL,
            PRIMARY KEY (`id_mailchimp`)
        ) ENGINE=InnoDB";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

	$datacenter = explode( '-', MI_MC_API );
	if ( $double == '1' ) {
        $double_optin   = true;
    } else {
        $double_optin   = false;
    }
    $email_struct 			= new StdClass();
	$email_struct->email 	= $email;

    $data = array(
    	'apikey'            => MI_MC_API,
    	'id'                => $id,
    	'email' 			=> $email_struct,
    	'merge_vars'        => $merges,
        'email_type'        => 'html',
        'double_optin'      => $double_optin,
        'update_existing'   => false,
        'replace_interests' => true,
        'send_welcome'      => false
    );

    $curl = curl_init( 'https://' . $datacenter[ 1 ] . '.api.mailchimp.com/2.0/lists/subscribe' );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
	curl_setopt( $curl, CURLOPT_POST, true );
	curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
	$response = curl_exec( $curl );
	$data = json_decode( $response );

	$wpdb->insert(
		$wpdb->prefix . 'mailchimp',
		array(
			'email' 	=> $email,
			'merges' 	=> json_encode( $merges ),
			'date' 		=> date( 'Y-m-d H:i:s' )
		),
		array(
			'%s',
			'%s',
			'%s'
		)
	);

	// $member = mi_maichimp_member( $email );
	// return $member;
}

function mi_mailchimp_compare()
{
	global $wpdb;
	$rs_query_mailchimp = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "mailchimp ORDER BY id_mailchimp ASC" );
	foreach ( $rs_query_mailchimp as $rs_mailchimp ) {
		mi_maichimp_single( $rs_mailchimp->email, $rs_mailchimp->merges );
	}
}

function mi_maichimp_single( $email, $merges )
{
	$datacenter 			= explode( '-', MI_MC_API );
	$email_struct 			= new StdClass();
	$email_struct->email 	= $email;

	$data = array(
		'apikey'	=> MI_MC_API,
		'id'        => MI_MC_DOUBLE,
    	'emails' 	=> array( $email_struct )
	);

	$curl = curl_init( 'https://' . $datacenter[ 1 ] . '.api.mailchimp.com/2.0/lists/member-info' );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
	curl_setopt( $curl, CURLOPT_POST, true );
	curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
	$response = curl_exec( $curl );
	$data = json_decode( $response );

	if ( $data->errors[ 0 ]->code == 232 || $data->data[ 0 ]->status != 'subscribed' ) {
	    $email_struct_single 			= new StdClass();
		$email_struct_single->email 	= $email;

	    $data = array(
	    	'apikey'            => MI_MC_API,
	    	'id'                => MI_MC_SINGLE,
	    	'email' 			=> $email_struct_single,
	    	'merge_vars'        => json_decode( $merges ),
	        'email_type'        => 'html',
	        'double_optin'      => false,
	        'update_existing'   => false,
	        'replace_interests' => true,
	        'send_welcome'      => false
	    );

	    $curl = curl_init( 'https://' . $datacenter[ 1 ] . '.api.mailchimp.com/2.0/lists/subscribe' );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$response = curl_exec( $curl );
		$data = json_decode( $response );

		global $wpdb;
		$rs_query_mailchimp = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "mailchimp WHERE email='" . $email . "'" );
		foreach ( $rs_query_mailchimp as $rs_mailchimp ) { }
		$wpdb->delete(
			$wpdb->prefix . 'mailchimp',
			array( 'id_mailchimp' => $rs_mailchimp->id_mailchimp ),
			array( '%s' )
		);
	} else if ( $data->data[ 0 ]->status == 'subscribed' ) {
		global $wpdb;
		$wpdb->delete(
			$wpdb->prefix . 'mailchimp',
			array( 'email' => $email ),
			array( '%s' )
		);
	}
}

function mi_mailchimp_single_unsubscribe()
{
	$datacenter = explode( '-', MI_MC_API );
	$opts_struct = new StdClass();
	$opts_struct->start = 0;
	$opts_struct->limit = 15000;

	$data = array(
		'apikey'	=> MI_MC_API,
		'id'        => MI_MC_DOUBLE,
		'status'	=> 'subscribed',
    	'opts' 		=> array( $opts_struct )
	);

	$curl = curl_init( 'https://' . $datacenter[ 1 ] . '.api.mailchimp.com/2.0/lists/members' );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
	curl_setopt( $curl, CURLOPT_POST, true );
	curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
	$response = curl_exec( $curl );
	$data = json_decode( $response );

	for ( $i=0; $i < $data->total; $i++) {
		$email_struct 			= new StdClass();
		$email_struct->email 	= $data->data[ $i ]->email;

		$data_single = array(
			'apikey'	=> MI_MC_API,
			'id'        => MI_MC_SINGLE,
	    	'emails' 	=> array( $email_struct )
		);

		$curl = curl_init( 'https://' . $datacenter[ 1 ] . '.api.mailchimp.com/2.0/lists/member-info' );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data_single ) );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$response_single = curl_exec( $curl );
		$data_single = json_decode( $response_single );

		if ( $data_single->errors[ 0 ]->code != 232 || $data_single->data[ 0 ]->status == 'subscribed' ) {
			$email_struct_unsubscribe 			= new StdClass();
			$email_struct_unsubscribe->email 	= $data->data[ $i ]->email;

			$data_unsubscribe = array(
				'apikey'		=> MI_MC_API,
				'id'        	=> MI_MC_SINGLE,
		    	'email' 		=> $email_struct_unsubscribe,
		    	'delete_member'	=> true,
		    	'send_goodbye'	=> false,
    			'send_notify'	=> false
			);

			$curl = curl_init( 'https://' . $datacenter[ 1 ] . '.api.mailchimp.com/2.0/lists/unsubscribe' );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data_unsubscribe ) );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			$response_unsubscribe = curl_exec( $curl );
			$data_unsubscribe = json_decode( $response_unsubscribe );

			// echo '<br><br><br><pre>';
			// print_r( $data_unsubscribe );
			// echo '</pre>';
		}
	}
}

if ( !wp_next_scheduled( 'mi_mailchimp_cron' ) ) {
	wp_schedule_event( time(), 'daily', 'mi_mailchimp_cron' );
}

add_action( 'mi_mailchimp_cron', 'mi_mailchimp_tasks' );

function mi_mailchimp_tasks() {
	$table_exists = get_option( MI_PREFIX . 'mailchimp_exists' );
	if ( $table_exists ) {
		mi_mailchimp_compare();
		mi_mailchimp_single_unsubscribe();
	}
}
?>
