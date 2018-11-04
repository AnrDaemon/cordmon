<?php
	namespace cordmon;

	class Building
	{
		public $log = [];

		public $calls = [];

		public function __construct($settings)
		{
			$this->settings = $settings;
			$this->subnet = $this->settings->subnet;

			$log = new Logger($settings->logs);

			for ($i = 0; $i < count($settings->devices); $i++)
			{
				$log->write("Setting up device " . $i . "\n", 0);
				$this->device[$i] = new Device($this, $i);
			}
			sleep(1);
		}

	}
