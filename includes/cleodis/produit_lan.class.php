<?	
/** Classe produit_lan
* @package Optima
* @subpackage Cleodis
*/
class produit_lan extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_lan";
		$this->colonnes['fields_column'] = array( 
			 'produit_lan.produit_lan'
		);

		$this->colonnes['primary'] = array(
			"produit_lan"
		);
		
		$this->colonnes["speed_insert"] = array(
			'produit_lan'	
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

class produit_lan_cleodisbe extends produit_lan { };
class produit_lan_cap extends produit_lan { };
?>