<?
/** Classe mission_ligne
* @package Optima MANALA
*/
class mission_ged extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "mission"; 

		$this->colonnes["fields_column"] = array(
			'mission_ged.id_mission'
			,'mission_ged.id_ged'

		);



		$this->fieldstructure();



	}

};