<?php
	namespace cordmon;

	class BuildingSettings
	{
		public $name = "Roseville";

		public $address = "1 Foo St";
			
		public $subnet = "192.168.1.";
		
		public $units = array
		(
			501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 301, 302, 303, 304, 305,
			306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214,
			215, 216, 217, 218, 219, 220, 221, 222, 223, 224, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 99
		);
		
		public $install_dir = "/srv/cordmon/"; //add trailing '/'
		
		public $log_file = "logs/cordmon.log";
		public $error_log_file = "logs/cordmon_error.log";

		public $devices = array
		(
			array
			(
				"ip" => "216",
				"tcp_port" => '7152',
				"port_mask" => 31,
				"ports" => array
				(
				// Defaults to enabling all pins, or you can enable certain pins, e.g. the first 7 pins on port 2, do:
				// "2" => array("pin_mask" => 127)
				)
			),
			array
			(
				"ip" => "217",
				"tcp_port" => '7152',
				"port_mask" => 31,
				"ports" => array()
			),
			array
			(
				"ip" => "218",
				"tcp_port" => '7152',
				"port_mask" => 63,
				"ports" => array()
			)			
		);
	
		public $AMIClient = array
		(
			"ip" => "127.0.0.1",
			"tcp_port" => "5038",
			"username" => "cordmon",
			"secret" => "cordmon1S8S"
		);
		
		public $gui_stream = 'tcp://127.0.0.1:5353';
		
		
		public function __construct()
		{
				$this->logs = array("log" => $this->install_dir . $this->log_file, "eroor_log" => $this->install_dir . $this->error_log_file);
		}
	}
	
?>