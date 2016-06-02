<?
/**
* @package Optima
*/
require_once dirname(__FILE__)."/../gep_projet.class.php";
class gep_projet_demo extends gep_projet {
	function __construct() { // PHP5hé
		parent::__construct();
		
		$this->colonnes['fields_column'] = array(
			 'gep_projet.gep_projet'
			,'gep_projet.id_societe'
			,'gep_projet.description'
			,'gep_projet.date_debut'
			,'pdf'=>array("custom"=>true,"nosort"=>true)
		);

		$this->colonnes['primary'] = array(
			 "id_societe"
			,"gep_projet"
			,"description"
			,"date_debut"
			,"date_fin"
		);
		$this->colonnes['bloquees']['insert'] = 	array(	
			"date"
			,"id_owner"
			,"type"
			,'formulaire'
			,'locked'
		);
		$this->colonnes['bloquees']['update'] = 	array(	
			"date"
			,"id_owner"
			,"type"
			,'formulaire'
			,'locked'
		);

//		$this->colonnes['panel']['formulaire'] = array("formulaire"=>array('custom'=>true));
		
		$this->fieldstructure();

//		$this->panels['formulaire'] = array("visible"=>true);
		
		$this->files = array();
		
		$this->onglets = array('visite'=>array('opened'=>true));
		
		$this->privilege_egal["formulaire"]="update";
		$this->quick_action['select'][] = 'formulaire';
		$this->gmap = array("loadJSOnly"=>true);
		$this->addPrivilege("autocompleteProjetGe");
		$this->addPrivilege("autocompleteProjetAcc");
		$this->addPrivilege("exportOcapi");
		$this->addPrivilege("exportDerogation");
		$this->addPrivilege("exportSPSI");
		$this->addPrivilege("exportSCPI");
		$this->addPrivilege("exportDesordre");
	}
	
	/**
    * Listing avec droits particuliers
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @todo Remplacer les paramètres de select_all par un &$s
    */ 
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!ATF::$usr->get("id_agence")) {
			if ($soc = ATF::$usr->get("id_societe")) {
				// Sinon restriction par societe
				$this->q->addCondition("gep_projet.id_societe",$soc);
			}
		}
		return parent::select_all($order_by,$asc,$page,$count);	
	}

	/**
	* Duplication récursive
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
	*/	
	public function cloner($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$this->infoCollapse($infos);
				
		// Début de la transaction
		ATF::db($this->db)->begin_transaction();
		
		 // Insertion d'une copie de ce projet
		$id_gep_projet = $this->insert($infos,$s,$files,$cadre_refreshed,$nolog);
		
		// Duplication des enfants du projet dupliqué
		$infos["id_gep_projet"] = $this->decryptId($infos["id_gep_projet"]);
		ATF::pa()->q->reset()->addCondition("id_gep_projet",$infos["id_gep_projet"])->addOrder("id_pa","asc")->addConditionNull("id_parent")->setStrict();
		if ($rootChilds = ATF::pa()->select_all()) {
			foreach ($rootChilds as $pa) {
				$pa["id_gep_projet"]=$id_gep_projet;
				ATF::pa()->cloner($pa);
			}
		}
		
		// Commit
		ATF::db($this->db)->commit_transaction();
	}
		
};
?>