<?
/** Classe type_affaire
* @package Optima
* @subpackage Cléodis
*/
class type_affaire_params extends classes_optima {
	public function __construct() {
        parent::__construct();

		$this->table = "type_affaire_params";
		$this->colonnes["fields_column"] = array(
            "id_type_affaire"
			,"id_societe"
		);

		$this->colonnes["fields_column"] = array(
            "id_type_affaire"
            ,"id_societe"
		);

    	$this->fieldstructure();
	}

}