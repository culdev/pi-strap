<?php 
	//Load piStrap class
	require('lib/_piStrap.php'); 
	
	//Create new instance
	$stats = new piStrap;
	
	//Access data
	$general = $stats->generalStats(); 
	$uptime = $stats->uptimeStats(); 
	$temp = $stats->tempStats();
	$ram = $stats->ramStats();
	$cpu = $stats->cpuStats();
	$user = $stats->userStats();
	$hdd = $stats->hddStats();
	
	//Include our header and off we go!
	require('header.php'); 
?>
<div class="container">
    <div class="row">
		<div class="capsule span4">
			<div class="capsule-inner">
				<h4>CPU Stats:</h4>
				<table>
					<?php 
						echo "<tr><td>Average Load:</td><td>" .$cpu['avgLoad']."</td></tr>";
						echo "<tr><td>Current Freq:</td><td>" .$cpu['curFreq']."MHz</td></tr>";
						echo "<tr><td>Minimum Freq:</td><td>" .$cpu['minFreq']."MHz</td></tr>";
						echo "<tr><td>Maximum Freq:</td><td>" .$cpu['maxFreq']."MHz</td></tr>";
						echo "<tr><td>Freq Governor:</td><td>" .$cpu['freqGov']."</td></tr>";
					?>
				</table>
				<hr>
				<h4>Temperature:</h4>
				<table>
					<?php 
						$tempPercent = $temp['percentageC'];
						$percentRemain = $temp['percentRemain'];
						echo "<tr><td>Maximum Temp:</td><td>" .$temp['maxTempC']."&deg;C</td></tr>";
						echo "<tr><td>Current Temp:</td><td>" .$temp['tempC']."&deg;C</td></tr>";
						echo "<tr><td>Current Temp:</td><td>" .$temp['tempF']."&deg;F</td></tr>";
					?>
				</table>
				<hr>
					<div class="progress">
						<div class="bar bar-primary" style="width: <?php echo $tempPercent; ?>%;"><?php echo $temp['tempC']; ?>&deg;C</div>
						<div class='bar bar-success' style="width: <?php echo $percentRemain; ?>%;"></div>
					</div>
				<hr>
			</div>
			<div class="capsule-inner">
				<h4>RAM Stats:</h4>
				<table>
					<?php 
						foreach($ram as $key => $value) {
							echo "<tr><td>Total Memory:</td><td>" .$ram['memArray']['memTotal']." KB</td></tr>";
							echo "<tr><td>Used Memory:</td><td>" .$ram['memArray']['memUsed']." KB</td></tr>";
							echo "<tr><td>Free Memory:</td><td>" .$ram['memArray']['memFree']." KB</td></tr>";
							echo "<tr><td>Buffer Memory:</td><td>" .$ram['memArray']['memBuf']." KB</td></tr>";
							echo "<tr><td>Cache Memory:</td><td>" .$ram['memArray']['memCache']." KB</td></tr>";
							
							$memPercentUsed = $ram['memArray']['memPercentUsed'];
							$memPercentFree = $ram['memArray']['memPercentFree'];
							$memUsed = $ram['memArray']['memUsed'];
							$memFree = $ram['memArray']['memFree'];
						} 
					?>
				</table>
				<hr>
					<div class="progress">
						<div class="bar bar-primary" style="width: <?php echo $memPercentUsed; ?>%;"><?php echo $memUsed; ?> KB</div>
						<div class="bar bar-success" style="width: <?php echo $memPercentFree; ?>%;"><?php echo $memFree; ?> KB</div>
					</div>
				</p>
				<hr>
			</div>
		</div>
		<div class="capsule span4">
			<div class="capsule-inner">
				<h4>HDD Stats:</h4>
					<?php 
						$i = 1;
						foreach($hdd as $key => $value) {
							echo "<table>";
							echo "<tr><td>File System:</td><td>" .$hdd[$i]['fileSys']."</td></tr>";
							echo "<tr><td>File Type:</td><td>" .$hdd[$i]['type']."</td></tr>";
							echo "<tr><td>HDD Size:</td><td>" .$hdd[$i]['size']."B</td></tr>";
							echo "<tr><td>HDD Used:</td><td>" .$hdd[$i]['used']."B</td></tr>";
							echo "<tr><td>HDD Available:</td><td>" .$hdd[$i]['available']."B</td></tr>";
							echo "<tr><td>HDD Mounted:</td><td>" .$hdd[$i]['mountedOn']."</td></tr>";
							echo "</table>";
							echo "<hr>";
							
							$percentUsed = $hdd[$i]['percentageUsed'];
							$percentFree = 100 - $hdd[$i]['percentageUsed']."%";
												
							echo "<div class='progress'>";
								echo "<div class='bar bar-primary' style='width: $percentUsed;'>" . $hdd[$i]['used'] . "B Used</div>";
								echo "<div class='bar bar-success' style='width: $percentFree;'>" . $hdd[$i]['available'] . "B Free</div>";
							echo "</div>";
							echo "<hr>";
							
							$i++;
						} 
					?>
			</div>
		</div>
		<div class="capsule span4">
			<div class="capsule-inner">
				<h4>General:</h4>
				<table>
					<?php
						echo "<tr><td>Hostname:</td><td>" .$general['hostname']. "</td></tr>";
						echo "<tr><td>Kernel:</td><td>" .$general['kernelName']. " " .$general['kernelRelease']. "</td></tr>";
						echo "<tr><td>Processor:</td><td>" .$general['processor']. "</td></tr>";
						echo "<tr><td>Operating Sys:</td><td>" .$general['OS']. "</td></tr>";
						echo "<tr><td>System Datetime:</td><td>" .$general['sysTime']['day']."/".$general['sysTime']['month']."/".$general['sysTime']['year']."</br>".$general['sysTime']['curTime']." ".$general['sysTime']['zone']. "</td></tr>";
					?>
				</table>
				<hr>
				<h4>Uptime:</h4>
				<p>
					<?php 
						//Loop through array
						foreach($uptime as $key => $value) {
							//If current object in array is not empty
							if(!empty($value))
							{
								//Echo the value followed by key
								echo "$value $key ";
							}
						} 
					?>
				</p>
				<hr>
				<h4>User Stats:</h4>
				<table>
					<?php 
						//If we have users
						if($user)
							{
								//Loop through array
								foreach($user as $key => $value) {
									foreach($value as $key => $value) {
										//If the array is not empty - We have users
										if(!empty($value))
										{
											//Echo the key followed by the value
											echo "<tr><td>$key:</td><td>$value</td></tr>";
										}
									}
								} 
							}
						//If we don't have users
						else
							{
								echo "<tr><td>No users currently active</td></tr>";
							}
					?>
				</table>
				<hr>
			</div>
		</div>
	</div>
<?php require('footer.php'); ?>