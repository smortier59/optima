<?
/** 
* Classe emailing_contact, gère les listes de diffusion
* @package Optima
* @author Quentin JANON <qjanon@absystech.fr>
* @todo Refactoring ATF5
*/
class emailing_lien extends emailing {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'emailing_lien.emailing_lien'
			,'emailing_lien.url'=>array("renderer"=>"url")
		);
		
		
		
		$this->fieldstructure();
		$this->formExt=true; 
		$this->addPrivilege("iFromPlugin","insert");
		$this->addPrivilege("autocomplete");
		$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Liens_d%27emailing";
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
	public function default_value($field,&$s,&$request){
		switch ($field) {
			case "url":
				return "http://";
				break;
			default:
				return parent::default_value($field,$s,$request);
		}
	
	}	
	
	/**
    * Permet l'insertion rapide d'un lien via le plugin EXTJS contenu dans le HTML Editor
    * @author Quentin JANON <qjanon@absystech.fr>
	* @date 04-11-2010
	* @return int ID
    */   	
	public function iFromPlugin($infos,&$s,&$request){
		$this->infoCollapse($infos);
		if (!$infos['emailing_lien']) return false;
		if (!preg_match("(http://)",$infos['emailing_lien'])) {
			$infos['emailing_lien'] = "http://".$infos['emailing_lien'];
		}
		$infos['url'] = $infos['emailing_lien'];
		$return = parent::insert($infos,$s,$request);
		return array("id"=>$return,"lib"=>$infos['url']);
	}	

	/**
    * Autocmplete spécifique, id NON CRYPTE
    * @author Quentin JANON <qjanon@absystech.fr>
	* @date 04-11-2010
	* @return array
    */   	
	public function autocomplete($infos,&$s,&$request){
		$return = parent::autocomplete($infos,$s,$request);
		if ($return) {
			foreach ($return as $k=>$i) {
				$r[$k] = array($this->decryptId($i[0]),$i[1]);
			}
		}
		return $r;
	}	

};
?>