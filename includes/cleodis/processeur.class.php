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