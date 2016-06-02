<?php
/** 
* Classe des permaliens
* @package ATF
*/
class permalink extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct(){
		parent::__construct();
		$this->table=__CLASS__;
		//$this->db='main';
		$this->getDb();
	}
	
	/** Si on peut accéder à la base main, sinon c'est que la table est sur la base courante
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function getDb(){
		try{
			ATF::db('main')->report();
			$this->db='main';
		}catch(errorATF $e){
			//$this->db=ATF::$codename;
		}
	}
	
	/**
	* Crer un lien  usage unique
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $target 
	* @param string $params 
	* @example create('opportunite,select.ajax','template=opportunite-select&id_opportunite=262')
	* @return string $url
	*/
	public function getPermalink($k){
		$this->q->reset()->addCondition("k",$k)->setDimension('row');
		if ($return = $this->select_all()) {
			$this->update(array(
				"id_permalink"=>$return["id_permalink"]
				,"date_activity"=>date("Y-m-d H:i:s")
			));
			$this->increase($return["id_permalink"],"nb_activity");
		}
		return $return;
	}
	
	/**
	* Crer un lien  usage unique
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $table Le module concerné
	* @param string $action (select,update,delete)
	* @param int $id identifiatnt de l'élément non crypté
	* @param string $extra paramètres supplémentaires (toto=1&titi=2)
	* @param mixed $droits Modules accessibles spars par virgule, ou tableau spcifique qui prendra la place des privileges de ATF::$usr
	*			array 	environnement de droits
	*			int 	id_user forc, celui qui clique sur le lien sera loggu avec les droits de cet utilisateur
	* @param string $skin Nom d'un module pour avoir la bonne couleur de skin, prcision pour le skin.tpl.css
	* @example create('opportunite,select.ajax','div=core&template=opportunite-select&id_opportunite=262','suivi,tache,opportunite')
	* @return string $url
	*/
	public function create($table,$action,$id,$extra,$droits,$skin=NULL) {
		$infos["table"]=$table;
		$infos["action"]=$action;
		$infos["id"]=$id;
		$infos["extra"]=$extra;

		if (is_numeric($droits)) {
			// Autologin
			$infos["id_user"]=$droits;
		} elseif($droits) {
			// Mode invit
			if (is_string($droits)) {
				foreach (explode(",",$droits) as $d) {
					$infos["env"]["privileges"][$d][NULL]["select"]=true;
				}
			} else {
				$infos["env"]["privileges"]=$droits;
			}						
			$infos["env"] = serialize($infos["env"]);
		}
		
		do {
			$infos["k"]=sha1(rand(0,time())); //si un k est deja prsent on en gnre un nouveau
			$this->q->reset()
				->addField('id_'.$this->table)
				->addCondition('k',$infos["k"])
				->setDimension('ffc');
		} while ($infos["k"]==$this->select_all());
		
		$infos["codename"]=ATF::$codename;
		$infos["skin"]=$skin;
		
		return $this->insert($infos);
	}
	
	/**
	* Retourne l'url unique complte
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $id
	* @return string $url
	*/
	public function getURL($id) {
		$path = defined('__PERMALINK_HTTP_HOST__') ? __PERMALINK_HTTP_HOST__ : __MANUAL_WEB_PATH__;
		if ($this->select($id,"env") && defined('__MANUAL_INVITE_PATH__')) $path = __MANUAL_INVITE_PATH__;
		return $path.$this->select($id,"k");
	}
};
?>