<?
/**
* Classe contact
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../contact.class.php";
class contact_cleodis extends contact {
	/*--------------------------------------------------------------*/
	/*                   Constructeurs                              */
	/*--------------------------------------------------------------*/
	public function __construct() {
		parent::__construct();
		$this->table = "contact";
	}

	/**
	* Surcharge de l'update de contact
	* Permet de modifier le responsable de la société lorsqu'il ne fait plus parti de CLEODIS
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 2013-04-12
	* @param array $infos
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		parent::update($infos,$s,$files,$cadre_refreshed);

		if($infos["contact"]["etat"] === "inactif"){
			//Si l'utilisateur fait parti de CLEODIS ou CLEOFI ...
			if(($infos["contact"]["id_societe"] == 246) || ($infos["contact"]["id_societe"] == 4111)|| ($infos["contact"]["id_societe"] == 4225) || ($infos["contact"]["id_societe"] == 4161) || ($infos["contact"]["id_societe"] == 4669) || ($infos["contact"]["id_societe"] == 4670) || ($infos["contact"]["id_societe"] == 5374)){
				//On récupere le user associé au contact
				ATF::user()->q->reset()->where("prenom", $infos["contact"]["prenom"])
									   ->where("nom" , $infos["contact"]["nom"]);
				$user = ATF::user()->select_row();
				ATF::societe()->q->reset()->where("id_owner", $user["id_user"]);
				$soc = ATF::societe()->select_all();
				if($user["id_superieur"]){
					//On check si le superieur fait encore parti de la société
					if(ATF::user()->select($user["id_superieur"] , "etat") == "normal"){
						$infos = array("id_owner" => $user["id_superieur"]);
						$sup = $user["id_superieur"];
					}else{
						$infos = array("id_owner" => 16);
						$sup = 16;
					}
				}else{
					//Si pas de responsable on met Jerome Loison
					$infos = array("id_owner" => 16);
					$sup = 16;
				}

				foreach ($soc as $k => $v){
					$infos["id_societe"] = $v["id_societe"];
					ATF::societe()->u($infos);
					$suivi = array(	"id_user"=>ATF::$usr->get('id_user')
									,"id_societe"=>$v["id_societe"]
									,"type"=>'note'
									,"texte"=>"Changement de responsable suite au changement d'etat de ".$user["prenom"]." ".$user["nom"]." nouveau responsable ".ATF::user()->nom($sup)
									,'public'=>'oui'
									,"type_suivi" => "Autre"
									,"suivi_notifie" => $infos["contact"]["id_superieur"]
								);
					ATF::suivi()->insert($suivi);
				}
			}
		}

	}

	/**
	 * Methode qui prépare la requête de login sur contact
	 * @param  [array] $infos [Infos pour le login]
	 * @param  [array] $infos[p]
	 * @param  [array] $infos[u]
	 * @return [array] $res   [champs de la table contact qui constitueront la session]
	 */
	public function loginQuery($infos){
		$this->q->reset()
			->addField("contact.*")
			->addField("societe.lead", "lead")
			->addField("societe.id_filiale", "id_filiale")
			/*->select('contact.id_societe')
			->select('contact.civilite')
			->select('contact.prenom')
			->select('contact.nom')*/
			->addJointure("contact","id_societe","societe","id_societe")
			->where('contact.login',ATF::db()->escape_string($infos["u"]))
			->setDimension('row');
	}
};

class contact_cleodisbe extends contact_cleodis { };
class contact_cap extends contact_cleodis { };

class contact_midas extends contact_cleodis {
	public function __construct() {
		parent::__construct();
		$this->table = "contact";

		$this->colonnes["fields_column"] = array(
			'contact.prenom'
			,'contact.nom'
			,'contact.id_societe'
			,'contact.fonction'
			,'contact.tel' => array("width"=>120)
			,'contact.gsm' => array("width"=>120)
			,'contact.email' => array("renderer"=>"email","width"=>250)
			,'completer' => array("custom"=>true,"renderer"=>"progress","aggregate"=>array("min","avg"),"width"=>100)
		);

		$this->fieldstructure();
	}

	/** On affiche que les sociétés midas
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function saCustom() {
		$this->q->addJointure("contact","id_societe","societe","id_societe")
				->addCondition("societe.code_client","M%","OR",false,"LIKE")
				->addCondition("societe.divers_3","Midas");
		return parent::saCustom();
	}

};
?>