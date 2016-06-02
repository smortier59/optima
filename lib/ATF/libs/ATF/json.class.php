<?php
/**
* La gestion du json dans ATF (création, génération,...)
* @package ATF
* @version ATF 5 - 2010-01-25
* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
* @copyright Copyright (c) 2003-2010, AbsysTech
*/ 
class json{
	/**
	* Les données du json
	* @var array
	*/
	private $data=array();
			
	/** 
	* Ajoute un élément au json
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string target Le nom du paramètre à insérer
	* @param string or array data les données à insérer
	* @example json.class.php add("cadre_refreshed",$cadre_refreshed)
	*/
	public function add($target,$data){
		$this->data[$target]=$data;
		
	}
	
	/**
	* Renvoie l'élément du json
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string target Le nom du paramètre à insérer
	* @return mixed la valeur du json demandé !
	*/
	public function get($target=NULL){
		if ($target) {
			return $this->data[$target];
		} else {
			return $this->data;
		}
	}
	
	/** 
	* Construit le json avec le cadre_refresh, les notices et les erreurs
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param array $crefresh le cadre_refresh
	* @param object $messages l'objet messages
	* @param string $url
	*/
	public function build($crefresh,$messages,$url=NULL){
		// Erreurs
		$this->add("error",$messages->getErrors());
		
		// Messages
		$this->add("notice",$messages->getNotices());
		
		// Alertes
		$this->add("warning",$messages->getWarnings());
		
		// Cadre refresh
		$this->add("cadre_refreshed",$crefresh);
		
		// Skin courant
		if (ATF::module()->isSingleton() && method_exists(ATF::module(), "skin_from_nom")) {
            if($color=ATF::module()->skin_from_nom((ATF::_g("table"))?ATF::_g("table"):ATF::_g("permatable"))){
                $this->add("skin",ATF::$staticserver."images/skins/".$color."/skin".((ATF::$debug)?"-debug":"").".css");
            } 
        }
		
		// Url
		if($url){
			$this->add("url",$url);
		}
		
		//Nouvelle version d'ATF
		if(ATF::$usr->isNewATF()){
			$this->add("newVersion",true);
		}
	}
	
	/** 
	* Renvoi le json
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string json
	*/
	public function getJson(){
		return json_encode($this->data);
	}
	
	/** 
	* Envoie le json au navigateur
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string le json
	*/
	public function send(){
		//Construction de la requête HTTP au navigateur
		ATF::makeHeaders("application/json",false,0,false);
		
		ATF::db()->makeHeaders();

		//header("Content-type: application/json");
		return $this->getJson();
	}
};
?>