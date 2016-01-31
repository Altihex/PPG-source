<!DOCTYPE html>                                                                         
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<meta http-equiv="Expires" content="Tue,01 Jan 1995 12:12:12 GMT">
		<meta http-equiv="Pragma" content="no-cache">
		<title>DEPLOY-0</title>
	</head>
	<body>


	<?php
		
		$path = $_SERVER['DOCUMENT_ROOT'];
   		$path .= "/php/dbconnect.php";
   		include_once $path;
   		
   		$memcache = new Memcache;
		$memcache->connect('localhost', 11211);
   		$memcache->flush();
   		
   		
   		$dbA = new dbconnect();
   		$dbA->db_con();
   		$result = $dbA->db_query("CREATE USER 'apache'@'localhost'");
   		$result = $dbA->db_query("GRANT ALL ON *.* TO 'apache'@'localhost'");
   		$result = $dbA->db_query("FLUSH PRIVILEGES");
   		echo "created temp apache user<br>";
   		
   		exec('/bin/cat /var/www/html/sql/schema.sql |/usr/bin/mysql',$output,$rv);
		echo "Schema update Run<br>";
		//echo "$output[0]";
		echo "<br>";
   		
   		
   		$result = $dbA->db_query("INSERT INTO `organisations` (`name`) VALUES('TempOrg')");
   		echo "<br>Created TempOrg record<br>";
   		$result = $dbA->db_query("INSERT INTO `user_profiles` (`organisations_id`,`name`) VALUES(1,'Default-Temp')");
   		echo "Created Default temp user profile record<br>";
   		$result = $dbA->db_query("INSERT INTO `user_profiles` (`organisations_id`,`name`) VALUES(1,'Default-Prereg')");
   		echo "Created Default user pre-reg profile record<br>";
   		$result = $dbA->db_query("INSERT INTO `user_profiles` (`organisations_id`,`name`) VALUES(1,'Default-Registered')");
   		echo "Created Default user registered profile record<br>";
   		$result = $dbA->db_query("INSERT INTO `roles` (`organisations_id`,`name`) VALUES(1,'Default')");
   		echo "Created Default role record<br>";
   		$result = $dbA->db_query("INSERT INTO `calendars` (`organisations_id`,`name`) VALUES(1,'Default')");
   		echo "Created Default calendar record<br>";
   		$result = $dbA->db_query("INSERT INTO `resources` (`organisations_id`,`name`) VALUES(1,'Default')");
   		echo "Created Default resources record<br>";
   		$result = $dbA->db_query("INSERT INTO `users` (`organisations_id`,
   															`user_profiles_id`,
   															`roles_id`,
   															`calendars_id`,
   															`resources_id`,
   															`handle`,
   															`email`,
   															`password`)
   															
   													VALUES('1','1','1','1','1','TempUser','temp@user','blank')");
   		echo "Created Default user record<br>";
   		
   		// TEST USER - MUST BE REMOVED
   		$passHash = crypt("Shitfire1","\$5\$poopingisalways1");
   		$result = $dbA->db_query("INSERT INTO `users` (`organisations_id`,
   															`user_profiles_id`,
   															`roles_id`,
   															`calendars_id`,
   															`resources_id`,
   															`handle`,
   															`email`,
   															`password`)
   															
   													VALUES('1','3','1','1','1','Deploy Generated User','steve@altihex.com','$passHash')");
   		echo "Created TEST user record - MUST BE REMOVED<br>";
   		
   		
   		
   		$dbA->db_query("DROP USER 'apache'@'localhost'");
   		"Deleted temp apache user<br>";
   		
   	?>