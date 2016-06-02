<?	
/** Classe fabriquant
* @package Optima
* @subpackage Cleodis
*/
class fabriquant extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "fabriquant";
		$this->colonnes["speed_insert"] = array(
			'fabriquant'	
		);

		$this->colonnes['fields_column'] = array( 
			 'fabriquant.fabriquant'
		);

		$this->colonnes['primary'] = array(
			"fabriquant"
		);

		$this->colonnes["speed_insert"] = array(
			'fabriquant'	
		);
		
		$this->fieldstructure();	
		$this->controlled_by = "produit";
	}
};

class fabriquant_cleodisbe extends fabriquant { };
class fabriquant_cap extends fabriquant { };
class fabriquant_exactitude extends fabriquant { };
?>