<?php
	namespace cordmon;


	class Device
	{
		public function __construct($building, $device_no)
		{
			$this->logs = $building->settings->logs;
			$log = new Logger($this->logs);
			$this->device_no = $device_no;
			$this->ip = $building->settings->subnet . $building->settings->devices[$device_no]["ip"];
			$this->tcp_port = $building->settings->devices[$device_no]["tcp_port"];
			$this->port_mask = $building->settings->devices[$device_no]["port_mask"];
			$this->create_socket();
			$this->connect_socket();
			$this->eth32_reset();
			
			for ($i = 0; $i < 6; $i++)
			{
				if (pow(2, $i) & $this->port_mask)
				{
					$log->write("Setting up port " . $i . "\n", 0);
					$this->port[$i] = new Port($building, $i, $device_no);
					$this->eth32_port_enable($this->port[$i]->port_no, $this->port[$i]->pin_mask);
				}
				else
				{
					$log->write("Skipping port " . $i . "\n", 0);
				}
				
			}
		}

		public function create_socket()
			{
			$log = new Logger($this->logs);
				$log->write("Creating socket for Eth32... ", 0);
				$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
				if($this->socket === false)
			{
				$log->write("socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n", 1);
			}
			else
			{
						$log->write("OK.\n", 0);
			}
		}

		public function connect_socket()
		{
			$log = new Logger($this->logs);
			$log->write("Connecting to ETH32 on $this->ip... ", 0);
			$result = socket_connect($this->socket, $this->ip, $this->tcp_port);
			if ($result === false)
			{
				$log->write("socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($this->socket)) . "\n", 1);
			}
			else
			{
				$log->write("OK.\n", 0);
			}
		}

		public function eth32_reset()
		{
			$log = new Logger($this->logs);
			$log->write("Resetting Ports... ", 0);
			$cmd = chr(26) . chr(0) . chr(0) . chr(0) . chr(0);
			$result=socket_write($this->socket, $cmd, strlen($cmd));
			if ($result === false)
			{
				$log->write("socket_write() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n", 1);
			}
			else
			{
					$log->write("OK.\n", 0);
			}
		}

		public function eth32_port_enable($port_no, $pin_mask)
		{
			$log = new Logger($this->logs);
			$log->write("Enabling Pull-Up Resistors on port {$port_no}... ", 0);
			$cmd = chr(2) . chr($port_no) . chr($pin_mask) . chr(0) . chr (0);
			$result = socket_write($this->socket, $cmd, strlen($cmd));
			if ($result === false)
			{
				$log->write("socket_write() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n", 1);
			}
			else
			{
				$log->write("OK.\n", 0);
			}
		}

		public function send_port_query($port_no)
		{
			$log = new Logger($this->logs);
				$cmd = chr(3) . chr(0) . chr($port_no) . chr(0) . chr(0);
				$result = socket_write($this->socket, $cmd, strlen($cmd));				
				if ($result === false)
				{
					$log->write("socket_write() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n", 1);
				}
		}

		public function receive_query_response()
		{
			$log = new Logger($this->logs);
			while (1)
			{
				$result = socket_recv($this->socket, $response, 5, MSG_WAITALL);
				if (ord(substr($response, 0, 1)) != 3)
				{
					continue;
				}
				else
				{
					break;
				}
			}
			if ($result === false)
			{
				$log->write("socket_read() failed.\nReason: ($result) ", 1);
			}
			elseif ($result < 5)
			{
				$log->write("socket_read() did not return a full 5 bytes.\n", 1);
			}
			else
			{
						$this->response = $response;
			}
		}

		public function do_port_query($port_no)
		{
			$this->send_port_query($port_no);
			$this->receive_query_response();
			$this->port[$port_no]->port_value = ord(substr($this->response, 3, 1));
			foreach ($this->port[$port_no]->pin as $pin)
			{
				$pin->port_value = $this->port[$port_no]->port_value;
			}
			return($this->port[$port_no]->port_value);
		}
	}

?>
