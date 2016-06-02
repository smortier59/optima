<?php
/** Classe pays
* @package ATF
*/
class pays extends classes_optima {
	/**
	* selection optimisée, utile pour les petites tables très souvent sollicitées !
	* @var bool
	*/
	var $memory_optimisation_select = true;

	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "societe";
		$this->colonnes["fields_column"] = array('pays');
		$this->fieldstructure();
	}
	
	/**
    * Retourne la liste des pays trié et formatté pour un menu déroulant
    * @author Quentin JANON <qjanon@absystech.fr>
    * @return array pays 
    */   	
	public function options(){
		return parent::options($this->table,"id_".$this->table,true,$this->table);
	}
	
	/**
	* Méthode spéciale par défaut "saCustom"
	* Appel la méthode de classe particulière à utiliser,  si $method =flase on utilise select_all
	* Utilisation dans generic_select_all
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $s : la session
	* @param string $method : methode de classe particulière à utiliser
	* @return array Résultat de la requête
	*/
	public function select_data(&$s,$method=false){
		if (!$method && $this->desc["id_localisation_langue"]) {		
			$method="saCustom";
		}
		return parent::select_data($s,$method);
	}
			
	/**
    * Filtre la liste des pays pour n'avoir que la liste des pays dans la langue de l'utilisateur courant
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @return array pays 
    */   	
	public function saCustom(){
		$this->q
			->from("pays","id_localisation_langue","localisation_langue","id_localisation_langue")
			->where("localisation_langue.localisation_langue",ATF::$usr->get("id_language"));
		return $this->select_all();
	}
	
	/** retourn l'id en fonction du nom du pays 
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string nom
	*/
	public function from_nom($nom) {
		if (!$nom) return false;
		$this->q->reset()->addField('id_pays')->setStrict()->addCondition('pays',$nom)->setDimension('cell');
		return parent::select_all();
	}
	/** Fonction qui récupère tous les pays disponibles 
	* @author cyril CHARLIER <ccharlier@absystech.fr>
	*/
	public function _GET($get,$post){
		$this->q->reset();

		$this->q->reset()->addField("id_pays")->addField("pays");
		return $this->select_all();
	}
};
?>