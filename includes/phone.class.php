<?
/** 
* Classe phone
* @package Optima
*/
class phone extends classes_optima {
	/**
	* Constructeur
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function __construct(){
		parent::__construct();
		$this->colonnes["fields_column"] = array(
			'phone.phone'
			,'phone.sip'
			,'phone.id_asterisk'
			,'phone.id_user'
		);
		//Speed Insert dans les préférences
		$this->colonnes["speed_insert"] = array(
			'phone'	
			,'sip'
			,'id_asterisk'
		);
		//On bloque le speed insert dans les generic_input et notamment sur le module user
		$this->colonnes["blocked_speed_insert"]=true;
		//Champ caché
		$this->colonnes["speed_insert_hidden"] = array(
			"id_user"=>array("name"=>"phone[id_user]","value"=>ATF::$usr->getId())
		); 
		//Redirection par défaut
		$this->defaultRedirect["insert"]="select_all";
		$this->defaultRedirect["update"]="select_all";
		$this->fieldstructure();
	}

	/**
	* Surcharge insert
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function insert($infos=NULL,&$s=NULL,$files=NULL,&$cadre_refreshed){
		$this->infoCollapse($infos);
		//Ajout via le speed_insert
		if($infos["notpl2div"]){
			ATF::$cr->add("main","preference");
			unset($infos["notpl2div"]);
			unset($infos["speed_insert"]);
			return parent::insert($infos,$s,$files);
		//Ajout mode classique
		}else{
			return parent::insert($infos,$s,$files,$cadre_refreshed);
		}
	}
};
?>