<?
/** Classe type_affaire
* @package Optima
* @subpackage Cléodis
*/
class type_affaire extends classes_optima {
	public function __construct() {
        parent::__construct();
        
        //table type_affaire et ajout de logo
		$this->table = "type_affaire";
		$this->colonnes["fields_column"] = array(
            "type_affaire.type_affaire",
            "logo"
		);
		
    }
}


   
    
	
	
