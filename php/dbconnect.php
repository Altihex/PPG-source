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
   	

	// This class creates two connections, read-only and write
	class dbconnect {
	
		//This needs fixing
		public $dbConnArray;
		public $dbCi;
		public $dbr;
		public $dbw;
		private $dbselected; 
		private $dbq;
		public $result;
		public $log;
		
		private function dbSelect($type) {
			// this will hold the selection and counters 
			// To control sites load balancing and db configs
			// For now we just connect
			if($type == 'read'){
				$this->dbselected = 1;
				return $this->dbselected;
			}
			if($type == 'write'){
				$this->dbselected = 1;
				return $this->dbselected;
			}
			else {
				return $this->dbselected = 0;
			}
		
		}
		
		private function dbParam($idx,$type){
			// This holds the connection detail will hold the connection details for all DB's
			if($idx == 1) {
				switch($type) {
					case 'hostname'	: return "localhost";
					case 'user' 	: return "root";
					case 'pass'		: return "steve554";
					case 'db'		: return "ppg";
				}
			}
			else {
				return "dbParam Error:No DB selected";
			}
				
		}
		
		private function dbRoute($query) {
			
			$dbType = "dbr";
			
			//Function to execute a query and select the right DB
			$qType = substr(strtoupper(trim($query)),0,6);
			if( ($qType !== "SELECT") and ($qType !== "SHOW") and ($qType !== "DESC") ) {
				$dbType = "dbw";
			}
			
			$this->dbq = $this->{$dbType};
			
		}
		
		public function db_query($query){
			$this->dbRoute($query);	
			
			//echo "\$query = $query <br>";
			if(! $result = $this->dbq->query($query)){
    			$log = new logger("SQL","dbonnect.php");
  				$log->write("SQL Error gives - {$result->error}",3);
  			}
			return $result;
		}
		
		public function db_prepare($query) {
			
			
			$this->dbRoute($query);
			$log = new logger("diag","dbconnect.php");			
			if(! $this->result = $this->dbq->prepare($query)) {
				$log = new logger("SQL","dbconnect.php");
				//$log->write("Prepare failed for query [' . $this->dbq->error . ']");
				$log->write("Prepare failed for query - {$this->dbq->error}",3);
				//print('The prepare failed for query [' . $this->dbq->error . ']');
				//print_r($this->result);
			}
			return $this->result;
			
		}
		
		public function db_execute($qid,$result) {
			$result->execute();
  			if($result->error) {
  				$log = new logger("SQL","dbonnect.php");
  				$log->write("SQL {$qid} Error gives - {$result->error}",3);
  				return;
  			}
		}
		
		public function db_con() {
			
			
			$idx = $this->dbselect("read");
			
			$host = $this->dbParam($idx, 'hostname');
			$user = $this->dbParam($idx, 'user');
			$pass = $this->dbParam($idx, 'pass');
			$db   = $this->dbParam($idx, 'db');
			
			
			$this->dbr = new mysqli($host,$user,$pass,$db);
			if($this->dbr->connect_errno > 0){
    			die('Unable to connect to database [' . $this->dbr->connect_error . ']');
			}
			$this->dbConnArray['read']= $this->dbr;
			
			
			$idx = $this->dbselect("write");
			$host = $this->dbParam($idx, 'hostname');
			$user = $this->dbParam($idx, 'user');
			$pass = $this->dbParam($idx, 'pass');
			$db   = $this->dbParam($idx, 'db');
			
			$this->dbw = new mysqli($host,$user,$pass,$db);
			if($this->dbw->connect_errno > 0){
   				die('Unable to connect to database [' . $this->dbw->connect_error . ']');
			}
			$this->dbConnArray['write']= $this->dbw;
			return $this->dbConnArray;
		}	
	}