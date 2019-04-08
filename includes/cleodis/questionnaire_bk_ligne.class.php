<?	
/** Classe questionnaire_bk_ligne
* @package Optima
* @subpackage Cleodis
*/
class questionnaire_bk_ligne extends classes_optima {
	function __construct() {
		parent::__construct(); 
		$this->table = "questionnaire_bk_ligne";
		$this->controlled_by = "questionnaire_bk";
		$this->colonnes['fields_column'] = array( 
			 'questionnaire_bk_ligne.id_pack_produit'			
		);

		$this->colonnes['primary'] = array(
			"id_questionnaire_bk"
			,"id_pack_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::pack_produit()->autocompleteMapping
			))			
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_questionnaire_bk_ligne','id_questionnaire_bk')	;
		$this->colonnes['ligne'] =  array( 	
			 "questionnaire_bk_ligne.id_pack_produit"=>array("hidden"=>true)
			
		);

		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
	
		

		$this->fieldstructure();
	}

	

  	
	
};
