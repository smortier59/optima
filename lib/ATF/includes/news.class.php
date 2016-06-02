<?php
/** 
* Classe module : permet de gérer les news de l'application
* @package ATF
*/
class news extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->db='main';

		$this->colonnes["fields_column"] = array(	
			'news.news'=>array("width"=>150,"align"=>"center")
			,'news.details'
			,'news.date'=>array("width"=>100,"align"=>"center")
		);
		
		/*-----------Colonnes insert/update/select - informations principales-----------------------*/
		$this->colonnes['primary'] = array(
			"news"
			,"date"
			,"redacteur"
		);
		/*-----------Panels-----------------------*/
		$this->colonnes['panel']['resume'] = array(
			"details"
		);  


		$this->fieldstructure();
		$this->addPrivilege("get");
		$this->addPrivilege("getHTML");
		$this->addPrivilege("read");

		$this->panels['resume'] = array("visible"=>true,"nbCols"=>1);

		$this->formExt=true;
	}
	
	/**
    * Retourne le nomber de news non lues
    * @author Quentin JANON <qjanon@absystech.fr>
	* @return int
    */   	
	public function isNews() {
		if (!ATF::$usr->get("last_news") || !ATF::$usr->privilege($this->table)) {
			return false;
		}
		$return = $this->get(true);
		return $return['count'];
	}
	
	/**
    * Renvoi toutes les news non lues
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param bool $count Active/Désactive le décompte
	* @return int
    */   	
	public function get($count=false) {
		$this->q->reset()
					->addCondition("date",ATF::$usr->get("last_news"),NULL,"sup",'>=')
					->addCondition("codename",ATF::$codename,"OR","B")
					->addCondition("type","release","AND","B")
					->addConditionNull("codename","OR","B")
					->addOrder('date','desc');
		if ($count) {
			$this->q->setCount();
		}	
		return $this->select_all();
	}
	
	/**
    * Renvoi le résultat HTML attendu pour afficher les news
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param array $params Paramètre optionnels, utile uniquement pour passer le display
	* @return string Résultat HTML
    */   	
	public function getHTML(&$params){
		$params["display"]=true;
		$data = $this->get();
		foreach ($data as $k=>$i) {
			$data[$k]['news'] = addslashes($i['news']);
			$data[$k]['details'] = addslashes($i['details']);
			$data[$k]['details'] = str_replace(chr(10),"",$data[$k]['details']);
			$data[$k]['details'] = str_replace(chr(13),"",$data[$k]['details']);
		}
		ATF::$html->assign('news',$data);
		return ATF::$html->fetch("news_popup.tpl.htm");
	}
	
	/**
    * Met à jour la date de dernière lecture des news pour l'utilisateur 
    * @author Quentin JANON <qjanon@absystech.fr>
	* @return void
    */   	
	public function read() {
		$u = array("id_user"=>ATF::$usr->getID(),"last_news"=>date("Y-m-d H:i:s"));
		ATF::user()->update($u);
		ATF::$usr->maj();
		ATF::$cr->block();
	}
	
		
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field){
		switch ($field) {
			case "id_user":
				return ATF::$usr->getID();
				break;
			case "redacteur":
				return ATF::$usr->get('prenom')." ".ATF::$usr->get('nom');
				break;
			default:
				return parent::default_value($field);
		}
	
	}	 
	
	/**
    * Force le codename avant de faire l'insertion de la news
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$this->infoCollapse($infos);
		if (ATF::$codename!="absystech") {
			$infos['codename'] = ATF::$codename;
		}
		//ATF::log()->logger($infos,'qjanon');
		return parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);
	}	
	
	/**
    * Force le codename avant de faire l'insertion de la news
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (ATF::$codename!="absystech") {
			$this->q->where("codename",ATF::$codename);
		}
		return parent::select_all($order_by,$asc,$page,$count);
	}	
	
};
?>