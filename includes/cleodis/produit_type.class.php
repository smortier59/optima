<?	
/** Classe produit_type
* @package Optima
* @subpackage Cleodis
*/
class produit_type extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_type";
		$this->colonnes['fields_column'] = array( 
			 'produit_type.produit_type'=>array("width"=>80,"rowEditor"=>"setInfos")
		);

		$this->colonnes['primary'] = array(
			"produit_type"
		);

		$this->colonnes["speed_insert"] = array(
			'produit_type'	
		);
		
		$this->fieldstructure();	
		$this->controlled_by = "produit";
	}

	/** Surcharge de l'autocomplete pour qu'il gère des condition qui sont en session
	* Author Quentin JANON <qjanon@absystech.fr>
	*/
	public function autocomplete($infos) {
		if (ATF::_s("preselected_".$this->table)) {
			foreach (ATF::_s("preselected_".$this->table) as $k=>$i) {
				$this->q->where("id_".$this->table,$i['id_'.$this->table]);
			}
		}
		return parent::autocomplete($infos,false);
	}

};

class produit_type_cleodisbe extends produit_type { };
class produit_type_cap extends produit_type { };
?>