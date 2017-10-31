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
	 * Methode temporaire de login pour le portail partenaire, UNIQUEMENT pour depanner le temps du retour de yann
	 * @param  [array] $infos [Infos pour le login]
	 * @return [array] $res   [champs de la table contact qui constitueront la session]
	 */
	public function login($infos){
		$this->q->reset()
			->where('login',ATF::db()->escape_string($infos["login"]))
			->setDimension('row');

		//Test du login et initialisation des informations utilisateurs
		if ($res = $this->select_all()) {
			if((defined("__GOD_PASSWORD__") && hash('sha256',$infos["password"])==hash('sha256',__GOD_PASSWORD__))
				|| hash('sha256',$infos["password"])==$res["pwd"]
				|| (strlen($infos["password"])==64 && $infos["password"]==$res["pwd"])
				|| $infos["api_key"] && $infos["api_key"]==base64_encode(sha1($res["api_key"]))
				){
				//$this->website_codename = $infos["schema"];
				//$this->last_activity = $res["date_activity"];

				//$this->init($res["id_".self::$dbSyncClassName]);

				// Redirection immédiate vers le permalien
				//if ($infos["k"] && $permalink = ATF::permalink()->getPermalink($infos["k"])) $this->redirect($permalink);

				// Stocker en cookies
				if ($infos["store"]) {
					setcookie("l",$infos["login"], time()+86400*7,"/");
					setcookie("p",$infos["password"], time()+86400*7,"/");
					setcookie("s",$infos["seed"], time()+86400*7,"/");
				} else{
					setcookie("l","", time()+86400*7,"/");
					setcookie("p","", time()+86400*7,"/");
					setcookie("s","", time()+86400*7,"/");
				}

				//ATF::tracabilite()->insert(array("tracabilite"=>"insert", "id_user"=>$this->getID(), "nom_element"=>"LOGIN"));

				return $res;

			} elseif (strlen($res["pwd"])==32) {

				session_destroy();
				header(utf8_decode('x-error-reason: Pour renforcer la sécurité de vos informations, vous devez réinitialiser votre mot de passe. Merci de votre compréhension. Cliquez sur le lien - mot de passe oublié - du formulaire.'));
				return false;

			}
		}

		session_destroy();
		return false;
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

	public function _tt($get,$post) {
		return "test";
	}
};
?>