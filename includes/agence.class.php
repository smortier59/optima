<?
/** Classe agence
* @package Optima
*/
class agence extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'agence.agence'
			,'agence.ville'
		);
		$this->colonnes['panel']['coordonnees'] = array(
			"adresse"
			,"adresse_2"
			,"adresse_3"
			,"cp"
			,"ville"
			,"id_pays"
			,"tel"
			,"fax"
		);
		$this->fieldstructure();
		$this->panels['coordonnees'] = array("visible"=>true);
	}
};
?>