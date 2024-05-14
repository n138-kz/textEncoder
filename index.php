<?php
date_default_timezone_set('UTC');
session_name('token'.date('Ymd-H'));
session_start();

$system = [
	'hash_aglos' => 'sha1',
];
$result = [];
$result += [ 'issueat' => time() ];
$result += [ 'client' => [
	'ipaddress' => $_SERVER['REMOTE_ADDR'],
	'portnum' => $_SERVER['REMOTE_PORT'],
	'ua' => $_SERVER['HTTP_USER_AGENT'],
	'hostbyaddr' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
	'inet_pton' => inet_pton($_SERVER['REMOTE_ADDR']),
	'ip2long' => ip2long($_SERVER['REMOTE_ADDR']),
] ];
$result += [ 'request' => [
	'uri' => $_SERVER['REQUEST_URI'],
	'query' => $_SERVER['QUERY_STRING'],
	'method' => mb_strtolower($_SERVER['REQUEST_METHOD']),
	'uuid' => hash($system['hash_aglos'], $result['issueat']) . '_' . $_SERVER['UNIQUE_ID'],
] ];

header('Content-Type: application/json');
if ( ! in_array( $result['request']['method'], ['get'] ) ) { 
	$result += [ 'erroron' => __LINE__ ];
	echo json_encode($result);
	exit();
}
if ( ! isset($_REQUEST) ) {
	$result += [ 'erroron' => __LINE__ ];
	echo json_encode($result);
	exit();
}
if ( ! isset($_REQUEST['q']) ) {
	$result += [ 'erroron' => __LINE__ ];
	echo json_encode($result);
	exit();
}
$result['request'] = array_merge($result['request'], [ 'text' => $_REQUEST['q'] ]);
$result += [ 'result' => [ 'text' => $_REQUEST['q'] ] ];

if ( strpos($_REQUEST['q'], 'https://x.com/') !== FALSE ) {
	$result['result'] = array_merge(
		$result['result'], ['text' => str_replace( 'https://x.com/', 'https://fxtwitter.com/', $_REQUEST['q'] )],
	);
}
if ( strpos($_REQUEST['q'], 'https://twitter.com/') !== FALSE ) {
	$result['result'] = array_merge(
		$result['result'], ['text' => str_replace( 'https://twitter.com/', 'https://fxtwitter.com/', $_REQUEST['q'] )],
	);
}
if ( strpos($_REQUEST['q'], 'https://vwitter.com/') !== FALSE ) {
	$result['result'] = array_merge(
		$result['result'], ['text' => str_replace( 'https://twitter.com/', 'https://fxtwitter.com/', $_REQUEST['q'] )],
	);
}

//$result['result'] = array_merge($result['result'], [ 'curl_result' => file_get_contents($result['result']['text']) ]);

echo json_encode($result);

