<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
   	$path .= "/php/ppg_session.php";
   	include_once $path;
  	
   	$path = $rootPath . "/php/logger.php";
   	include_once $path;
  	
   	$path = $rootPath . "/php/mainObj.php";
   	include_once $path;
   	
  	$mc = new mainCalc;
  	$rc = $mc->decode();
  	
  	//one object for main display and actions
  	class mainCalc {
  	
  		public $log;
  		public $sess_data;
  		public $sess;
  		public $dbAccess;
  		public $request;
  		public $dO;
  		public $startDate;
  		public $endDate;
  		public $projEndDate;
  		public $projStartDate;
  		
  		public function decode() {
 			//get the post data on from a request
 			$this->sess = new ppg_session;
 			$this->sess_data = new ppg_session_data;
  			$this->dbAccess = $this->sess->start("main.php");
  			$this->sess_data = $this->sess->get_data();
  			$this->log = new logger($this->sess->ppg_sessionId,"main.php"); 		
  			if(! isset($this->sess_data->loginName)){
  				//not logged in, no va
  				$this->log->write("Not logged in exiting",9);
  				return;
  			}
  			
 			$postdata = file_get_contents("php://input");
    		$this->request = json_decode($postdata);
    		$this->projEndDate = DateTime::createFromFormat('d-m-Y',"01-01-1970");
  			if($this->request->action==="init"){
  				$winHeight = $this->request->winHeight;
  				$winWidth = $this->request->winWidth;
  				$this->log->write("Window height = $winHeight / Window Width = $winWidth");
  				//Setup Header Row
  				$rtn = new mainObj;
  				echo json_encode($rtn->setWindow($winHeight, $winWidth)); 
  				return;
  			}
  			if($this->request->action ==="new"){
  				$this->log->write("Creating New Project - {$this->request->name}");
  				//need to get the next project number for the org
  				$this->log->write("Organisations id for this user = {$this->sess_data->usersOrgId} ");
  				$result = $this->dbAccess->db_prepare("SELECT last_project_id FROM `organisations` WHERE `id` = '{$this->sess_data->usersOrgId}' FOR UPDATE");
  				$this->dbAccess->db_execute(1,$result);
  				$result->bind_result($progId);
  				$result->fetch();
  				$this->log->write("last project id = $progId before update");
  				$progId++;
  				$result = $this->dbAccess->db_prepare("UPDATE `organisations` SET `last_project_id` = {$progId} WHERE `id` = {$this->sess_data->usersOrgId}");	
  				$this->dbAccess->db_execute(2,$result);
 				$this->log->write("progId = {$progId} After update");
 				$startDate = date('Y-m-d H:i:s');
 				$days = 0;
  				$result = $this->dbAccess->db_prepare("INSERT INTO `projects` (
  					`id`,
  					`organisations_id`,
  					`name`,
					`start_date`,
					`last_date`,
					`proj_days`)
  					VALUES (?,?,?,?,?,?)");	
  				$result->bind_param('iisssi',$progId,$this->sess_data->usersOrgId,$this->request->name,$startDate,$startDate,$days);
  				$this->dbAccess->db_execute(4,$result);
  				$result = $this->dbAccess->db_prepare("INSERT INTO `objectives` (`id`,`seq_id`,`projects_id`,`description`) VALUES(?,?,?,?)");
  				$blank ="";
  				$result->bind_param('iiis',$nid,$nid,$progId,$blank);
  				for($nid=1;$nid<11;$nid++) {
  					$this->dbAccess->db_execute(5,$result);
  				}
  				$mainO = new mainObj;
  				$mainO->setWindow($this->request->winHeight,$this->request->winWidth);
  				$mainO->dataObj->cells = $this->loadData(1,30,$progId);
  				$mainO->dataObj->deps = $this->loadDeps(1,30,$progId);
  				$mainO->dataObj->status= $this->loadStatus(1,30);
  				//manual hack - sets the window size
  				$mainO->dataObj->rows = range(0,29);
  				//$mainO->dataObj->headerArray = $this->create_headerArray($this->request->winWidth,$startDate,$startDate);
  				$this->projStartDate = new DateTime;
  				$this->projEndDate = new DateTime;
  				$mainO->dataObj->headerArray = $this->create_headerArray('4000',$this->projStartDate,$this->projEndDate);
  				$this->sess->update_proj($this->request->name,$progId);
  				$look = $this->log->dTS($mainO->dataObj->headerArray);
  				$this->log->write("headerArray object = \n$look");
  				echo json_encode($mainO);
  				return;
  			}
  			
  			if($this->request->action ==="loadCurrent"){
  				//test code
  				//$startDate = date('Y-m-d H:i:s');
 				//$days = 0;
  				
  				$this->log->write("Loading existing Project Id - {$this->request->projId}");
  				$this->log->write("Organisations id for this user = {$this->sess_data->usersOrgId} ");
  				$this->sess->update_proj($this->request->name,$this->request->projId);
  				$mainO = new mainObj;
  				$mainO->setWindow($this->request->winHeight,$this->request->winWidth);
  				$mainO->dataObj->cells = $this->loadData(1,30,$this->request->projId);
  				$mainO->dataObj->status= $this->loadStatus(1,30);
  				$mainO->dataObj->deps = $this->loadDeps(1,30,$this->request->projId);
  				//manual hack - sets the window size
  				$mainO->dataObj->rows = range(0,29);
  				//$look = $this->log->dTS($this->request);
  				//$this->log->write("request array = \n$look");
  				$this->startDate = date('d-m-Y',strtotime($this->request->sdate));
  				$this->endDate = date('d-m-Y',strtotime($this->request->edate));
  				//$mainO->dataObj->headerArray = $this->create_headerArray($this->request->winWidth,$this->projStartDate,$this->projEndDate);
  				$mainO->dataObj->headerArray = $this->create_headerArray('4000',$this->projStartDate,$this->projEndDate);
  				$look = $this->log->dTS($mainO->dataObj->headerArray->screen);
  				$this->log->write("mainData before the json_encode = \n$look");
  				echo json_encode($mainO);
  				return;
  			}
  			
  			if($this->request->action ==="getProjectList"){
  				$this->log->write("Loading existing Projects org ID - {$this->sess_data->usersOrgId}");
  				$this->log->write("Organisations id for this user = {$this->sess_data->usersOrgId} ");
  				$pList = $this->getProjList($this->sess_data->usersOrgId);
  				//$look = $this->log->dTS($pList);
  				//$this->log->write("pList array = \n$look");
  				echo json_encode($pList);
  				return;
  			}
  			
  			if($this->request->action ==="update"){
  				$this->log->write("Applying updates to Project - {$this->sess_data->currentProjectName}");
  				$look = $this->log->dTS($this->request->uA[0]);
  				$this->log->write("Request updArray = \n$look");				
  				$updArray = $this->request->uA[0];
  				$look = $this->log->dTS($updArray);
  				$this->log->write("updArray = \n$look");
  				$fieldArray = array("description","duration","duration_format","start_date","end_date","completion","act_date");
  				foreach ($updArray as $aO){
  					$cellVal = $aO->value;
  					// Looking for "action" type records - need to unpack and create/delete dependency records, or delete/create objectives
  					if(gettype($cellVal) === "object"){
  						switch($cellVal->action){
  							case 'link':
  								// create dependencies records
  								$look = $this->log->dTS($cellVal);
  								$this->log->write("cellVal = \n$look");
  								$sDep = $cellVal->value[0];
  								if(isset($cellVal->value[1])) {
  									$eDep = $cellVal->value[1];
  									$sqlString = "INSERT INTO dependencies 
  											( 	type,
  												lead_lag,
  												organisations_id,
  												projects_id,
  												projects_organisations_id,
  												objectives_id, 
  												objectives_projects_id,
  												from_objectives_id,
  												description)
  											VALUES(?,?,?,?,?,?,?,?,?)";
  									$this->log->write("sqlstring = $sqlString");
  									$result = $this->dbAccess->db_prepare($sqlString);
  									$cnt = count($cellVal->value);
 									$this->log->write("elements in cellVal->value = $cnt");
  									$i = 2;
  									$type = "local";
  									$lead_lag = 0.00;
  									$desc = "Local Dependency";
  									
  									while($i<=$cnt) {
  										$result->bind_param('sdiiiiiis',
  											$type,
  											$lead_lag,
  											$this->sess_data->usersOrgId,
  											$this->sess_data->currentProjectId,
  											$this->sess_data->usersOrgId,
  											$eDep,
  											$this->sess_data->currentProjectId,
  											$sDep,
  											$desc
  											);
  										$this->dbAccess->db_execute(9,$result);
  										$sDep = $eDep;
  										//ugly code ... needs fixing
  										if(isset($cellVal->value[$i])){
  											$eDep = $cellVal->value[$i];
  										}
  										$i++;
  									}								
  								}
  								break;
  							case 'delink':
  								// delete dependencies record
  								
  								
  								break;
  							
  							default:
  								$this->log->write("Update record has an invalid action");	
  						
  							
  						}
  						
  					} else {
  						
  						$recId = $aO->recId;
  						if(($fieldArray[$aO->column - 1] == "start_date") or ($fieldArray[$aO->column - 1] == "end_date") or ($fieldArray[$aO->column - 1]== "act_date")){
  							$this->log->write("CellVal was $cellVal");
  							$cellVal = date("Y-m-d H:i:s",$cellVal/1000);
  							$this->log->write("CellVal is now = $cellVal");
  						}
  						$sqlString = "UPDATE objectives SET {$fieldArray[$aO->column - 1]}=? WHERE projects_id =? AND seq_id=?";
  						$this->log->write("sqlstring = $sqlString");
  						$result = $this->dbAccess->db_prepare($sqlString);
 						$result->bind_param('sii',$cellVal,$this->sess_data->currentProjectId,$recId);
 						//$this->log->write("cellValue = $cellVal / projectid = $this->sess_data->currentProjectId");
  						$this->dbAccess->db_execute(8,$result);
  					}
  				}	
  				//$look = $this->log->dTS($updArray);
  				//$this->log->write("updArray = \n$look");
  				return;		
  			}
    		echo "ok";
    		return;
  		}
  		
  		
  		public function loadData($startRec,$loadCount,$projectId) {
  			$this->log->write("Calling loadData with start record = $startRec records to load = $loadCount Project id = $projectId");
  			$look = $this->log->dTS($this->projEndDate);
  			$this->log->write("projEndDate = \n$look");
  				
  			$result = $this->dbAccess->db_prepare("SELECT `seq_id`,
  												  `description`,
  												  `duration`,
  												  `duration_format`,
  												  `start_date`,
  												  `end_date`,
  												  `completion`, 
  												  `act_date`
  											FROM `objectives`
  											USE INDEX (PRIMARY)
  											WHERE ((`projects_id` = $projectId) AND ((`seq_id` >= $startRec) AND (`seq_id` <= ($startRec + $loadCount))))");
  			$result->bind_result($seqId,$desc,$duration,$durFormat,$startDate,$endDate,$completion,$actDate);
  			$this->dbAccess->db_execute(6,$result);		
  			$rowCnt = 0;
  			if(!isset($this->projEndDate)){
  				$this->projEndDate = DateTime::createFromFormat('d-m-Y',"01-01-1970");
  			}
  			if(!isset($this->projStartDate)){
  				$this->projStartDate = DateTime::createFromFormat('d-m-Y',"01-01-3000");
  			}
  			
  			while($result->fetch()) {
  				$look = $this->log->dTS($endDate);
  				$this->log->write("before endDate = \n$look");
  				if($startDate == NULL){
  					$startDate = date('Y-m-d H:i:s');
  				}
  				if($endDate == NULL){
  					$endDate = date('Y-m-d H:i:s');
  				}
  				if($actDate == NULL) {
  					$actDate = date('Y-m-d H:i:s');
  				}
  				$startDate 	= DateTime::createFromFormat('Y-m-d H:i:s',$startDate);
          		$endDate	= DateTime::createFromFormat('Y-m-d H:i:s',$endDate);
  				$actDate	= DateTime::createFromFormat('Y-m-d H:i:s',$actDate);
  				
          		
          		$look = $this->log->dTS($endDate);
  				$this->log->write("after endDate = \n$look");
  			
  				if($endDate){
					//$tempEndDate	= DateTime::createFromFormat('Y-m-d h:i:s',$endDate);
  					//$look = $this->log->dTS($endDate);
  					//$this->log->write("after endDate = \n$look");
  					$interval = $this->projEndDate->diff($endDate);
  					$days = $interval->format('%R%a');
					//$this->log->write("projEndDate diff = $days");
					if($days > 0){
						$this->log->write("extending projEndDate ");
						$this->projEndDate = $endDate;
						$look = $this->log->dTS($endDate);
  						$this->log->write("extended projEndDate is now = \n$look");
  			
					}
  				}
  				if($startDate){
  					//$look = $this->log->dTS($startDate);
  					//$this->log->write("after startDate = \n$look");
  					$interval = $this->projStartDate->diff($startDate);
  					$days = $interval->format('%R%a');
					$this->log->write("projStartDate diff = $days");
					if($days < 0){
						$this->log->write("extending projEndDate ");
						$this->projStartDate = $startDate;
						$look = $this->log->dTS($this->projStartDate);
  						$this->log->write("extended projStartDate is now = \n$look");
  			
					}
  				}
				//$rA = array($seqId,$desc,$duration,$durFormat,$startDate->format('d-m-Y'),$endDate->format('d-m-Y'),$completion);
				//$this->log->write("end Date $endDate");
  				//$this->log->write("Start Date $startDate");			
  				$rA = array($seqId,$desc,$duration,$durFormat,($startDate->format('U')*1000),($endDate->format('U')*1000),$completion,($actDate->format('U')*1000));
  				$cnt = 0;
  				while($rA){ 
  					$this->dO[$rowCnt][$cnt++] = array_shift($rA);
  				}
  				$rowCnt++;
  			}
  			$look = $this->log->dts($this->projStartDate);
  			$look1 = $this->log->dts($this->projEndDate);
  			$this->log->write("Exiting loadData - projStartDate = $look / projEndDate = $look1");
  			return $this->dO;
  		}
  		
  		public function loadStatus($startRec,$loadCount){
  			$row = 0;
  			while($row <= $loadCount){
  				for($col=0;$col<9;$col++){
  					$this->st[$row][$col] = false;
  				}
  				$row++;
  			}
  			return $this->st;
  		}
  		
  		public function loadDeps($startRec,$loadCount,$projId){
  			//where we load dependencies from the database
  			$this->log->write("Calling loadDeps with projId = $projId");
  			$row = 0;
  			while($row <= $loadCount){
  				$this->d[$row]['status'] = false;
  				$this->d[$row]['values'] = array();
  				$this->d[$row]['deps'] = array();
  				$this->d[$row]['preds'] = array();
  				$row++;
  			}
  				$result = $this->dbAccess->db_prepare("SELECT `id`,
  					`objectives_id`,
  					`from_objectives_id`,
  					`type`,
  					`lead_lag`,
  					`description`
  				FROM `dependencies`
  				WHERE `projects_id` = $projId ");
  				$result->bind_result($id,$objId,$fromObjId,$type,$lead_lag,$description);
  				$this->dbAccess->db_execute(8,$result);
  				while($result->fetch()) {
  					/* The values array immediately below shouldn't be used anymore and can be deleted */
  					$this->d[$objId]['values'][] = array($id,$objId,$fromObjId,$type,$lead_lag,$description);
  					$this->d[$objId]['deps'][] = array($id,$objId,$fromObjId,$type,$lead_lag,$description);
  				}
  				$this->log->write("Loaded DEPS");
  				$this->dbAccess->db_execute(8,$result);
  				while($result->fetch()) {
  					$this->log->write("fetched a dep record - id = $id");
  					foreach($this->d[$objId]['deps'] as $depArray){	
  						$look = $this->log->dts($this->d);
						$this->log->write("creating Preds = $look");		
  						$this->d[$depArray[2]]['preds'][] = $depArray[1];
  					}
  				}
  			
  			return $this->d;
  		}
  		
  		public function getProjList($orgId) {
  			$this->log->write("Calling getProjList with orgId = $orgId");
  			$result = $this->dbAccess->db_prepare("SELECT `id`,
  												  `name`,
  												  `start_date`,
  												  `last_date`
  											FROM `projects`
  											WHERE `organisations_id` = $orgId");
  			$result->bind_result($id,$name,$startDate,$endDate);
  			$this->dbAccess->db_execute(7,$result);		
  			$rowCnt = 0;
  			$dO->cells = array();
  			while($result->fetch()) {
  				$rA = array($id,$name,$this->m2pDate($startDate,'d-m-Y'),$this->m2pDate($endDate,'d-m-Y'));
  				$cnt = 0;
  				while($rA){ 
  					$dO->cells[$rowCnt][$cnt++]= array_shift($rA);
  				}
  				$rowCnt++;
  			}
  			$dO->rows = range(0,$rowCnt);
  			$dO->cols = range(0,3);
  			return $dO;
  			
  		}
  		
  		//helper code to convert a mysql datetime to a php date
  		// This is being used to format dates for display - JSON doesn't pass date objects
  		public function m2pDate($mySqlDate,$format) {
  			$pD = strtotime( $mySqlDate);
			return date( $format, $pD );		
  		}
  		
  		public function http_response_code($newcode) {
        	header('X-PHP-Response-Code: '.$newcode, true, $newcode);
		}

		
		
		public function create_headerArray($screen_width,$projStartDate,$projEndDate){
			$this->log->write("Calling create_headerArray with screen_width = $screen_width");
			if(!isset($screen_width) || !isset($projStartDate) || !isset($projEndDate)) {
				$this->log->write("projStartDate = isset($projStartDate) projEndDate = isset($projEndDate)",3);
				$this->log->write("Call to create_headerArray returned NULL variable not set",3);
				return NULL;	
			}
			//$start = DateTime::createFromFormat('d-m-Y', $projStartDate);
          	//$end   = DateTime::createFromFormat('d-m-Y', $projEndDate);
          	$start = clone $projStartDate;
			$end = clone $projEndDate;
			$look = $this->log->dts($start);
			$this->log->write("create_HeaderArray input projStartDate = $look");
			$interval = $start->diff($end);
			$days = $interval->format('%a');
			if($days < 90 ){ 
				$addDays = 120 - $days;
				$end->add(new DateInterval(sprintf("P%dD",$addDays)));
				$days = 120;
			}
			// hard coding the screen width....
			$screen_width = 10000;
			$colWidth = intval(($screen_width/2) /$days);
			// hard coding screen width
			$screen_width = 1000;
			$leftWidth = intval($screen_width/2);
			$this->log->write("colWidth = $colWidth");
			//list of years and months
			$look = $this->log->dts($start);
			$this->log->write("create_HeaderArray POINT 1 projStartDate = $look");
			$headerArray = $this->get_years_months_days($leftWidth,$start,$end);
			$look = $this->log->dts($start);
			$this->log->write("create_HeaderArray POINT 2 projStartDate = $look");
			
			//$look = $this->log->dTS($headerArray);
  			//$this->log->write("headerArray = \n$look");
			//$headerArray->screen["colWidth"] = $colWidth;
			$headerArray->screen["colWidth"] = 20;
			//$headerArray->screen["projStartDate"] = $this->projStartDate->format('d-m-Y');
			$headerArray->screen["projStartDate"] = $start->format('d-m-Y');
			$headerArray->screen["projEndDate"] = $end->format('d-m-Y');
			$look = $start->format('d-m-Y');
			$this->log->write("Leaving create_headerArray - projStartDate = $look");
			return $headerArray;
		
		}	
		//This function populates the headers for the Input sheet and gantt chart
		public function get_years_months_days($leftWidth,$start,$end,$count = 1) {
       		// PHP seems to pass object pointers instead of a copy of the actual object - Makes sense really
       		// but if you do actually want a copy of the object then you need to "clone" it
			$e = clone $end;
			$list = (object) array();
			//This creates a date interval of a month by default as $count is set to 1 in the function prototype
          	$interval = new DateInterval(sprintf("P%dM",$count));
          	//Subtract a month from the 
          	$start->sub($interval);
          	$s = clone $start;
			
           	//constant for now - will pull out of session object later 
          	// each title has a weighting out of 100 - All the weights should add up to 100
          	$headerTitles[] = array("Id" => 10);
          	$headerTitles[] = array("Description" => 35);
          	$headerTitles[] = array("Duration" => 10);
          	$headerTitles[] = array("Unit" => 10);
          	$headerTitles[] = array("Start" => 20);
          	$headerTitles[] = array("End" => 20);
          	$headerTitles[] = array("Comp %" => 15);
          	// scale all the header widths against 100	
          	$lW=($leftWidth/100);
          	$vS=0;
          	$colSpans = 0;
          	for($indx=0;$indx < sizeof($headerTitles);$indx++) {
   				foreach($headerTitles[$indx] as $key => $v1_value){
					$vS += $v1_value;
					$colSpans++;   				
   				}
			}
          	$lW = $lW * (100 / $vS );
          	$hI=0;
          	for($indx=0;$indx < sizeof($headerTitles);$indx++) {
          		foreach($headerTitles[$indx] as $key => $v1_value) {
          			$hT[$indx][$key]= intval($lW*$v1_value);
     				$hI++;
          		}
          	}
          
          	$list->leftHeaders = $hT;
          	$firstMonth = true;
          	$oY = -1;
          	$oM = $hI;
          	$oQ = -1;
          	$cQuarter ="";
          	$cYear = "";
          	//Loop through the months from the project start to the project end.
          	while ( $s < $e ) {
          		// For the first month we need to get an offset
          		if($firstMonth){
          			//Get the day date of the month;
          			$fmOffset = $s->format('j');
          			$this->log->write("fmOffset = $fmOffset");
          		}
            	$sPointer = clone $s;
              	$s->add($interval);
              	$mdays = $sPointer->diff($s);
              	$yIndex = sprintf("%s",$sPointer->format("Y"));
              	$qIndex = sprintf("Q%s",intval(ceil($sPointer->format('m')/3)));
              	$mIndex = sprintf("%s",$sPointer->format("M"));
              	
              	if(!isset($list->months[$mIndex])){
              		$list->months[$oM][$mIndex] = 0;
              	}
              	if($firstMonth){
					$firstMonth=false;
					$list->months[$oM][$mIndex] = $mdays->format('%a') + $fmOffset;
              	} else {
              		$list->months[$oM][$mIndex] = $mdays->format('%a');
              	}
              	$oM++;
              	
              	if($cQuarter != $qIndex){
              		$cQuarter = $qIndex;
              		$oQ++;
              	}
          		if(!isset($list->quarters[$oQ][$qIndex])){
              		$list->quarters[$oQ][$qIndex] = 0;
          		}
              	$list->quarters[$oQ][$qIndex] = $list->quarters[$oQ][$qIndex] + 1;
              	
              	if($cYear != $yIndex){
              		$cYear = $yIndex;
              		$oY++;
              	}
              	if(!isset($list->years[$oY][$yIndex])) {
              		$list->years[$oY][$yIndex] = 0;
              	}
              	$list->years[$oY][$yIndex] = $list->years[$oY][$yIndex]+1;
              	
           	}
           	$list->screen["colSpans"]= $colSpans;
           	$list->screen["rColSpans"] = $oM;
  		
           	return $list;
		}
		
  		//display load
  		// get action - page up / page down / Home / End
  		
  		
  		
  		// user wants to see the next page of records 
  		// start at new anchor, load 50 
  		
  
    
  	}
  	
  ?>