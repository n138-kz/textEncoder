<?php
date_default_timezone_set('UTC');
session_name('token'.date('Ymd-H'));
session_start();

function is_empty($arg) {
	return ( !isset($arg) || empty($arg) );
}
function push2discord($endpoint, $content_author='Webhooks', $content_author_avatar='https://www.google.com/s2/favicons?size=256&domain=https://discord.com/', $content_color=0, $content_body=''){
	if ( is_empty( $endpoint ) ) { return false; }
	$content_color = is_numeric($content_color) ? $content_color : 0;
	$content_color = $content_color > 0 ? $content_color : 0;

	$payload = [];
	$payload += [
		'username' => $content_author,
		'content' => chr(0),
		'avatar_url' => $content_author_avatar,
		'embeds' => [],
	];
	array_push($payload['embeds'], [
		'color' => $content_color,
		'timestamp' => date('c'),
		'footer' => [
		'text' => 'Auth-Google'
		],
		'fields' => [
		[
			'inline' => false,
			'name' => '',
			'value' => $content_body
		]
		]
	]);
	$payload_encoded = json_encode($payload);
	$curl_req = curl_init($endpoint);
	curl_setopt($curl_req,CURLOPT_POST, TRUE);
	curl_setopt($curl_req,CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	curl_setopt($curl_req,CURLOPT_POSTFIELDS, $payload_encoded);
	curl_setopt($curl_req,CURLOPT_SSL_VERIFYPEER, TRUE);
	curl_setopt($curl_req,CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($curl_req,CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl_req,CURLOPT_FOLLOWLOCATION, TRUE);
	$curl_res=curl_exec($curl_req);
	$curl_res=json_decode($curl_res, TRUE);
	return $curl_res;
}

$system = [
	'hash_aglos' => 'sha1',
];
$config = dirname(__FILE__) . '/' . 'config.json';
if ( file_exists($config) && filesize($config) > 0 ) {
	try {
		$config = json_decode(file_get_contents($config), true);
	} catch (\Exception $e) {
		unset($config);
	}
}

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

	if ($config['external']['discord']['activate']['notice']) {
		push2discord(
			$config['external']['discord']['uri']['notice'],
			$config['external']['discord']['authorname']['notice'],
			$config['external']['discord']['authoravatar']['notice'],
			$config['external']['discord']['color']['notice'],
			'time: ' . chr(9) . time() . PHP_EOL.
			'```json' . PHP_EOL.
			json_encode([ $result, ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES ) . PHP_EOL.
			'```' . PHP_EOL
		);
	}
	
	echo json_encode($result);
	exit();
}
if ( ! isset($_REQUEST) ) {
	$result += [ 'erroron' => __LINE__ ];

	if ($config['external']['discord']['activate']['notice']) {
		push2discord(
			$config['external']['discord']['uri']['notice'],
			$config['external']['discord']['authorname']['notice'],
			$config['external']['discord']['authoravatar']['notice'],
			$config['external']['discord']['color']['notice'],
			'time: ' . chr(9) . time() . PHP_EOL.
			'```json' . PHP_EOL.
			json_encode([ $result, ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES ) . PHP_EOL.
			'```' . PHP_EOL
		);
	}
	
	echo json_encode($result);
	exit();
}
if ( ! isset($_REQUEST['q']) ) {
	$result += [ 'erroron' => __LINE__ ];

	if ($config['external']['discord']['activate']['notice']) {
		push2discord(
			$config['external']['discord']['uri']['notice'],
			$config['external']['discord']['authorname']['notice'],
			$config['external']['discord']['authoravatar']['notice'],
			$config['external']['discord']['color']['notice'],
			'time: ' . chr(9) . time() . PHP_EOL.
			'```json' . PHP_EOL.
			json_encode([ $result, ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES ) . PHP_EOL.
			'```' . PHP_EOL
		);
	}
	
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

if ($config['external']['discord']['activate']['notice']) {
	push2discord(
		$config['external']['discord']['uri']['notice'],
		$config['external']['discord']['authorname']['notice'],
		$config['external']['discord']['authoravatar']['notice'],
		$config['external']['discord']['color']['notice'],
		'time: ' . chr(9) . time() . PHP_EOL.
		'```json' . PHP_EOL.
		json_encode([ $result, ], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES ) . PHP_EOL.
		'```' . PHP_EOL
	);

	$endpoint = $config['external']['discord']['uri']['notice'];
	$result_json = '/tmp/' . time() . '.json';
	file_put_contents($result_json, json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
	$payload_postdata = [
		'content' => 'json files - ' . $result_json,
		'tts' => 'false',
		'file' => curl_file_create($result_json, 'application/json', $result_json),
	];
	$curl_req = curl_init($endpoint);
	curl_setopt($curl_req,CURLOPT_POST, TRUE);
	curl_setopt($curl_req,CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
	curl_setopt($curl_req,CURLOPT_POSTFIELDS, $payload_postdata);
	curl_setopt($curl_req,CURLOPT_SSL_VERIFYPEER, TRUE);
	curl_setopt($curl_req,CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($curl_req,CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl_req,CURLOPT_FOLLOWLOCATION, TRUE);
	$curl_res=curl_exec($curl_req);
	$curl_res=json_decode($curl_res, TRUE);

}

echo json_encode($result);
