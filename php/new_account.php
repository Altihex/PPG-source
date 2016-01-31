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

   	$lg = new newAccount;
   	$rt = $lg->newToken();
   	$return = $lg->newUser();
  	return $return;
  	
  	
   	class newAccount {
   		
   		public $dbAccess;
   		public $usrToken;
   		
   		public function getToken() {
   			return $this->usrToken;
   		}
   		
   		public function newToken () {
   			
   			exec('/bin/bash /usr/bin/mkpasswd -l 40 -d 10 -s 0 ',$output,$rv);
			if(!$rv){
				$this->usrToken = $output[0];
				//$this->log->write("token returned is $this->usrToken");
				if(strlen($this->usrToken)=== 40) {
					return "true";
				}
			} 
			return "false";  			
   		}
   		
   		public function validateEmail($email) {
   			//$email = test_input($_POST["email"]);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  				return false; 
			}
   			return true;
   		}
   		
   		public function newUser () {
   			$sess = new ppg_session;
  			$this->dbAccess = $sess->start("new_account.php");
  			$sess_data = $sess->get_data();
  			$log = new logger($sess_data->ppg_sessionId,"new_account.php");
  			$log->write("Starting up");
			$postdata = file_get_contents("php://input");
    		$request = json_decode($postdata);
    		$log->write("got the following");
    		if(!isset($request->email)) {
    			$log->write("Email isn't set on calling object",9);
    			echo "Invalid Request sent";
    			return;
    		}
    		$log->write("Email gives -> $request->email");
    		$log->write("Password gives -> " . $request->pass); 
    		$log->write("Handle gives -> " . $request->handle); 
   			$result = $this->dbAccess->db_prepare("SELECT email,password,handle FROM `users` WHERE email = ?"); 
   			$result->bind_param('s',$request->email);
   			$this->dbAccess->db_execute(1,$result);
   			$result->bind_result($emName,$cPass,$shortName);
   			$rwCnt = 0;
   			if(!$this->validateEmail($request->email)){
   				$log->write("Invalid e-mail entered - $request->email",9);
   				$eMsg = "Invalid e-mail address";
   				echo $eMsg;
   				return;
   			}
   			while($result->fetch()){
   				$rwCnt++;
   			}   			
   			$log->write("Got $rwCnt from query");
   			$eMsg = "This address is already registered";
    		if($rwCnt == 1){
    			//email not found
    			$log->write("Email address found - $request->email");
    			echo $eMsg;
    			return;
    		}
    		if($rwCnt == 0){
    			//password validation - just in case some mong is trying it on.
    			if (strlen($request->pass <= '8')) {
        			echo "Your Password Must Contain At Least 8 Characters!";
        			return;
    			}
    			elseif(!preg_match("#[0-9]+#",$request->pass)) {
        			echo "Your Password Must Contain At Least 1 Number!";
    				return;
    			}
    			elseif(!preg_match("#[A-Z]+#",$request->pass)) {
        			echo "Your Password Must Contain At Least 1 Capital Letter!";
    				return;
    			}
    			elseif(!preg_match("#[a-z]+#",$request->pass)) {
        			echo "Your Password Must Contain At Least 1 Lowercase Letter!";
    				return;
    			}
    			$log->write("Valid email address");
    			$log->write("Valid password");
    			$passHash = crypt($request->pass,"\$5\$poopingisalways1");
   				$result = $this->dbAccess->db_prepare("INSERT INTO `users` (
   					`organisations_id`,
   					`user_profiles_id`,
   					`roles_id`,
   					`calendars_id`,
   					`resources_id`,
   					`email`,
   					`password`,
   					`handle`,
   					`verifylink`)	
   					VALUES(?,?,?,?,?,?,?,?,?)");
   				$orgId = 1;
   				$userProf = 2;
   				$roleId = 1;
   				$calId = 1;
   				$resourceId = 1;
   				$result->bind_param("iiiiissss",$orgId,$userProf,$roleId,$calId,$resourceId,$request->email,$passHash,$request->handle,$this->usrToken);
   				$this->dbAccess->db_execute(4,$result);
   				
				$subject = 	'Welcome to PlanPrintGo';
				$message = 	"Hello,\n\nPlease click or copy the link into your browser to activate your newly created Plan Print Go account.\n\n" .
							 "http://192.168.56.10/php/verify.php?id={$this->usrToken}\n\n" .
							 "If you didn't create this account please let us know at admin@planprintgo.com\n\nThanks from the team at PPG\n";
				$headers = 	'From: admin@planprintgo.com' . "\r\n" .
    						'Reply-To: admin@planprintgo.com' . "\r\n" .
    						'X-Mailer: PHP/' . phpversion();

				mail($request->email, $subject, $message, $headers);   
				$log->write("Login URL = http://192.168.56.10/php/verify.php?id={$this->usrToken}");			
				echo "cool";
    			return;
    		}
    	}
   	}
  ?>