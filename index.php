<?php
date_default_timezone_set('UTC');
session_name('token'.date('Ymd-H'));
session_start();

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
	'method' => $_SERVER['REQUEST_METHOD'],
	'uuid' => hash('sha1', $result['issueat']) . '_' . $_SERVER['UNIQUE_ID'],
] ];

header('Content-Type: application/json');
echo json_encode($result);

