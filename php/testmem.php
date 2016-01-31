<?php	
	$path = $_SERVER['DOCUMENT_ROOT'];
   	$path .= "/ppg_session.php";
   	include_once $path;
   	$path = $_SERVER['DOCUMENT_ROOT'];
   	$path .= "/dbconnect.php";
   	include_once $path;
   	
   	
   	session_start();
   	
   	
?> 
<!DOCTYPE html>                                                                         
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta http-equiv="Expires" content="Tue,01 Jan 1995 12:12:12 GMT">
<meta http-equiv="Pragma" content="no-cache">
<title>MemCache TEST Mule</title>
</head>
<body>
<p>
	<?php 
		echo '<pre>';
		$version = $memcache->getVersion();
		echo "Server's memcache version: ".$version."<br/>\n";
		
		$sess->showAll();
		printf("<line><br>Sessions table dump <br>");
		$result = $dbConnObj->db_query("select * from sessions");
		while($row = $result->fetch_assoc()){
    		print_r($row);
    		echo '<br />';
		}
		printf("</line><br>");
		//print_r($result->fetch_assoc());
		echo "number of query rows = {$result->num_rows} <br>";
		echo "HTTP_USER_AGENT = " . $_SERVER['HTTP_USER_AGENT'] .  "<br>";
		if(isset($_SERVER['HTTP_REFERER'])) {echo "HTTP_REFERER = " . $_SERVER['HTTP_REFERER'] . "<br>";}
		echo "REMOTE_ADDR = " . $_SERVER['REMOTE_ADDR'] . "<br>";
		echo "Session Object dump <br>";
		print_r($sess);
		
		echo "Memcache Object dump <br>";
		print_r($memcache);
		
		
		echo '<br></pre>';
	?>
        
</p>
</body>
</html>