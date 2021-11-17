<?php  
	$pid = pcntl_fork(); 
	if ($pid == -1) { echo "Going to die(could not fork)"; die("could not fork"); }
	elseif ($pid) {
		$specialclientpid = getmypid();
//		echo "I'm the Parent with pid: $specialclientpid . Going to get on hold waiting for child pid: $pid termination <br>";
		$specialclientfilename = "specialclientpid-"."$specialclientpid"."-childpid+"."$pid".".specialclient";
		
		if ($specialclientfile = fopen($specialclientfilename, "w"))
			{ fwrite($specialclientfile , "specialclientpid=$specialclientpid,childpid=$pid"); fclose($specialclientfile); }
		else { echo "Unable to open $specialclientfilename file!"; }

//	  	echo "<br>Parent is put on hold at "; $objDateTime = new DateTime('NOW'); echo $objDateTime->format('Y-m-d  H:i:s');
		pcntl_wait($pid , $status); 
//	  	echo "<br>Parent wakes at "; $objDateTime = new DateTime('NOW'); echo $objDateTime->format('Y-m-d  H:i:s') . "<br>";

		foreach (glob("*.query") as $queryfilename) { 
			if ($queryfile = fopen("$queryfilename", "r")) { 
				echo fread($queryfile,filesize("$queryfilename"));
				fclose($queryfile);
				$queryfilenewname = str_replace(".query", ".query_being_processed_on_special_client", "$queryfilename");
				rename("$queryfilename","$queryfilenewname");
			}
			else { echo "Unable to open $queryfilename file!"; }

			break; 
		}
		unlink("$specialclientfilename"); // delete file
	}
	else {
		$childpid = getmypid();
		$parentid = posix_getppid();
//	  	echo "<br> I am the child pid = $pid , getmypid() = $childpid and my parentid posix_getppid() = $parentid ";
	  	sleep(60);
//	  	echo "<br> Child timed out and is going to exit at "; $objDateTime = new DateTime('NOW'); echo $objDateTime->format('Y-m-d  H:i:s') . "<br>"; 
	  	exit;
	}
	$objDateTime = new DateTime('NOW');
//	echo "<br> End: " . $objDateTime->format('Y-m-d  H:i:s'); 
?>
