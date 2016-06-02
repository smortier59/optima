<?
/**
* Classe Ordre de Mission
* @package Optima
*/
class ordre_de_mission extends classes_optima {
	/**
	* Mail de création d'une mission
	* @var mixec
	*/
	private $mail_mission;
	
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(
			'ordre_de_mission.ordre_de_mission'
			,'ordre_de_mission.id_user'
			,'ordre_de_mission.id_societe'
			,'ordre_de_mission.date'=>array("width"=>100,"align"=>"center")
			,'ordre_de_mission.etat'=>array("width"=>30,"renderer"=>"etat","align"=>"center")
			,'actions'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"actionsODM","width"=>40)
			,'ordre_de_mission.id_hotline'
			,'fichier_joint'=>array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file","format"=>"pdf", "renderer"=>"scanner")
		);
		
		$this->colonnes['primary'] = array(
			'ordre_de_mission'		
			,'id_user'
			,'date'
			,'etat'
		);
											
		$this->colonnes['panel']['lieu'] = array(
			"adresse"
			,"adresse_2"
			,"adresse_3"
			,"cp"
			,"ville"
			,"moyen_transport"
		);
		
		$this->fieldstructure();
		$this->panels['lieu'] = array("visible"=>true);
		$this->colonnes['bloquees']['update'] = array("etat","id_societe","id_contact","id_hotline");	
		$this->colonnes['bloquees']['insert'] = array("etat","id_societe","id_contact","id_hotline");	
		$this->files["fichier_joint"] = array("type"=>"pdf","no_upload"=>true);
		$this->field_nom = "%ordre_de_mission.ordre_de_mission%";
		//$this->selectAllExtjs = false;
	}
	
	
	/**
    * Insert des enregistrements dans la table appelant cette méthode
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos 
	* @param array $s SESSION 
    * @return int  id si enregistrement ok 
    */
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		if (is_array($infos) && isset($infos['id_hotline'])) {
			$infos['id_societe'] = ATF::hotline()->select($infos['id_hotline'],'id_societe');
			$infos['id_contact'] = ATF::hotline()->select($infos['id_hotline'],'id_contact');
		}
		
		//log::logger($infos,"jgwiazdowski");
		//Création du pdf dans l'insert		
		$infos['filestoattach'] = array("fichier_joint");
		if ($id_ordre_de_mission = parent::insert($infos,$s,$files,$cadre_refreshed)){
			$this->createMail($id_ordre_de_mission,$infos["id_user"],$infos["id_contact"],$infos["id_societe"],$infos["date"]);
			$this->sendMail();
		}
		
		ATF::hotline()->redirection('select',$infos['id_hotline'],"hotline-select-".$this->cryptId($infos['id_hotline']).".html");
		
		return $id_ordre_de_mission;
	}
	
	/**
	* Créé un mail d'ordre de mission
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param int $id_ordre_de_mission L'odm en cours
	* @param int $id_user L'utilisateur qui a créé l'odm
	* @param int $id_contact Le contact assigné à l'odm
	* @param int $id_societe La société concernée
	* @param string $date La date de planification de mission
	*/
	private function createMail($id_ordre_de_mission,$id_user,$id_contact,$id_societe,$date){
		//Destinataires
		$destinataire = ATF::user()->select($id_user,'email');
		
		if(!$destinataire) throw new error("null_recipient_mail");
		
		if ($d = ATF::contact()->select($id_contact,'email')) {
			$destinataire .=",".$d;
		}
		
		//Objet
		$objet="[".ATF::societe()->nom($id_societe)." | ".ATF::user()->nom($id_user)."] Mission planifiée pour le ".ATF::$usr->date_trans($date,"force");
	
		//Expéditeur
		$from= "Support AbsysTech <noreply@absystech.fr>";
	
		//Création du mail
		$this->mail_mission = new mail(array("recipient"=>$destinataire, 
								"objet"=>$objet,
								"template"=>'ordre_de_mission',
								"ordre_de_mission"=>$this->select($id_ordre_de_mission),
								"from"=>$from));			
		
		//Pièce jointe
		$this->mail_mission->addFile($this->filepath($id_ordre_de_mission,"fichier_joint"),"ordre_mission.pdf",true);
	}
	
	/**
	* Envoi le mail de planification de mission
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function sendMail(){
		if(!$this->mail_mission) throw new error("null_mission_mail");
		return $this->mail_mission->send();
	}
	
	/**
	* Donne le mail actuel
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return mixed
	*/
	public function getMail(){
		//Current mail
		if(!$this->mail_mission) throw new error("null_mission_mail");
		return $this->mail_mission;
	}
	
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field){
		switch ($field) {
			case "adresse":
			case "adresse_2":
			case "adresse_3":
			case "cp":
			case "ville":
			case "id_pays":
				if(ATF::_r("id_hotline")) {
					$id_societe = ATF::hotline()->select(ATF::_r('id_hotline'),"id_societe");					
					return ATF::societe()->select($id_societe,$field);
				}
			case "id_user":
				return ATF::$usr->getID();
			case "ordre_de_mission":
				return ATF::hotline()->select(ATF::_r('id_hotline'),"hotline");;
			default:
				return parent::default_value($field);
		}
	
	}	 
};	
?>