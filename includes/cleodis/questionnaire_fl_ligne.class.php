<?	
/** Classe questionnaire_fl_ligne
* @package Optima
* @subpackage Cleodis
*/
class questionnaire_fl_ligne extends classes_optima {
	function __construct() {
		parent::__construct(); 
		$this->table = "questionnaire_fl_ligne";
		$this->controlled_by = "questionnaire_fl";
		$this->colonnes['fields_column'] = array( 
			 'questionnaire_fl_ligne.id_pack_produit'			
		);

		$this->colonnes['primary'] = array(
			"id_questionnaire_fl"
			,"id_pack_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::pack_produit()->autocompleteMapping
			))			
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_questionnaire_fl_ligne','id_questionnaire_fl')	;
		$this->colonnes['ligne'] =  array("questionnaire_fl_ligne.id_pack_produit"=>array("hidden"=>true));

		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
	
		

		$this->fieldstructure();
	}

	

  	
	
};
?>