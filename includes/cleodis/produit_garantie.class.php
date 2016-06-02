<?	
/** Classe produit_garantie
* @package Optima
* @subpackage Cleodis
*/
class produit_garantie extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_garantie";
		$this->colonnes['fields_column'] = array( 
			 'produit_garantie.produit_garantie'
		);

		$this->colonnes['primary'] = array(
			"produit_garantie"
		);

		$this->colonnes["speed_insert"] = array(
			'produit_garantie'	
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

class produit_garantie_cleodisbe extends produit_garantie { };
class produit_garantie_cap extends produit_garantie { };
class produit_garantie_exactitude extends produit_garantie { };
?>