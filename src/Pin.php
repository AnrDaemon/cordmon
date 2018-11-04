<?php
	namespace cordmon;

	class Pin
	{
		protected static $c = 0;
		public $pin_no;
		public $port_no;
		public $device_no;
		public $bitmask;
		public $unit_no;
		public $port_value;
		public $call_file;
		public $status;
		public $previous_status;
		public $in_call;
		public $last_call = 0;

		public function __construct($building, $pin_no, $port_no, $device_no)
		{
			$log = new Logger($building->settings->logs);
			$log->write("for unit {$building->settings->units[self::$c]}\n", 0);
			$this->pin_no = $pin_no;
			$this->port_no = $port_no;
			$this->device_no = $device_no;
			$this->unit_no = $building->settings->units[self::$c];
			$building->units[self::$c] = $this;
			$this->do_counter();
			$this->set_bitmask($this->pin_no);
		}

		public function do_counter()
			{
			 self::$c++;
		}

		public function set_bitmask($pin_no)
		{
			$this->bitmask = pow(2, $pin_no);
		}

		public function get_status()
		{
			$this->previous_status = $this->status;
			$this->status = $this->bitmask & $this->port_value;
			return($this->status);
		}

		public function detect_change()
		{
			$this->get_status();
			if ($this->previous_status === $this->status)
			{
				return(0);
			}
			else
			{
				return(1);
			}
		}
	}
