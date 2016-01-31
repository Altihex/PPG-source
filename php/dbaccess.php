<?php
	// Class for interacting with the database
	class dbaccess {
		
		public $pdb;
		
		
		// creates a new dbaccess object
		public function connect_pdb() {
			if(! isset($pdb)){
				$pdb = new dbconnect();
				$pdb->db_conn();
			} 	
			return $pdb;
		}
		
		public function disconnect_pdb() {
			$pdb->close; 	
		} 
		
		public function query($query) {
			
			
		}
		
		
	}
