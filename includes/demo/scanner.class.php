<?php
/** 
* Classe scanner
* @package Optima
*/
require_once dirname(__FILE__)."/../scanner.class.php";
class scanner_absystech extends scanner {
	/**
	* Constructeur
	*/
	function __construct() {
			
		$this->colonnes['fields_column'] = array(
			"date"
			,"nbpages"
			,"provenance"
			//,'scanner'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>100)
			,"scanner"=>array("custom"=>true,"nosort"=>true,"renderer"=>"pdfScanner","width"=>100)
			,"action"=>array("custom"=>true,"nosort"=>true,"renderer"=>"transfertFile","width"=>80)  
			,"transfert"
		);	
					
		parent::__construct();
		
		
	}
	
};
class scanner_att extends scanner_absystech { };
?>