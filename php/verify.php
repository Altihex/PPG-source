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

   	$vf = new verifyMail;
   	
   	$return = $vf->verifyUserAccount();
  	return $return;
  	
  	class verifyMail {
  		
  		public $dbAccess;
  		
  		public function verifyUserAccount() {
  			$sess = new ppg_session;
  			$this->dbAccess = $sess->start("verify.php");
  			$sess_data = $sess->get_data();
  			$log = new logger($sess_data->ppg_sessionId,"verify.php");
  			$log->write("Starting up");
  			
  			$id = $_GET['id'];
  			if(strlen($id) == 40) {
  				if(preg_match("/^[[:alnum:]]+$/",$id) == 0){
  					$log->write("Input string is incorrect = $id",9);
  					return;
  				} 
  				$result = $this->dbAccess->db_prepare("SELECT id,user_profiles_id,handle  FROM users USE INDEX (verify_long) WHERE verifylink = ?"); 
  				if(!$result) {
  					$log->write("Prepare failed",1);
  					return;
  				}
   				$result->bind_param('s',$id);
   				$result->execute();
   				$result->bind_result($uId,$profId,$handle);
   				$rwCnt = 0;
   				while($result->fetch()){
   					$rwCnt++;
   				}
   				if($rwCnt > 1){
   					$log->write("Found more than one verify user",9);
   					return;
   				}
   				else if($rwCnt == 1) {
   					if($profId != 2) {
   						$log->write("User account for id = $uId currently set to $profId",9);
   						header('Location: /index.html');
   						die();
   					}
   					$log->write("Creating new organisations record with name = $handle and users.id = $uId");
   					$result = $this->dbAccess->db_prepare("INSERT INTO `organisations` (	
    					`name`,
    					`user_created_id`
    					)
   						VALUES(?,?)");
   					$result->bind_param('si',$handle,$uId);
					$this->dbAccess->db_execute(2,$result);
   					$result = $this->dbAccess->db_prepare("SELECT `id` FROM `organisations` WHERE `user_created_id` = {$uId}");			
   					$this->dbAccess->db_execute(3,$result);
   					$result->bind_result($orgId);
   					$rwCnt = 0;
   					while($result->fetch()){
   						$rwCnt++;
   					}
   					if($rwCnt > 1){
   						$log->write("Found more than one organisations record for this user",3);
   						return;
   					}
   					else if($rwCnt == 1) {
   						$log->write("OrgId = $orgId / UserId = $uId");
   						$result = $this->dbAccess->db_prepare("UPDATE `users` SET `user_profiles_id` = 3,`organisations_id` = $orgId WHERE `id` = {$uId}");
   						$this->dbAccess->db_execute(4,$result);
   						$log->write("Activated account id = $uId");
   						header('Location: /index.html');
   						die();
   					}
   				}   
   				else {
   					$log->write("verify not found",9);
   					return;
   				}
  			}
  			else {
  				return;
  			}
  		}
  	 		
  	}