<?	
/** Classe produit_puissance
* @package Optima
* @subpackage Cleodis
*/
class produit_puissance extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_puissance";
		$this->colonnes['fields_column'] = array( 
			 'produit_puissance.produit_puissance'
		);

		$this->colonnes['primary'] = array(
			"produit_puissance"
		);

		$this->colonnes["speed_insert"] = array(
			'produit_puissance'	
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

class produit_puissance_cleodisbe extends produit_puissance { };
class produit_puissance_cap extends produit_puissance { };
class produit_puissance_exactitude extends produit_puissance { };
?>