<?
/**
* @package Optima
*/
class frais_kilometrique extends classes_optima {
	
	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;

		$this->colonnes['fields_column'] = array(
			'frais_kilometrique.annee'
			,'frais_kilometrique.cv'
			,'frais_kilometrique.coeff'
			,'frais_kilometrique.type'
		);

		$this->fieldstructure();
		$this->field_nom = "%frais_kilometrique.annee% %frais_kilometrique.type% - %frais_kilometrique.cv%CV";
		
		//$this->helpMeURL = "http://wiki.optima.absystech.net/index.php/Notes_de_frais";
	}
	
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 30-03-2011
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field,&$s=NULL,&$request=NULL){
		switch ($field) {
			case "id_user":
				return ATF::$usr->getID();
			default:
				return parent::default_value($field);
		}
	}	

	/**
    * Renvoi un json avec tous les frais kilométrique de l'année en cours
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 31-03-2011
	* @return JSON
    */   	
	public function getCoeffs($annee=false) {
		$this->q->reset()->where("annee",$annee?$annee:date("Y"));
		foreach ($this->sa() as $k=>$i) {
			$return[$this->cryptID($i['id_frais_kilometrique'])] = $i;
		}
		return json_encode($return);
	}
	
	/**
    * Renvoi tous les frais kilométrique de l'année en cours
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 31-03-2011
	* @return JSON
    */   	
	public function autocomplete($infos,&$s,&$request,$annee=NULL) {
		$this->q->reset()->where("annee",($annee?$annee:date("Y")));
		return parent::autocomplete($infos,false);
	}
	
};
?>