<?	
/** Classe produit_viewable
* @package Optima
* @subpackage Cleodis
*/
class produit_viewable extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_viewable";
		$this->colonnes['fields_column'] = array( 
			 'produit_viewable.produit_viewable'
		);

		$this->colonnes['primary'] = array(
			"produit_viewable"
		);
		
		$this->colonnes["speed_insert"] = array(
			'produit_viewable'	
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

class produit_viewable_cleodisbe extends produit_viewable { };
class produit_viewable_cap extends produit_viewable { };
?>