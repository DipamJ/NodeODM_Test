<?php
	function SetDBConnection()
	{
		//return mysqli_connect("127.0.0.1:5306","root","","uas_projects");
		//return mysqli_connect("127.0.0.1","lhuynh","!STl246502017","uas_projects");
        return mysqli_connect("localhost", "hub_admin", "", "uas_projects");
    }
?>
