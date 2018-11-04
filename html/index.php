<?php
	namespace cordmon;

	require_once dirname(__DIR__) . '/vendor/autoload.php';

	ob_implicit_flush();

	$settings = new BuildingSettings;  // exception, class not found

	$server = new GUISocket($settings);

	$server = $server->create_server();


	if (false === $server)
	{
		throw new Exception('Could not listen');
	}

	$server->SetClient(stream_socket_accept($server));
	if (false !== $server->client)
	{
		while(1)
		{
			$data = $server->get($server->client);
			echo $data . "\n";
			sleep(1);
		}
	}



	/*
	include "/srv/cordmon/src/Socket.php";
	// set some variables
	$host = "127.0.0.1";
	$port = 25003;
	// don't timeout!
	set_time_limit(0);
	// create socket
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
	// bind socket to port
	$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
	// start listening for connections
	$result = socket_listen($socket, 3) or die("Could not set up socket listener\n");

	// accept incoming connections
	// spawn another socket to handle communication
	$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
	// read client input
	while (1)
	{
		$input = socket_read($spawn, 1024) or die("Could not read input\n");
		// clean up input string
		$input = trim($input);
		echo "Client Message : ".$input . "\n";
		// reverse client input and send back
		$output = strrev($input) . "\n";
		socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
		sleep(3);
	}
	// close sockets
	socket_close($spawn);
	socket_close($socket);
	*/
