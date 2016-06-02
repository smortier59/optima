<?
/**
 * Classe contact
 * @package Optima
 * @subpackage AbsysTech
 */
require_once dirname(__FILE__)."/../contact.class.php";
class contact_absystech extends contact {
	private $teamviewerMail=NULL;
	/**
	 * Constructeur
	 */

	public function __construct() {
		parent::__construct();
		$this->colonnes['fields_column']['sendMailTeamViewer'] = array("renderer"=>"sendTeamviewer","custom"=>true, "width"=>50);
		
		$this->colonnes['bloquees']['export'] =
		$this->colonnes['bloquees']['select'] =
		$this->colonnes['bloquees']['update'] =
		$this->colonnes['bloquees']['insert'] = array_merge(array("sendMailTeamViewer"));

		$this->addPrivilege("sendMailTeamViewer");

		$this->fieldstructure();
		
	}


	public function sendMailTeamViewer($infos){	
		if(ATF::contact()->select(ATF::contact()->decryptId($infos["id_contact"]), "email")){
			$info_mail["objet"] = "Prise en main à distance";
			$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".ATF::user()->select(ATF::$usr->getID(), "email").">";
			$info_mail["html"] = true;
			$info_mail["contact"] = ATF::contact()->nom(ATF::contact()->decryptId($infos["id_contact"]));
			$info_mail["template"] = 'teamviewer';		
			$info_mail["recipient"] = ATF::contact()->select(ATF::contact()->decryptId($infos["id_contact"]), "email");

			$this->teamviewerMail = new mail($info_mail);
			$this->teamviewerMail->send();
			ATF::$msg->addNotice(loc::mt("Mail envoyé pour le téléchargement de Teamviewer"));
		}else{	throw new error("Le contact n'a pas d'adresse mail renseignée",880); }	
	}
	

	/**
	* Autocomplete sur les contacts 
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	/*public function autocomplete($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q->setSearch($infos["query"])
			->addField("contact.nom","nom")
			->addField("contact.prenom","prenom")
			->addField("contact.tel","tel")
			->addField("contact.gsm","gsm")
			->addField("contact.id_contact","id");	
			
		return parent::autocomplete($infos,false);
	}*/

};

class contact_att extends contact_absystech { };
?>