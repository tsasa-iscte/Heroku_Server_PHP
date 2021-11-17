<?php
	$_POST = json_decode(file_get_contents('php://input'), true); // With this line, the script processes only a json object not a form submission (for testing purposes)
	$queryanswerfilename = "queryid-".$_POST["queryid"]."-normalclientpid-".$_POST["parentpid"]."-childpid+".$_POST["childpid"].".answer" ;

	if ($queryanswerfile = fopen($queryanswerfilename, "w")) { 
//		$queryanswer["queryanswer"] = $_POST["queryanswer"];
//		fwrite($queryanswerfile , str_replace("\\\"", "\"", str_replace("\\t", "", str_replace("\\/", "/", (json_encode($queryanswer))))));
		fwrite($queryanswerfile , str_replace("\\\"", "\"", str_replace("\\t", "", str_replace("\\/", "/", ($_POST["queryanswer"])))));
		fclose($queryanswerfile);

		$output=null; $retval=null;
		$cmd = "kill -9 ".$_POST["childpid"];
		exec("$cmd", $output, $retval);

		if (is_null($retval) or !($retval == '0')) {
			echo "Pending query parent process not awaken because child pid kill was not successful!<br>" . $queryanswerfilename;
		} else {
			$querybeingprocessedfilename = str_replace(".answer", ".query_being_processed_on_special_client" , $queryanswerfilename);
			unlink("$querybeingprocessedfilename"); // delete file
			echo "Files $queryanswerfilename and $querybeingprocessedfilename , created and removed in the server, respectively!" ;
		}
	}
	else { 
		echo "Unable to open $queryanswerfile file!"; 
	}
?>
