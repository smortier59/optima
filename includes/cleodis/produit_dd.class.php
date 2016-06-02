<?	
/** Classe produit_dd
* @package Optima
* @subpackage Cleodis
*/
class produit_dd extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_dd";
		$this->colonnes["speed_insert"] = array(
			'produit_dd'	
		);

		$this->colonnes['fields_column'] = array( 
			 'produit_dd.produit_dd'
		);

		$this->colonnes['primary'] = array(
			"produit_dd"
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

class produit_dd_cleodisbe extends produit_dd { };
class produit_dd_cap extends produit_dd { };
class produit_dd_exactitude extends produit_dd { };
?>