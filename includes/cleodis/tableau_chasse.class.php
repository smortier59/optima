<?
/** Classe tableau_chasse
* @package Optima
* @subpackage Cléodis
*/
class tableau_chasse extends classes_optima {
	function __construct() {
		$this->table ="tableau_chasse";
		parent::__construct();

		$this->colonnes['fields_column'] = array(
			 'tableau_chasse.magasin'=>array("width"=>150,"rowEditor"=>"setInfos")
			,'tableau_chasse.tel'=> array("tel"=>true,"renderer"=>"tel","width"=>120,"rowEditor"=>"setInfos")
			,'tableau_chasse.mobile'=> array("tel"=>true,"renderer"=>"tel","width"=>120,"rowEditor"=>"setInfos")
			,'tableau_chasse.identifie'=>array("width"=>150,"rowEditor"=>"setInfos")
			,'tableau_chasse.fonction'=>array("width"=>150,"rowEditor"=>"setInfos")
			,'tableau_chasse.mail'=> array("renderer"=>"email","width"=>250,"rowEditor"=>"setInfos")
			,'tableau_chasse.commentaire'
			,'tableau_chasse.codes'=>array("width"=>150,"rowEditor"=>"setInfos")
			,'tableau_chasse.interesse'=>array("width"=>150,"rowEditor"=>"interesseUpdate")
		);
		
		$this->colonnes['primary'] = array(		 
			 "id_affaire"
			,"magasin"
			,"adresse"	
			,"codes"
			,"interesse"
		);

		$this->colonnes['panel']['infos_contact'] = array(
			 "identifie"
			,"tel"
			,"mobile"
			,"fonction"
			,"mail"
			,"commentaire"
		);

		$this->fieldstructure();

		$this->foreign_key['id_affaire'] =  "affaire";	

		$this->addPrivilege("interesseUpdate");
		$this->addPrivilege("setInfos");

	}


	public function interesseUpdate($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		
		$data["id_tableau_chasse"] = $this->decryptId($infos["id_tableau_chasse"]);
		$data["interesse"] = $infos["interesse"];
				
		if ($r=$this->u($data)) {
			ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success"),array("record"=>$this->nom($data["id_tableau_chasse"]))),ATF::$usr->trans("notice_success_title"));
		}
		return $r;
	}


	/**
	* Mise à jour des infos sur le grid
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function setInfos($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){	
		
		if(!$infos['id_tableau_chasse']) throw new Error(ATF::$usr->trans("erreur_identifiant"),900);
		 
		$data["id_tableau_chasse"] = $infos["id_tableau_chasse"];
		$data[$infos["field"]] = $infos[$infos["field"]];
		
		if ($r=$this->u($data)) {
			ATF::$msg->addNotice(loc::mt(ATF::$usr->trans("notice_update_success"),array("record"=>$this->nom($data["id_tableau_chasse"]))),ATF::$usr->trans("notice_success_title"));
		}
		return $r;		
	}
};