<?	
/** Classe produit_ram
* @package Optima
* @subpackage Cleodis
*/
class produit_ram extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_ram";
		$this->colonnes['fields_column'] = array( 
			 'produit_ram.produit_ram'
		);

		$this->colonnes['primary'] = array(
			"produit_ram"
		);
	
		$this->colonnes["speed_insert"] = array(
			'produit_ram'	
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

class produit_ram_cleodisbe extends produit_ram { };
class produit_ram_cap extends produit_ram { };
class produit_ram_exactitude extends produit_ram { };
?>