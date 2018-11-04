<?php
	namespace cordmon;

	class AMIClient
	{
		public $response = [];

		public function login($settings)
		{
			$log = new Logger($settings->logs);
			$log->write("Logging into AMI...\r\n", 0);
			$this->socket = @fsockopen($settings->AMICLient->ip,$settings->AMICLient->tcp_port, $errno, $errstr, 1);
			if (!$this->socket)
			{
				$log->write($this->error, 1);
				return(FALSE);
			}
			else
			{
					stream_set_timeout($this->socket, 1);
				$response = $this->query
				(
					"Action: Login\r\n" .
					"UserName: $this->username\r\n" .
					"Secret: $this->secret\r\n\r\n"
				);
				if ($response[0]['Message'] == "Authentication accepted")
				{
						return(TRUE);
				}
				else
				{
					$this->error = "Could not login - Authentication failed";
						$log->write($this->error, 0);
					fclose($this->socket);
						$this->socket = FALSE;
					return(FALSE);
				}

			}
		}

		public function show_channels()
		{
			stream_set_timeout($this->socket, 2);

			$response = $this->query
			(
				"ACTION: CoreShowChannels\r\n" .
				"ACTIONID: " . 999 .  "\r\n\r\n"
			);
			$this->response = $response;
			return($response);
		}

		public function hangup($extension)
		{
			$response = $this->show_channels();
			if ($channel = $this->get_sip_channel($extension, $response))
			{
				$response = $this->query
				(
					"Action: Hangup\r\n" .
					"ActionID: " . 999 . "\r\n" .
					"Channel: " . $channel . "\r\n" .
					"Cause: Cord State Change\r\n\r\n"
				);
			}
			else
			{
				return FALSE;
			}
		}

		public function originate($extension)
		{
			stream_set_timeout($this->socket, 2);

			$response = $this->query
			(
				"Action: Originate\r\n" .
				"Channel: sip/" . $extension . "\r\n" .
				"Exten: 1000\r\n" .
				"Context: emergency_call\r\n" .
				"Priority: 1\r\n\r\n"
			);
			return($response);
		}

		public function query($query)
		{

				if ($this->socket === FALSE)
			{
					return(FALSE);
				}

			$c = 0;

			fputs($this->socket, $query);
			while(1)
				{
				$info = stream_get_meta_data($this->socket);
					$line = fgets($this->socket, 4096);
				if ($line == "\r\n")
				{
					$c++;
					continue;
				}
				if ($line == NULL || $info['timed_out'])
				{
					break;
				}
					$key_value = explode(": ", $line);
				if (isset($key_value[1]))
				{
					$response[$c][$key_value[0]] = str_replace(array("\r", "\n"), '', $key_value[1]);
				}
				else
				{
					$response[$c][str_replace(array("\r", "\n"), '', $key_value[0])] = str_replace(array("\r", "\n"), '', $key_value[0]);
				}
			}

			return($response);
		}

		public function get_sip_channel($extension, $response)
		{
			foreach ($response as $message)
			{
				if (isset($message["ActionID"]) && isset($message['Event']))
				{
					if ($message["ActionID"] == 999 && $message["Event"] == "CoreShowChannel")
					{
						if (strpos(Smessage["Channel"], "sip/" . $extension))
						{
							return $message["Channel"];
						}
					}
				}
			}
			return FALSE;
		}
	}

?>
