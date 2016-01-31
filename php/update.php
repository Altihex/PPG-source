<!DOCTYPE html>                                                                         
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta http-equiv="Expires" content="Tue,01 Jan 1995 12:12:12 GMT">
<meta http-equiv="Pragma" content="no-cache">
<title>UPDATE-2</title>
</head>
<body>
<h1>
<?php
echo "Starting Update<br>";
exec('/bin/bash /var/www/html/update.sh',$output,$rv);
if(! $rv){
	echo "Update Succeded<br>";
	print_r($output);
	echo "<br>";
}
else {
	echo "Update Failed<br>";
	print_r($output);
	echo "<br>";
}
echo "<a href='index.php'> Test Referer </a>";
echo '<pre>';
var_dump(PDO::getAvailableDrivers());
echo '</pre>';
?>
        
</h1>
</body>
</html>













