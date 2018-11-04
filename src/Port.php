<?php
	namespace cordmon;

	class Port
	{
		public $pin_count = array(8, 8, 8, 8, 1, 1);
		public $port_no;
		public $device_no;
		public $pin_qty;
		public $port_value;

		public function __construct($building, $port_no, $device_no)
		{
			$this->logs = $building->settings->logs;
			$log = new Logger($this->logs);
			$this->device_no = $device_no;
			$this->port_no = $port_no;
			if (isset($building->settings->devices[$device_no]["ports"][$port_no]["pin_mask"]))
			{	
				$this->pin_mask = $building->settings->devices[$device_no]["ports"][$port_no]["pin_mask"];
			}
			elseif ($port_no < 4)
			{
				$this->pin_mask = 255;
				$this->pin_count = 8;
			}
			else
			{
				$this->pin_mask = 1;
				$this->pin_count = 1;
			}
			for ($i = 0; $i < $this->pin_count; $i++)
			{
				if (pow(2, $i) & $this->pin_mask)
				{
					$log->write("Setting up pin {$i} ", 0);
					$this->pin[$i] = new Pin($building, $i, $port_no, $device_no);
				}
				else
				{
					$log->write("Skipping pin {$i}.\n", 0);
				}
			}
		}

	}
?>
