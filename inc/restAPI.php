<?php
$headers = array (
	'Authorization' => 'Basic ' . base64_encode( 'admin' . ':' . 'ignazionet' ),
);
$url = rest_url( 'wp/v2/posts/1' );

$data = array(
	'username' => 'admin',
	'password' => 'ignazionet',
	'title' => 'Hello Gaia' 
);

$response = wp_remote_post( $url, array(
	'method' => 'POST',
	'timeout' => 45,
	'redirection' => 5,
	'httpversion' => '1.0',
	'blocking' => true,
	'headers' => array(),
	'body' => $data,
	'cookies' => array()
    )
);

if ( is_wp_error( $response ) ) {
   $error_message = $response->get_error_message();
   echo "Something went wrong: $error_message";
} else {
   echo 'Response:<pre>';
   print_r( $response );
   echo '</pre>';
}
die();