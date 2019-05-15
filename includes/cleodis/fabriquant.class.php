<?
/** Classe fabriquant
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../fabriquant.class.php";
class fabriquant_cleodis extends fabriquant {

	function __construct() {
		parent::__construct();
		$this->table = "fabriquant";
		$this->colonnes["speed_insert"] = array(
			'fabriquant'
		);

		$this->colonnes['fields_column'] = array(
			 'fabriquant.fabriquant'
		);

		$this->colonnes['primary'] = array(
			"fabriquant"
		);

		$this->colonnes["speed_insert"] = array(
			'fabriquant'
		);

		$this->field_nom="fabriquant";
		$this->fieldstructure();
		$this->controlled_by = "produit";
	}
};

class fabriquant_cleodisbe extends fabriquant_cleodis { };
class fabriquant_cap extends fabriquant_cleodis { };

class fabriquant_bdomplus extends fabriquant_cleodis { };
class fabriquant_bdom extends fabriquant_cleodis { };
class fabriquant_boulanger extends fabriquant_cleodis { };