<?php
/**
* Le rafraichisseur, appelé  courrament "cadre_refresh" Permet la construction de template pour les appels Ajax (avec javel)
* La construction des templates s'effectue via l'objet Smarty_ATF
* @date 2010-01-25
* @package ATF
* @version 5
* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
*/ 
class crefresh{
	/**
	* Les données du cadre_refresh
	* @var array
	*/
	private $data=array();
	
	/**
	* Les données par défaut
	* @var array
	*/
	private $default_data=array();
	
	/**
	* Divs bloqués (blacklist)
	* @var array
	*/
	private $blocked=array();
	
	/**
	* Prédicat sur la génération
	* @var bool
	*/
	private $is_generated=false;
	
	/**
	* Objet smarty utilisé
	* @var mixed
	*/
	private $smarty_obj=NULL;
	
	/**
	* Objet json
	* @var mixed
	*/
	private $json_obj=NULL;
	
	/**
	* Objet message
	* @var mixed
	*/
	private $msg_obj=NULL;
	
	/**
	* Url (ancre) à mettre à jour
	* @var string
	*/
	private $url="";
	
	/** 
	* Création de l'objet cadre refresh
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param object $smarty_obj L'objet smarty à utiliser afin de générer les templates de façon cohérente.
	* @param object $json_obj L'objet json ou seront rafraichit les templates
	* @param msg $messages l'objet message
	* @param array $div_refresh Les divs à rafraichir par défaut
	*/
	public function __construct(&$smarty_obj,&$json_obj,&$messages,$div_refresh=NULL){
		$this->smarty_obj=$smarty_obj;
		$this->json_obj=$json_obj;
		$this->msg_obj=$messages;
		//Ajout des div_refresh par défaut
		if(is_array($div_refresh)){
			foreach($div_refresh as $div){
				$this->addDefault($div,$div.".tpl.htm");
			}
		}
	}
		
	/** 
	* Ajoute un élément à rafraichir
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $target_div Le div cible à rafraichir
	* @param array $vars les variables à ajoutées au template (assign vars)
	* @param bool $default true si c'est un div de raffraichissement par défaut( comme le top, left,...)
	* @param string $template Le nom du template
	* @param bool $default True si c'est un cr par défaut que l'on désire ajouter
	* @param bool $override True si on désire écraser l'ancienne valeur
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	*/
	public function add($target_div,$template="generic.tpl.htm",$vars=NULL,$function="fetchWithAnalyzer",$default=false,$override=false){
		if(!$override && ( ($default && isset($this->default_data[$target_div])) || isset($this->data[$target_div]) ) ){
			return $this;
		}
		
		if ($template && substr($template,-8)!=".tpl.htm" && substr($template,-7)!=".tpl.js"){
			$template .= ".tpl.htm";
		}
		
		$tmp=array(
			"vars"=>((is_array($vars) && (count($vars)>=1))?$vars:array()),
			"template"=>(($template)?$template:"generic.tpl.htm"),
			"function"=>$function
		);
		if($default){
			$this->default_data[$target_div]=$tmp;
		}else{
			$this->data[$target_div]=$tmp;
		}
		
		return $this;
	}
	
	/**
	* Ajoute une variable à un cadre de destination (target_div). Cela permet de rajouter des variables à d'autre moments dans le code comme par exemple pour les cadres par défaut (top)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param $target_div string L'élément ciblé
	* @param $var array La variable dans un tableau (clé : nom de la variable)
	*/
	public function addVar($target_div,$var){
		if($this->data[$target_div] && is_array($var)){
			$this->data[$target_div]["vars"]+=$var;
		}
		if($this->default_data[$target_div] && is_array($var)){
			$this->default_data[$target_div]["vars"]+=$var;
		}
	}
	
	/** 
	* Ajoute un élément à rafraichir
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $target_div Le div cible à rafraichir
	* @param array $vars les variables à ajoutées au template (assign vars)
	* @param string $template Le nom du template
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	*/
	public function addDefault($target_div,$template="generic.tpl.htm",$vars=NULL,$function="fetchWithAnalyzer"){
		return $this->add($target_div,$template,$vars,$function,true);
	}

	/** 
	* Ajoute un élément déjà construit en HTML à rafraichir
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $target_div Le div cible à rafraichir
	* @param array $vars les variables à ajoutées au template (assign vars)
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	
	*/
	public function addHtml($target_div,$html){
		$this->data[$target_div]=array("html"=>$html);
		return $this;
	}
	
	/** 
	* Génère les templates à partir du paramètre data
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param array $data L'attribut data
	* @return array $cadre_refreshed
	*/
	private function generateFromData($data){
		if(!is_array($data)) throw new errorATF("data_incorrect");
		$cadre_refresh=array();
		foreach($data as $target=>$div){
			if(!$this->isBlocked($target)){
				//Assign des variables
				if($div["vars"]){
					$this->smarty_obj->array_assign($div["vars"]);
				}
				//Génération du template
				if($div["html"]){
					$cadre_refresh[$target]=$div["html"];
				}else{
					$cadre_refresh[$target]=$this->smarty_obj->{$div["function"]}($div["template"]);
				}
			}
		}
		return $cadre_refresh;
	}
	
