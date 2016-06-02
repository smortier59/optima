<?	
/** Classe produit_besoins
* @package Optima
* @subpackage Cleodis
*/
class produit_besoins extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = "produit_besoins";
		$this->colonnes['fields_column'] = array( 
			 'produit_besoins.produit_besoins',
			 'produit_besoins.ordre'=>array("width"=>80,"rowEditor"=>"setInfos")
		);

		$this->colonnes['primary'] = array(
			"produit_besoins",
			'ordre'
		);

		$this->colonnes["speed_insert"] = array(
			'produit_besoins',
			'ordre'	
		);
		
		$this->fieldstructure();	
		$this->controlled_by = "produit";

		$this->addPrivilege("setInfos","update");
	}


	/**
	 * Permet de modifier un champs en AJAX
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @return bool
	 */
	public function setInfos($infos){
		$res = $this->u(array("id_produit_besoins"=> $this->decryptId($infos["id_produit_besoins"]),
						  $infos["field"] => $infos[$infos["field"]])
					);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}		
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

class produit_besoins_cleodisbe extends produit_besoins { };
class produit_besoins_cap extends produit_besoins { };
class produit_besoins_exactitude extends produit_besoins { };
?>