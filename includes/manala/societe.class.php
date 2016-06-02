<?
/** Classe societe
* @package Optima
* @subpackage MANALA
*/
require_once dirname(__FILE__)."/../societe.class.php";
class societe_manala extends societe {

	/**	* Constructeur par défaut
	*/ 
	public function __construct() { 
		parent::__construct();
		$this->table = "societe";

		$this->addPrivilege("getGed");
	}

	/**
    * Retourne les s
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @return string
    */   	
	public function getGed($infos){
		if($infos['id']){
			$societe = $this->select($infos['id']);
			ATF::ged()->q->reset()->where('id_societe',$societe['id_societe']);
			$return = ATF::ged()->sa();
		} else if ($infos['id_mission']) {
			$mission = ATF::mission()->select($infos['id_mission']);
			$societe = $this->select($mission['id_societe']);
			ATF::ged()->q->reset()->where('id_societe',$societe['id_societe']);
			$return = ATF::ged()->sa();
		}
		if ($return) {
			foreach ($return as $k=>$i) {
				$ged = ATF::ged()->select($i['id_ged']);
				$r[$k] = array(
					"boxLabel"=>$ged['ged'].($ged['description']?"(".$ged['description'].")":""),
					"inputValue"=>$ged['id_ged'],
					"name"=>"mission[ged][]"
				);
				if ($infos['id_mission']) {
					ATF::mission_ged()->q->reset()->where('id_mission',$infos['id_mission'])->where('id_ged',$ged['id_ged']);
					if (ATF::mission_ged()->select_row()) {
						$r[$k]["checked"]=true;
					}
				}
			}
			
			return $r;	
		} else {
			return false;
		}	


	}
	

		
};

?>