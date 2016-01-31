#!/usr/bin/php 
   	
  	$mc = new mainCalc;
  	$date = DateTime::createFromFormat('j-M-Y', '15-Feb-2009');
echo $date->format('Y-m-d');
  	//'YYYY-MM-DD HH:MM:SS' 
  	//$rc = $mc->getMonths('2016-07-24 00:00:00','2015-08-22 00:00:00' );
  	//print_r($mc);
	exit;
	  	
  	//one object for main display and actions
  	class mainCalc {
  	
  		public $log;
  		public $sess_data;
  		public $sess;
  		public $dbAccess;
  		public $request;
  		public $dO;


		public function create_screen($screen_width,$projStartDate,$projEndDate){
			if(!isset($width) || !isset($projStartDate) || !isset($projeEndDate)) {
				return NULL;	
			}
			$col_width = $width / ($projEndDate - $projStartDate);
			
			
		}	
		
		public function getMonths($projEndDate,$projStartDate,$count = 1) {
    		$now = $projEndDate;
    		$start = DateTime::createFromFormat("D Y", $projStartDate);
    		$list = array();
    		$interval = new DateInterval(sprintf("P%dM",$count));
    		while ( $start <= $now ) {
        		$list[$start->format("Y")][] = $start->format("D");
        		$start->add($interval);
    		}
    		return $list;
		}
		
  }



