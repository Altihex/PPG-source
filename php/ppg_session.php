<?php
	$rootPath = $_SERVER['DOCUMENT_ROOT'];
   	$path = $rootPath . "/php/dbconnect.php";
   	include_once $path;
  
   	$path = $rootPath . "/php/ppg_session_data.php";
   	include_once $path;
   	
	$path = $rootPath . "/php/logger.php";
   	include_once $path;
   	
	//Session Object
	class ppg_session {

		//set the debug level for the session
		private $debugLevel = -1;		
		public $dbAccess;
		public $errorHandle;
		public $log;
		public $sess_data;
		
		public $ppg_sessionId;
		public $usersId;
		public $usersOrgId;
		public $usersUserProfileId;	
		public $sessionId;
		public $remoteAddress;
		public $httpUserAgent;
		public $refererURL;
		public $creationDate;
		public $lastAccess;
		public $pageviews;
		public $currentProjectId;
		public $currentProjectName;
		
		//Start the session
		public function start() {
			$this->sess_data = new ppg_session_data;
			$this->dbAccess = new dbconnect();	
			if(session_id() == '') {
				session_start();
			}
			$this->ppg_sessionId = session_id();	
			$this->log = new logger($this->ppg_sessionId,"ppg_session.php");
			//$this->log->write("Started Session for $caller",0);
			$dbar = $this->dbAccess->db_con();
			$this->log->write("Setup new DB Connections");
			
			if(isset($_SESSION['ppg_id'])) {
				$this->log->write("Loading an existing session from memcache for " . $_SESSION['ppg_id']);
				$memcache = new Memcache;
				$memcache->connect('localhost', 11211);
				if($this->sess_data = $memcache->get($this->ppg_sessionId)) {
					$this->log->write("Found memcache session object");
					$this->update_pageview();
					//$look = $this->log->dTS($this->sess_data);
					//$this->log->write("sess_data =\n$look");
					$memcache->set($this->ppg_sessionId, $this->sess_data, false, 0);
   					return $this->dbAccess;	
				}
				else {
					$this->log->write("Memcache miss loading session object from DB");
				}
			}
			
			// This may be the section were we load it from the DB anyway and check the session timeout
				
			// Not successfull loading from memcache Set the session id to tempUser
			$_SESSION['ppg_id'] = 'tempUser';
		
			
			$_SESSION['ppg_sess_id'] = $this->ppg_sessionId;
			$result = $this->dbAccess->db_query("SELECT * FROM `sessions` WHERE `session_id` = '{$this->ppg_sessionId}'");
			if($result->num_rows == 0){	
				$result->close();					
				$result = $this->dbAccess->db_prepare("INSERT INTO `sessions` 
						(`users_id`,
						`users_organisations_id`,
						`users_user_profiles_id`,
						`session_id`,
						`remote_address`,
						`user_agent`,
						`referer_address`,
						`creation_date`, 
						`last_accessed`,
						`pageviews`
						) VALUES(?,?,?,?,?,?,?,?,?,?)");
		
				$result->bind_param('iiissssssi',
						$usersId,
						$usersOrgId,
						$usersUserProfileId,	
				  		$sessionId,
				  		$remoteAddress,
				 		$httpUserAgent,
				 		$refererURL,
				 		$creationDate,
				 		$lastAccess,
				 		$pageviews
				 		);
				 	
					// Set bind vars
					// Create a tempuser session
				$remoteAddress = $_SERVER['REMOTE_ADDR'];
				$httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
				if(isset($_SERVER['HTTP_REFERER'])) {
					$refererURL = $_SERVER['HTTP_REFERER'];
				}
				else {
					$refererURL= "Unknown";
				}
				$usersId = 1;
				$usersOrgId = 1;
				$usersUserProfileId = 1;
				date_default_timezone_set("GMT");
				$sessionId = $this->ppg_sessionId;
				//'YYYY-MM-DD HH:MM:SS'
				$creationDate = date("Y-m-d G:i:s");
				$lastAccess = date("Y-m-d G:i:s");
				$pageviews = 0;
				
				$result->execute();
				$result->close();
				$result = $this->dbAccess->db_query("SELECT * FROM `sessions` WHERE `session_id` = '{$this->ppg_sessionId}'");	
				$this->log->write("Created New Temp Session Record");
			}

			// Load the session object
			
			$this->load_session();
			$this->update_pageview();
			$memcache = new Memcache;
			$memcache->connect('localhost', 11211);
			$memcache->set($this->ppg_sessionId, $this->sess_data, false, 0);
			return $this->dbAccess; 
		}
		public function get_data() {
			
			return $this->sess_data;
		}
		
		public function getMemStats() {
			$memcache = new Memcache;
			$memcache->connect('localhost', 11211);
			return $memcache->getExtendedStats();	
		}
		
		public function update_proj($projName,$projId) {
			$this->log = new logger($this->ppg_sessionId,"ppg_session.php-update_proj");
			$this->log->write("projName = $projName / progId = $projId / Session id = $this->ppg_sessionId");	
			$this->log->write("Setup new DB Connections");
			
			$result = $this->dbAccess->db_prepare("UPDATE `sessions` 
				SET `curr_proj_id` 		= '$projId',
					`curr_proj_name` 	= '$projName' 
				WHERE `session_id` = '{$this->ppg_sessionId}'");
			$this->dbAccess->db_execute(1,$result);
			$this->sess_data->currentProjectName = $projName;
			$this->sess_data->currentProjectId	 = $projId;
			$this->update_session();
			//$look = $this->log->dTS($this->sess_data);
			//	$this->log->write("update_proj - sess_data =\n$look");
					
		}
		
		private function update_pageview() {
			$this->sess_data->pageviews++;
			date_default_timezone_set("GMT");
			$aDate = date("Y-m-d G:i:s");
			$result = $this->dbAccess->db_query("UPDATE `sessions` 
				SET `last_accessed` = '$aDate',
					`pageviews` = {$this->sess_data->pageviews} 
				WHERE `session_id` = '{$this->ppg_sessionId}'");
		}
		
		public function load_session() {
			$this->log->write("Starting load_session");
			if((!isset($this->sess_data->usersOrgId))){
				$result = $this->dbAccess->db_query("SELECT * FROM `sessions` WHERE `session_id` = '{$this->ppg_sessionId}'");
				$row = $result->fetch_assoc();
				$this->sess_data->ppg_sessionId 		= 	$this->ppg_sessionId;
				$this->sess_data->usersId	 			=	$row['users_id'];
				$this->sess_data->usersOrgId			=	$row['users_organisations_id'];
				$this->sess_data->usersUserProfileId 	= 	$row['users_user_profiles_id'];
				$this->sess_data->sessionId				=	$row['session_id'];
				$this->sess_data->remoteAddress			= 	$row['remote_address'];
				$this->sess_data->httpUserAgent			= 	$row['user_agent'];
				$this->sess_data->refererURL			= 	$row['referer_address'];
				$this->sess_data->creationDate			= 	$row['creation_date'];
				$this->sess_data->lastAccess			= 	$row['last_accessed'];
				$this->sess_data->pageviews				= 	$row['pageviews'];
				$this->sess_data->currentProjectName	=	$row['curr_proj_name'];
				$this->sess_data->currentProjectId		=	$row['curr_proj_id'];
				$this->log->write("Loaded session record");
				}
			else {
				$this->log->write("Skipped session record load");
			}
			
		}
		public function update_session() {
			$this->log->write("Starting update_session");
			$memcache = new Memcache;
			$memcache->connect('localhost', 11211);
			$memcache->replace($this->ppg_sessionId, $this->sess_data, false, 0);
		}
		public function sessId() {
			// returns the sesion id
			return $this->sess_data->ppg_sessionId;
		}
		
		//Display all the keys and values - For diagnostics
		public function showAll() {
			foreach ($_SESSION as $key=>$val)
			echo $key. ": ".$val. "<br>";
		}
	}
	
?>