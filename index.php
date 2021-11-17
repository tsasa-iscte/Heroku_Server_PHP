<?php 
	echo "index.php";
	$pid = pcntl_fork(); 
	if ($pid == -1) { echo "Going to die(could not fork)"; die("could not fork"); }
	elseif ($pid) {
		$specialclientpid = getmypid();
//		echo "I'm the Parent with pid: $specialclientpid . Going to get on hold waiting for child pid: $pid termination <br>";

		$queryid = rand(1, 5000);
		$normalclientpid = getmypid();
		
		$queryfilename = "queryid-".$queryid."-normalclientpid-".$normalclientpid."-childpid+".$pid.".query"; 
		$query["queryid"] = $queryid; $query["parentpid"] = $normalclientpid; $query["childpid"] = $pid; $query["query"] = $_POST["query"];
		$queryfile = fopen("$queryfilename", "w") or die("Unable to open $queryfilename file!");
		fwrite($queryfile , json_encode($query));
		fclose($queryfile);

		$output=null; $retval=null;
		foreach (glob("*.specialclient") as $filename) { 
			$start = strpos($filename,"+") + 1; $end = strpos($filename,"."); $length = $end - $start;
			$specialclientchildpid = substr($filename, $start, $length);
			exec("kill -9 $specialclientchildpid", $output, $retval);
			unlink("$filename"); // delete file
//			echo "Please wait, your request in being processed ... <br>";
			break;
		}
		
		if (is_null($retval) or !($retval == '0')) {
			echo "Computing resources temporary not available for your request, please try again later. <br>Sorry for any inconvenience!<br>";
		}
		else {
//			echo "<br>Parent is put on hold at "; $objDateTime = new DateTime('NOW'); echo $objDateTime->format('Y-m-d  H:i:s');
			pcntl_wait($pid , $status); // Parent waits for query answer to arrive (file to be created)
			
//			echo "<br>Parent wakes at "; $objDateTime = new DateTime('NOW'); echo $objDateTime->format('Y-m-d  H:i:s') . "<br>";
			$queryanswerfilename = str_replace(".query", ".answer", "$queryfilename");
			if ($queryanswerfile = fopen("$queryanswerfilename", "r")) { 
				echo fread($queryanswerfile,filesize("$queryanswerfilename")) ;				
				fclose($queryanswerfile);
				unlink("$queryanswerfilename"); // delete file
			}
			else { echo "Unable to open $queryanswerfilename file!"; }
		}
		unlink("$queryfilename"); // delete file
	}			
	else {
		$childpid = getmypid();
//	  	echo "<br> I am the child pid = $pid and getmypid() = $childpid "; 
	  	sleep(60);
	  	echo "<br> Child timedout and is completing its processing at "; $objDateTime = new DateTime('NOW'); echo $objDateTime->format('Y-m-d  H:i:s') . "<br>"; 
	}
?>
