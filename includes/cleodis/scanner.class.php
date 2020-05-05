<?
/**
* Classe scanner
* @package Optima
*/
require_once dirname(__FILE__)."/../scanner.class.php";
class scanner_cleodis extends scanner {
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

class scanner_cleodisbe extends scanner_cleodis { };
class scanner_cap extends scanner_cleodis { };

class scanner_bdomplus extends scanner_cleodis { };

class scanner_boulanger extends scanner_cleodis { };

class scanner_assets extends scanner_cleodis { };