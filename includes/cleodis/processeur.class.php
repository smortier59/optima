<?	
/** Classe processeur
* @package Optima
* @subpackage Cleodis
*/
class processeur extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "processeur";
		$this->colonnes["speed_insert"] = array(
			'processeur'	
		);

		$this->colonnes['fields_column'] = array( 
			 'processeur.processeur'
		);

		$this->colonnes['primary'] = array(
			"processeur"
		);

		$this->colonnes["speed_insert"] = array(
			'processeur'	
		);
		
		$this->fieldstructure();	
		$this->controlled_by = "produit";
	}
};

class processeur_cleodisbe extends processeur { };
class processeur_cap extends processeur { };
class processeur_exactitude extends processeur { };
?>