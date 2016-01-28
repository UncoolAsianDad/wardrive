<?php

$filename = $argv[1];

$location = basename($filename, ".csv");


$f = file("vandrico.csv");

$keys = split(",", trim($f[0]));
for ($i = 1; $i< sizeof($f); $i++) {
	$line = $f[$i];
	$elements = split(",",trim($line));
	
	$obj = new stdClass();
	for ($j = 0; $j < sizeof($keys); $j++) {
		$obj->$keys[$j] = $elements[$j];		
	}

	$obj->location = $location;

	$jsonObj = json_encode($obj);

	$request = curl_init( 'http://canary1.vandrico.com:9200/proximity/ap' );
	curl_setopt( $request, CURLOPT_POST, true ); // use POST
	curl_setopt( $request, CURLOPT_POSTFIELDS, $jsonObj);
	curl_setopt( $request, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($jsonObj))
	);
	$response = curl_exec( $request );

	print($obj->SSID).PHP_EOL;
}
