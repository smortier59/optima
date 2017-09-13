<?
/** Classe tache
* @package Optima
* @subpackage LMA
*/
require_once dirname(__FILE__)."/../tache.class.php";
class tache_lm extends tache {
	function __construct() {
		$this->table = "tache";
		parent::__construct();

		$this->addPrivilege("valid");
	}


	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Morgan FLEURQUIN <mfleurquind@absystech.fr>
	* @param string $field
	* @return string
    */
	public function default_value($field){
		if(ATF::_r('id_affaire')){
			switch ($field) {
				case "id_societe":
					return ATF::affaire()->select(ATF::_r("id_affaire"), "id_societe");
				break;
			}
		}
		return parent::default_value($field);
	}

	/**
    * Valide une tache
    * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $infos pour la validation
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
    */
	public function valid($infos,&$s,$files=NULL,&$cadre_refreshed){
		if (!$infos['id_tache']) return false;

		$infos['etat'] = "fini";
		$infos['complete'] = 100;
		$infos['date_validation'] = date("Y-m-d H:i");
		$infos['id_aboutisseur'] = ATF::$usr->getID();

		$concerne = false;

		ATF::tache_user()->q->reset()->where("id_tache", $this->decryptId($infos["id_tache"]),"AND")
									 ->where("id_user",ATF::$usr->getID());
		$concerne = ATF::tache_user()->select_all();


		if ($concerne || ATF::$usr->privilege('tache','update')) {
			if (parent::update($infos)) {
				if ($email_envoye=$this->envoyer_mail($infos["id_tache"],"tache_valid")) {
					ATF::$msg->addNotice(ATF::$usr->trans("email_envoye"));
				}
			}
			return $infos['id_tache'];
		}else{
			throw new errorATF("Vous n'avez pas le droit de modifier cette tache");
		}

	}

};


