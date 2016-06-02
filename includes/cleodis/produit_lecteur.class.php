<?	
/** Classe produit_lecteur
* @package Optima
* @subpackage Cleodis
*/
class produit_lecteur extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_lecteur";
		$this->colonnes['fields_column'] = array( 
			 'produit_lecteur.produit_lecteur'
		);

		$this->colonnes['primary'] = array(
			"produit_lecteur"
		);
		
		$this->colonnes["speed_insert"] = array(
			'produit_lecteur'	
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

class produit_lecteur_cleodisbe extends produit_lecteur { };
class produit_lecteur_cap extends produit_lecteur { };
class produit_lecteur_exactitude extends produit_lecteur { };
?>