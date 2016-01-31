<?php
class logger {

	public $debugLevel = -1;
	public $sessId;
	public $errorHandle;
	public $caller;
	
	function __construct($sessId,$caller) {
		$this->caller = $caller;
		$this->sessId = $sessId;
		if(! isset($this->errorHandle)){
			$this->errorHandle = fopen("php://stderr","a+") or die ( "No standard error availble for logging");
		}
		$this->write("Creating logger object for $caller");
	}
	
	function __destruct() {
		
		$this->write("Closing logger object for $this->caller");
	}
	
	
	public function write($message,$sev = 0) {
		$tag;
		if($sev < $this->debugLevel){
			return;
		}
		date_default_timezone_set("GMT");
		switch($sev) {
			case 0 : $tag = "notice";
							break;
			case 1 : $tag = "warn";
							break;
			case 3 : $tag = "error";
						 	break;
			case 9 : $tag = "security";
							break;
			default : $tag = "notice";
					break;
		}
		$message = sprintf("[%s] [%s] [ppg] [session:%s] [prog:%s] : %s", date('D M d H:i:s Y'), $tag,$this->sessId,$this->caller,$message);
		fprintf($this->errorHandle,"%s\n",$message);
	}
	
	//diagnostics helper
	public function dTS($var) {
    	ob_start();
    	var_dump($var);
    	$result = ob_get_clean();
    	return $result;
	}
}