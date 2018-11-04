<?php
	namespace cordmon;

	class GUISocket
	{
		public function __construct($settings)
		{
			$this->stream = $settings->gui_stream;
		}

		public function create_server()
		{
			$this->server = stream_socket_server($this->stream);
			return $this->server;
		}

		public function create_client()
		{
			$this->socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
			return $this->socket;
		}

		public function SetClient($client)
		{
			$this->client = $client;
		}

		public function select($read, $write, $except)
		{

			// $this->conn = stream_socket_accept($this->socket, 0) or die("Could not accept incoming connection\n");
			// stream_set_blocking($conn, FALSE);
			return(stream_select($read, $write, $except, 1));
		}

		public function accept()
		{
			$this->conn = stream_socket_accept($this->socket, 0) or die("Could not accept incoming connection\n");
			// stream_set_blocking($conn, FALSE);
			return($this->conn);
		}

		public function connect()
		{
			$this->conn = stream_socket_client("tcp://" . $this->host . ":" . $this->port, $errno, $errstr) or die("Failed to connect to server\n");
			return $this->conn;
		}

		public function get()
		{
			$this->data = stream_get_line($this->client, 4096, "\r\n\r\n");
			return $this->data;
		}

		public function write($statuses)
		{
			fputs($this->socket, $statuses);
		}
	}
