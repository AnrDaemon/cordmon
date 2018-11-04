<?php
	namespace cordmon;

	require_once dirname(__DIR__) . '/vendor/autoload.php';

	//ob_implicit_flush();

	set_time_limit(0);

	$settings = new BuildingSettings;

	$building = new Building($settings);

	$log = new Logger($building->settings->logs);

	$client = new AMIClient;

	$client->login($settings);


	$d = 0;

	// UI VARS
	// How many times to blink after a change
	$blink_qty = 12;

	$row = 0;

	$m = 1;

	$c = [];

	while(1)
	{
		$d++;
		foreach ($building->units as $unit)
		{
			// If it's the 1st pin, get the port value
			if ($unit->pin_no == 0)
			{
				$building->device[$unit->device_no]->do_port_query($unit->port_no);
			}


			// See if status has changed
			if ($unit->detect_change())
			{
				// Log changes
				$building->log[] = $log->write(date("H:i:s Y-m-d") . ":  Change Deteted on Unit No:  {$unit->unit_no}...\nDevice\t\tPort\t\tPin\t\tOld Val\t\tNew Val\n{$unit->device_no}\t\t{$unit->port_no}\t\t{$unit->pin_no}\t\t{$unit->previous_status}\t\t{$unit->status}\n", 0);

				// If it's an emergency, make call
				if ($unit->previous_status > $unit->status)
				{
					// Temp for testing
					if ($unit->unit_no == 99)
					{
						$client->originate($unit->unit_no);
					}
				}

				// If status changed to non-emergency, check the channel, and clear it if necessary
				elseif ($unit->previous_status < $unit->status && $unit->previous_status)
				{
					$client->hangup($unit->unit_no);
				}
			}


			// Output to Display

			// Keep a list of recently changed units

			if ($unit->previous_status != $unit->status || ! isset($unit->previous_status))
			{
				$blinkers[$unit->unit_no] = $blink_qty;
			}

			// Keep track of pulled courds
			if ($unit->status == 0)
			{
				$c[] = array("$unit->unit_no" => 1);
			}
			else
			{
					unset($c["$unit->unit_no"]);
			}

			// Make Header if row # is 0
			if ($row == 0)
			{
				echo $building->settings->name . "\t" . date("H:m:s") . "\r\n";
				for ($i = 0; $i < 10; $i++)
				{
					echo ("Unit Addr Val\t");
					if ($i == 9)
					{
						echo ("\r\n");
					}
				}
				echo "==============================================================================================================================================================\r\n\r\n";
			}
			// ... header done


			// Make a row in the main output
			// If the unit is set to blink, and it's an odd numbered pass...

			if ($blinkers[$unit->unit_no] % 2 == 1)
			{
				echo "\033[1;37m";  // ... bright white
			}
			elseif ($unit->status)
			{
				// Otherwise, green if untriggered...
				echo "\033[0;32m";
			}
			else  // ... and red if triggered
			{
				echo "\033[0;31m";
			}


			// Print row to screen
			echo ($unit->unit_no . ": " . $unit->device_no . '/' . $unit->port_no . '/' . $unit->pin_no . ' ' . $unit->status . " \t");

			// back to grey (default) console color
			echo "\033[0;37m";

			// increment row before deciding to newline, or 0 % X always is 0, thus true 1st pass
			$row++;

			// new line
			if ($row % 10 == 0 && $row != 0)
			{
				echo ("\r\n\r\n");
			}
			// ... row done.



			// decrement remaining blinks for this $unit
			if ($blinkers[$unit->unit_no] > 0)
			{
				$blinkers[$unit->unit_no]--;
			}

		}

		echo "\r\n======================================================================= LAST 4 CHANGES ========================================================================\r\n";
		for ($i = count($building->log) - 4; $i < count($building->log); $i++)
		{
			echo ($building->log[$i]) . "\n";
		}

		// Make footer...

		// Alternate blinking a "###" per pass
		$m = ! $m;
		echo count($c) . " cords pulled.  Processing... ";
		if (! $m)
		{
			echo "###";
		}
		// ...footer done

		// Reset or increment counters
		$c = NULL;

		$row = 0;

		// Display for 0.25 s and clear screen


		usleep(250000);
		system("clear");
	}
