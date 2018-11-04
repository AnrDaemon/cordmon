<?php
	namespace cordmon;

	class Logger
	{

		public function __construct($logs)
		{
			$this->log_file = fopen($logs["log"], "a");
		}

		public function write($text, $error)
		{
			if ($error)
			{
				error_log($text, 3, $logs["error_log"]);
				echo $text;
				return $text;
			}
			else
			{
				fwrite($this->log_file, $text);
				echo $text;
				return $text;
			}
		}
	}
