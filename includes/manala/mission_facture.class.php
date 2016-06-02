<?
/** Classe mission_ligne
* @package Optima MANALA
*/
class facture_mission extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "mission"; 

		$this->colonnes["fields_column"] = array(
			'facture_mission.id_mission'
			,'facture_mission.id_facture'

		);

		$this->fieldstructure();

	}

};