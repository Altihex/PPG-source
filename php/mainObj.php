<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
   	$path .= "/php/ppg_session.php";
   	include_once $path;
  	
   	$path = $rootPath . "/php/logger.php";
   	include_once $path;
   	
   	
   	class mainObj {
  		//Class definition for main display object
  		public $headerObj;
  		public $dataObj;
  		public $cols;
  		public $sess;
  		public $dbAccess;
  		public $sess_data;
  		public $log;
  		
  		function __construct() {
  			
  			$this->sess = new ppg_session;
  			$this->dbAccess = $this->sess->start("mainObj.php");
  			$this->sess_data = $this->sess->get_data();
  			$this->log = new logger($this->sess_data->ppg_sessionId,"mainObj.php"); 	
  			
  			$this->headerObj->cols = 0;	
  			$this->headerObj->style->width = 0;
  			$this->headerObj->text = "";
  			
  			$this->dataObj->rows = 0;	
  			$this->dataObj->style->width = 0;
  			$this->dataObj->style->updateable = "";
  			$this->dataObj->cells = array();
  			$this->dataObj->status = array();
  			//$this->dataObj->user->loginname = $sess_data->loginName;
  		}
  		
  		public function setWindow($winHeight,$winWidth) {
  			
  			//$widths = array("50px","200px","50px","col-md-1","col-md-1","col-md-1","col-md-1","col-md-1");	
  			$ro = array(true,false,false,true,false,false,false);
  			//$date = array(false,false,false,false,true,true,false);	
  			$date = array(false,false,false,false,true,true,false);			
  			$this->headerObj->cols = range(0,6);	
  			//$this->headerObj->style->width = $widths;
  			$this->headerObj->text = array("Id","Description", "Dur","","Start","End", "Completion");
  			
  			//$rowCount = $winHeight / 40;
  			//$this->dataObj->rows = range(0,$rowCount*100,100);	
  			//$this->dataObj->style->width = $widths;
  			$this->dataObj->style->updateable = $ro;
  			$this->dataObj->style->date = $date;
  			//$this->dataObj->user->loginname = "";
  			
  		}
  		
  		public function getHeaders($width){
  		// for now all constants, later will do a setup screen and pull this info from the session.	
  
  			
  			

  		}
  		
  	}