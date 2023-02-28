<?
/** Classe campagne
* @package Optima
* @subpackage Cléodis
*/
class campagne extends classes_optima {
	function __construct($table_or_id) {
		$this->table ="campagne";
		parent::__construct($table_or_id);

		$this->field_nom="campagne";

		$this->fieldstructure();

	}

};

class campagne_cleodisbe extends campagne {};
class campagne_itrenting extends campagne {};
class campagne_cap extends campagne {};


class campagne_bdomplus extends campagne { };
class campagne_boulanger extends campagne { };

class campagne_assets extends campagne { };