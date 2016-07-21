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
		}else{	throw new errorATF("Le contact n'a pas d'adresse mail renseignée",880); }	
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



	/**
	* Permet de récupérer la liste des contacts pour telescope
	* @package Telescope
	* @author Morgan FLEURQUIB <mfleurquin@absystech.fr> 
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/ 
	//$order_by=false,$asc='desc',$page=false,$count=false,$noapplyfilter=false
	public function _GET($get,$post) {


		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "id_contact";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;

		$colsData = array(
			"contact.id_contact"=>array(),
			"contact.id_societe"=>array("visible"=>false),
			"contact.civilite"=>array(),
			"contact.nom"=>array(),
			"contact.prenom"=>array(),
			"contact.tel"=>array(),
			"contact.gsm"=>array(),
			"contact.email"=>array()
		);
		


		$this->q->reset();

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			$this->q->setSearch($get["search"]);
		}

		if ($get['id']) {
			$this->q->where("id_contact",$get['id'])->setLimit(1);
		} else {
			$this->q->setLimit($get['limit']);

		}



		switch ($get['tri']) {
			case 'id_societe':	
				$get['tri'] = "contact.".$get['tri'];
			break;
		}

		if($get["filter"]){
			foreach ($get["filter"] as $key => $value) {
				if (strpos($key, 'contact') !== false) {
					log::logger($key , "mfleurquin");
					$this->q->addCondition(str_replace("'", "",$key), str_replace("'", "",$value), "AND");
				}
			}
		}

		$this->q->addField($colsData);

		$this->q->from("contact","id_societe","societe","id_societe");


		$data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);

		foreach ($data["data"] as $k=>$lines) {
			foreach ($lines as $k_=>$val) {
				if (strpos($k_,".")) {
					$tmp = explode(".",$k_);
					$data['data'][$k][$tmp[1]] = $val;
					unset($data['data'][$k][$k_]);
				}				
			}
		}

		if ($get['id']) {
	        $return = $data['data'][0];			
		} else {
			// Envoi des headers
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);

	        $return = $data['data'];			
		}

		return $return;
	}	

};

class contact_att extends contact_absystech { };
?>