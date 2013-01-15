<?php
 /**
 * piStrap Class
 *
 * @category PHP based Pi Statistics
 * @author Alex Ward <alex@indiescope.co.uk>
 * @website http://www.indiescope.co.uk
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @version 0.1
 **/
class piStrap {
		
		/**
		* Grab general Pi stats.
		* ----------------------
		* @return multidimensional array - Contains general pi stats.
		*/
		public function generalStats(){
			//Execute command to retrieve system info
		 	$general = shell_exec("uname -a");
			
			//Execute command to retrieve system time
			$sysTime = shell_exec("date +'%d %m %Y %T %Z'");
			
			//Split returned string at " " into an array
			$general = explode(" ", $general);  
			
			//Split returned string at " " into an array
			$sysTime = explode(" ", $sysTime);  
			
			//Return times in array
			$sysTime = array("day" => $sysTime[0], "month" => $sysTime[1], "year" => $sysTime[2], "curTime" => $sysTime[3], "zone" => $sysTime[4]);
	
			//Return values in array
			return array("kernel" => $general[0], "hostname" => $general[1], "kernelRelease" => $general[2], "processor" => $general[11], "OS" => $general[12], "sysTime" => $sysTime);
		}
		
		/**
		* Grab Pi uptime stats.
		* ---------------------
		* @return array - Contains pi uptime split into years/days/hours/mins/secs.
		*/
		public function uptimeStats(){
			//Execute command to retrieve uptime
		 	$uptime = shell_exec("cat /proc/uptime");
			
			//Split returned string at " " into an array to give 2 objects. 1 - uptime of the system (s), 2 - amount of time spent in idle process (s)
			$uptime = explode(" ", $uptime);   
			
			//Pass to convertTime function to convert seconds into years, days, hours, minutes, seconds.
		    $uptime = $this->convertTime($uptime[0]);
			
			//Return converted times in array
			return $uptime;
		}
		
		/**
		* Converts seconds into years/days/hours/mins/secs.
		* -------------------------------------------------
		* @return array - Contains separated uptime values.
		*/
		private function convertTime($seconds){
			$year = floor($seconds / 60/60/24/365);
			$day = floor($seconds/60/60/24) % 365;
			$hour = floor(($seconds / 3600) % 24);
			$min = floor(($seconds / 60) % 60);
			$sec = $seconds % 60;
			
			return array('year' => $year, 'day' => $day, 'hour' => $hour, 'min' => $min, 'sec' => $sec);
		}
		
