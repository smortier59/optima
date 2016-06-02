<?
/**
* Classe formation_participant
* Cet objet permet de gérer les devis des formations
* @package Optima
*/
class formation_participant extends classes_optima {

	/**
	* Constructeur par défaut
	*/
	public function __construct() {		
		$this->table = "formation_participant";
		parent::__construct();

		$this->colonnes["fields_column"] = array(
			 'formation_participant.id_formation_devis'
			,'formation_participant.id_contact'
		);

		$this->fieldstructure();

		$this->foreign_key["id_formation_devis"] = "formation_devis";
 		$this->foreign_key["id_contact"] = "contact";

	}
};

class formation_participant_cleodisbe extends formation_participant { };
class formation_participant_cap extends formation_participant { };
class formation_participant_exactitude extends formation_participant { };
?>