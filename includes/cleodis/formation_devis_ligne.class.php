<?
/**  
* Classe formation_devis_ligne
* @package Optima
*/
class formation_devis_ligne extends classes_optima {
	/** 
	* Constructeur
	*/
	public function __construct() {
		$this->table = "formation_devis_ligne";
		parent::__construct();
		
		$this->colonnes["fields_column"] = array(
			 'formation_devis_ligne.id_formation_devis'
			  ,'formation_devis_ligne.date'
			 ,'formation_devis_ligne.date_deb_matin'
			 ,'formation_devis_ligne.date_fin_matin'
			 ,'formation_devis_ligne.date_deb_am'
			 ,'formation_devis_ligne.date_fin_am'
			);	
		

		$this->fieldstructure();

		$this->no_insert = true;
		$this->no_delete = true;
		$this->no_update = true;					
	}
};

class formation_devis_ligne_cleodisbe extends formation_devis_ligne { };
class formation_devis_ligne_cap extends formation_devis_ligne { };
class formation_devis_ligne_exactitude extends formation_devis_ligne { };
