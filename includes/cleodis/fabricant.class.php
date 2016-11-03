<?	
/** Classe fabricant
* @package Optima
* @subpackage Cleodis
*/
class fabricant extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "fabricant";
		$this->colonnes["speed_insert"] = array(
			'fabricant'	
		);

		$this->colonnes['fields_column'] = array( 
			 'fabricant.fabricant'
		);

		$this->colonnes['primary'] = array(
			"fabricant"
		);

		$this->colonnes["speed_insert"] = array(
			'fabricant'	
		);
		
		$this->fieldstructure();	
		$this->controlled_by = "produit";
	}
};

class fabricant_cleodisbe extends fabricant { };
class fabricant_cap extends fabricant { };
?>