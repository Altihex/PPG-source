<?php
	
	$rootPath = $_SERVER['DOCUMENT_ROOT'];
   	$path = $rootPath . "/php/dbconnect.php";
   	include_once $path;
  
   	$path = $rootPath . "/php/ppg_session_data.php";
   	include_once $path;
   	
	$path = $rootPath . "/php/logger.php";
   	include_once $path;
   	
   	$path = $rootPath . "/php/ppg_session.php";
   	include_once $path;

   	$lg = new login;
   	$return = $lg->checkUser();
  	return $return;
  	
  	
   	class login {
   		
   		public $dbAccess;
   		
   		
   		public function checkUser () {
   			$sess = new ppg_session;
  			$this->dbAccess = $sess->start("login.php");
  			$sess_data = $sess->get_data();
  			$log = new logger($sess_data->ppg_sessionId,"login.php");
  			$log->write("Starting up");
			$postdata = file_get_contents("php://input");
    		$request = json_decode($postdata);
    		$log->write("got the following");
    		$log->write("Email gives -> " . $request->email);
    		$log->write("Password gives -> " . $request->pass); 
   			$result = $this->dbAccess->db_prepare("SELECT `organisations_id`,`id`,`user_profiles_id`,`roles_id`,`calendars_id`,`resources_id`,`email`,`password`,`handle` FROM `users` WHERE `email` = ?"); 
   			$result->bind_param('s',$request->email);
   			$this->dbAccess->db_execute(1,$result);
   			$result->bind_result($orgId,$userId,$userProfileId,$roleId,$calendarId,$resourceId,$emName,$cPass,$shortName);
   			//print_r($result);
   			//echo "<br>";
   			$rwCnt = 0;
   			while($result->fetch()){
   				$rwCnt++;
   			}   			
   			$log->write("Got $rwCnt from query");
   			$eMsg = "Login Failed";
    		if($rwCnt == 0){
    			//email not found
    			$log->write("Email address not found - $request->email");
    			echo $eMsg;
    			return;
    		}
    		if($rwCnt == 1){
    			$log->write("Valid email address");
    			$log->write("Password crypt = " . $cPass);
    			if(strcmp(crypt($request->pass,"\$5\$poopingisalways1"),$cPass) == 0){
    				// want to switch to https - SSL here
    				
    				//load data into session here
    				$sess_data->usersOrgId = $orgId;
    				$sess_data->usersId = $userId;
    				$sess_data->usersUserProfileId = $userProfileId;
    				$sess_data->loginName = $shortName;
    				$sess_data->loggedIn = true;
    				$sess->update_session();
    				$_SESSION['ppg_id'] = $sess_data->loginName;
    				$rtnObj->loginName = $shortName;
    				// Check if user is has been validated
    				if($sess_data->usersUserProfileId > 2) {
    					$rtnObj->loggedIn = true;
    					echo json_encode($rtnObj);
    					return;
    				}else {
    					$rtnObj->loggedIn = false;
    					$log->write("User login attempted for $userId - not email validated",9);
    					echo json_encode($rtnObj);
    					return;
    				}
    			}else {
    				$cString = crypt($request->pass,"\$5\$poopingisalways1");
    				$log->write("Password validation failed -> crypt gives $cString vs. $cPass");
    				$rtnObj->loginName ="";
    				$rtnObj->loggedIn = false;
    				echo json_encode($rtnObj);
    				return;
    			}
    		}
    		$rtnObj->loginName ="";
    		$rtnObj->loggedIn = false;
    		echo json_encode($rtnObj);
    		return;
   		}
   	}
   ?>