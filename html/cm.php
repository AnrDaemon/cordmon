<?php
	namespace cordmon;

	require_once dirname(__DIR__) . '/vendor/autoload.php';

	ob_implicit_flush();

	$client = stream_socket_client("tcp://127.0.0.1:5353", $errno, $errstr, 30);
	if (!$client)
	{
		echo "$errstr ($errno)<br />\n";
	}
	else
	{
		while(1)
		{
			stream_socket_sendto($client, "Hello Server...\t" . mt_rand(0, 1000) . "\t" . date("Y.m.d") . "\t" . date("H:m:s") . "\r\n\r\n");
			//fwrite($client, "Hello Server...\t" . mt_rand(0, 1000) . "\t" . date("Y.m.d") . "\t" . date("H:m:s") . "\r\n\r\n");
			sleep(3);
		}
		fclose($client);
	}

	/*
	$host    = "127.0.0.1";
	$port    = 25003;
	// create socket
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
	// connect to server
	$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");
	// send string to server
	while (1)
	{
		$message = "Hello Server...\t" . date("Y.m.d") . "\t" . date("H:m:s") . "\n";
		echo "Message To server:  ". $message . "\n";
		socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
		// get server response
		$result = socket_read ($socket, 1024);
		echo "Reply From Server:  ".$result . "\n";
		sleep(3);
	}

	// close socket
	socket_close($socket);
	*/
