<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
   	$path .= "/php/ppg_session.php";
   	include_once $path;
   	
  	$sess = new ppg_session;
  	$dbAccess = $sess->start("status.php");
  	$sess_data = $sess->get_data();
  	echo json_encode($sess_data);
  	return;