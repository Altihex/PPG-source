<?php
	//Session Object
	class ppg_session_data {

		//set the debug level for the session
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
		public $loginName;
		public $loggedIn;
		public $pageviews;
		public $currentProjectName;
		public $currentProjectId;
		
		
		public function set_sessid($sid) {
			
			$this->ppg_sessionId = $sid;
		}
		
		public function get_sessid() {
			
			return $this->ppg_sessionId;
		}

	}
	
?>