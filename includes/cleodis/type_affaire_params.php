<?
/** Classe type_affaire
* @package Optima
* @subpackage Cléodis
*/
class type_affaire extends classes_optima {
	public function __construct() {
        parent::__construct();
        
        //table type_affaire
		$this->table = "type_affaire_params";
		$this->colonnes["fields_column"] = array(
            ,"type_affaire_params.id_type_affaire"	
			"type_affaire_params.id_societe"	
		);
		
	}

}