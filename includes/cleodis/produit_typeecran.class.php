<?	
/** Classe produit_typeecran
* @package Optima
* @subpackage Cleodis
*/
class produit_typeecran extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_typeecran";
		$this->colonnes['fields_column'] = array( 
			 'produit_typeecran.produit_typeecran'
		);

		$this->colonnes['primary'] = array(
			"produit_typeecran"
		);
	
		$this->colonnes["speed_insert"] = array(
			'produit_typeecran'	
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

class produit_typeecran_cleodisbe extends produit_typeecran { };
class produit_typeecran_cap extends produit_typeecran { };
class produit_typeecran_exactitude extends produit_typeecran { };
?>