		/**
		* Grab Pi CPU stats.
		* ------------------
		* @return array - Contains pi CPU stats.
		*/
		public function cpuStats(){
			//Returns three samples representing the average system load over last 1, 5, 15 mins
			$rawLoad = sys_getloadavg();
			
			//Pass to avgLoad() funtion to calculate average from raw load data
			$avgLoad = $this->avgLoad($rawLoad);
			
			//Grab cpu scaling vars, min, max & current. Divide by 1000 to get value in MHz
			$curFreq = round(shell_exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq") / 1000);
			$minFreq = round(shell_exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_min_freq") / 1000);
			$maxFreq = round(shell_exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_max_freq") / 1000);
			
			//Grab cpu scaling governor
			$freqGov = shell_exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_governor");
			
			//Return values in array
			return array('avgLoad' => $avgLoad, 'curFreq' => $curFreq, 'minFreq' => $minFreq, 'maxFreq' => $maxFreq, 'freqGov' => $freqGov);
		}
		
		/**
		* Calculate average CPU load.
		* ---------------------------
		* @param array - Containing multiple CPU load snapshots.
		* @return value - Average CPU load.
		*/
		private function avgLoad($load){
			//Sum of 3 values
			$sum = array_sum($load);
			
			//Divide sum by 3 to work out average
			$out = round($sum / 3, 2);
			
			return $out;
		}
		
		/**
		* Grab Pi RAM stats.
		* ------------------
		* @param boolean - True if swap is enabled on pi / False if disabled
		* @return multidimensional array - Contains pi RAM stats.
		*/
		public function ramStats($swap = false){
			//If swap is enabled
			if($swap)
			{
				//Return multi-dimensional array containg memStats() and swpStats()
				return array('memArray' => $this->memStats(), 'swpArray' => $this->swpStats());
			}
			//If swap is default (swap is bad for your sd card -sad face-)
			else
			{
				//Return array containg memStats()
				return array('memArray' => $this->memStats());
			}
		}
		
		/**
		* Grab Pi mem stats.
		* -------------------
		* @return array - Contains pi mem stats.
		*/
		private function memStats(){
			//Execute /proc/meminfo command
		    $free = shell_exec('cat /proc/meminfo');

			//Extract values only from mem entry
		    preg_match_all('/\s+([0-9]+)/', $free, $match);

			//Assign values to list of vars
		    list($total, $free, $buffers, $cached) = $match[1];
			
			//Calculate free mem
			$used = $total - $free;
			
			//Calculate percentage of ram currently in use 
			$percentageUsed = ($used / $total) * 100;
			
			//Calculate percentage of ram currently free
			$percentageFree = ($free / $total) * 100;
			
			//Return array containing values
			return array('memTotal' => $total, 'memUsed' => $used, 'memFree' => $free, 'memBuf' => $buffers, 'memCache' => $cached, 'memPercentUsed' => $percentageUsed, 'memPercentFree' => $percentageFree);
		}
		
		/**
		* Grab Pi swap stats.
		* -------------------
		* @return array - Contains pi swap stats. (if $swap = true).
		*/
		private function swpStats(){
			//Execute free-mo command
		    exec('free -mo', $free);
			
			//Extract values only from swap entry
		    preg_match_all('/\s+([0-9]+)/', $free[2], $matches);
			
			//Assign values to list of vars
		    list($total, $used, $free, $shared, $buffers, $cached) = $matches[1];
			
			//Calculate percentage of ram currently in use 
			$percentage = round($used / $total * 100, 2);
			
			//Return array containing values
			return array('swpTotal' => $total, 'swpUsed' => $used, 'swpFree' => $free, 'swpPercent' => $percentage);
		}
		
		/**
		* Grab Pi cpu temperature stats.
		* ------------------------------
		* @param value - Set max CPU temp. (Default 85).
		* @return array - Contains pi CPU temperature stats.
		*/
		public function tempStats($maxTemp = 85){
			//Execute command to retrieve current cpu temp
			$temp = shell_exec("cat /sys/class/thermal/thermal_zone0/temp");
			
			//Convert temp to celcius by dividing by 1000
			$tempC = round($temp / 1000, 2);
			
			//Pass to celcToFare() function to convert celcius to Fahrenheit
			$tempF = round($this->celcToFare($tempC, 2));
			
			//Calculate percentage of max temp
			$percentageC = round($tempC / $maxTemp * 100);
			
			//Calculate percentage left
			$percentRemain = 100 - $percentageC;
			
			//Return array containing values
			return array('maxTempC' => $maxTemp, 'tempC' => $tempC, 'tempF' => $tempF, 'percentageC' => $percentageC, 'percentRemain' => $percentRemain);
		}	
		
		/**
		* Convert degrees C to Fahrenheit.
		*
		* @param value - CPU temperature degrees C
		* @return multidimensional array - Contains general pi stats.
		*/
		private function celcToFare($tempC) {
			//Celcius to Fahrenheit conversion
			return((9 / 5) * $tempC + 32);
		}
		
		/**
		* Grab Pi HDD stats.
		* ------------------
		* @return multidimensional array - Contains pi HDD stats.
		*/
		function hddStats(){
			//Execute command to retrieve drives
			exec('df -T -l -BM -x tmpfs -x devtmpfs -x rootfs', $drives);
			
			//Remove whitespace
			$drives = preg_replace('/( )+/', ' ', $drives);

			//Create vars
			$i = 0;
		
			//Loop through drives
			foreach($drives as $drive) 
				{					
					//Explode into array at " "
					$drive = explode(" ", $drives[$i]);
					
					//Assign values to list of vars
					list($fileSys, $type, $size, $used, $available, $percentage, $mountedOn) = $drive;
					
					//Output associated array containing values
					$out[$i] = array('fileSys' => $fileSys,'type' => $type,'size' => $size,'used' => $used,'available' => $available, 'percentageUsed' => $percentage,'mountedOn' => $mountedOn);
					
					//Remove 1st array containing text headers
					if($i == 0)
						{
							unset($out[0]);
						}
					$i++;
				} 		
			
			//Return array containing values
			return $out;
		}
		
		/**
		* Grab Pi user stats.
		* -------------------
		* @return multidimensional array - Contains array for each user logged onto pi.
		*/
		function userStats() {
			//Execute command to retrieve logged in users
			$who = shell_exec("who");	
			
			//Create vars
			$i = 0;
			$array = array();
				
			//Split lines into individual array elements
			$users = explode ("\n", $who);
			
			//Loop through users
			foreach ($users as $user)
				{
					//Replace whitespace spaces with single space
					$user = preg_replace("/ +/", " ", $user);
					
					//Now split user into multiple values
					$value = explode(" ", $user);
							
					//Output associated array containing values
					$out[$i] = array('user' => $value[0], 'IP' => $value[5], 'since' => $value[4]);
							
					//Add array into multi-dimensional array
					array_push($array, $out[$i]);
					$i++;
				}
		
			//If don't have users logged in
			if($i <= 1)
				{
					//No users logged in so return false
					return false;
				}
			
			//We have users logged in
			else
				{
					//Return array containing values
					return $array;
				}
		}
	
	//End of class
	}
?>
	
