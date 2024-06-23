<?php
function _log($str)
{
	// log to the output
	$log_str = date('d.m.Y').": {$str}\r\n";
	echo $log_str;

	// log to file
	if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
			fputs($fp, $log_str);
			fclose($fp);
	}
}

	function SetDBConnection()
	{
		//return mysqli_connect("127.0.0.1","lhuynh","!STl246502017","uas_projects");
		return mysqli_connect("localhost","hub_admin","UasHtp_Rocks^^7","uas_projects");
	}
?>