	/**
	* Génère le cadre_refresh dans l'objet json, Cette méthode ne doit être appelée qu'une seule fois
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param boolean $default true si on désre raffraichir les default_div
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	*/
	public function generate($default=true){
		if($this->is_generated) throw new errorATF("already_generated_cadre_refresh");
		$cadre_refresh=array();
		//Write session
		//ATF::env()->saveSession();
		
		//Temporisation de sortie
		$ob_level_before=ob_get_level();
		ob_start();
		try{
			//Génération des templates donnés
			$cadre_refresh+=$this->generateFromData($this->data);
		
			//Génération des templates par défaut
			$cadre_refresh+=$this->generateFromData($this->default_data);
		}catch(Exception $e){
			//Gestion de l'erreur
			ATF::errorProcess($e,true);
			//Remise à zéro du cadre_refresh
			$cadre_refresh=array();
		}
		//Flush du tampon de la sortie standard
		//Patch : On revient à l'ob_level obtenu avant l'affichage des templates ! (et non l'ob_level 0)
		$ob_level_after=ob_get_level();
//log::logger("niveau ob_level_before=".$ob_level_before,"jgwiazdowski");
//log::logger("niveau ob_level_after=".$ob_level_after,"jgwiazdowski");
		if($ob_level_after>$ob_level_before){
			for($i=$ob_level_before;$i<$ob_level_after;$i++){
				ob_end_clean();
			}
		}
//log::logger("RELOAD niveau ob_level_before=".ob_get_level(),"jgwiazdowski");
		//log::logger();
/*for ($xxx=100;$xxx<10000;$xxx+=100) {
        $s = json_encode(substr($cadre_refresh['main'],0,$xxx));
        log::logger($xxx.": ".strlen($s),ygautheron);
        if ($s=='null') {
        log::logger(json_last_error(),ygautheron);
                log::logger(substr($cadre_refresh['main'],0,$xxx+2),ygautheron);
                break;
        }
}*/

/*      METHODE DE DEBUG ERREUR JAVASCRIPT       */
//log::logger($cadre_refresh,"cadre_refresh");

		//Génération du json
		$this->json_obj->build($cadre_refresh,$this->msg_obj,$this->getUrl());
		
		//Generate finished
		$this->is_generated=true;
		return $this;
	}
	
	/**
	* Renvoie le fameux tableau du cadre Refreshed
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $targerDiv Le nom de la cible
	* @param bool $template_only Donne uniquement le nom des templates (à des fins de debug lisible !)
	* @param bool $default True si c'est un cr par défaut que l'on désire obtenir
	* @return array $cadre refreshed
	*/
	public function getCrefresh($targetDiv=false,$template_only=false,$default=false){
		if($default){
			$data=$this->default_data;
		}else{
			$data=$this->data;
		}
		if ($targetDiv) {
			if($template_only){
				return $data[$targetDiv]["template"];
			}else{
				return $data[$targetDiv];
			}
		} else {
			if($template_only){
				$tmp=array();
				foreach($data as $div=>$details){
					$tmp[$div]=$details["template"];
				}
				return $tmp;
			}else{
				return $data;
			}
		}
	}
	
	/** 
	* Supprime un élément placé dans le cadre refresh (même dans le default)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $target_div Le div cible à rafraichir
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	*/
	public function rm($targetDiv){
		foreach (explode(",",$targetDiv) as $k=>$i) {
			if($this->data[$i]){
				unset($this->data[$i]);
			}elseif($this->default_data[$i]){
				unset($this->default_data[$i]);
			}
		}
		return $this;
	}
	
	/**
	* Test si l'élément est bloqué
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $target_div Le div cible à rafraichir
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	*/
	public function isBlocked($targetDiv){
		return in_array($targetDiv,$this->blocked);
	}
	
	/** 
	* Ajoute un élément dans une blacklist. A la suite de cet ajout les divs concernés ne seront jamais générés.
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $target_div Le div cible à rafraichir
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	*/
	public function block($targetDiv){
		if(!in_array($targetDiv,$this->blocked)){
			array_push($this->blocked,$targetDiv);
		}
		return $this;
	}
	
	/**
	* Retire un élément de la black-list
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $target_div Le div cible à rafraichir
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	*/
	public function deblock($targetDiv){
		$key=array_search($targetDiv,$this->blocked);
		if($key!==false){
			unset($this->blocked[$key]);
		}
		return $this;
	}
	
	/** 
	* Réinitialise le cadre_refresh
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	*/
	public function reset(){
		$this->data=array();
		return $this;
	}
	
	/** 
	* Réinitialise le cadre_refresh default (top, left,...)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return $this Retourne l'objet pour pouvoir joindre des méthodes en chaine
	*/
	public function resetDefault(){
		$this->default_data=array();
		return $this;
	}
	
	/**
	* Initialise l'objet smarty (nécessaire pour la génération du cr)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param mixed $smarty L'objet smarty
	*/
	public function setSmartyObj(&$smarty){
		$this->smarty_obj=$smarty;
	}
	
	/**
	* Initialise l'objet json (nécessaire pour la génération du cr)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param mixed $json L'objet json
	*/
	public function setJsonObj(&$json){
		$this->json_obj=$json;
	}
	
	/**
	* Initialise l'objet message (nécessaire pour la génération du cr)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param mixed $msg L'objet message
	*/
	public function setMsgObj(&$msg){
		$this->msg_obj=$msg;
	}
	
	/**
	* Initialise l'url ajax
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $url
	*/
	public function setUrl($url){
		$this->url=$url;
	}
	
	/**
	* Donne L'url en cours
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string l'url ajax
	*/
	public function getUrl(){
		return $this->url;
	}
};
?>