<?
/** 
* Classe emailing_contact, gère les listes de diffusion
* @author Quentin JANON <qjanon@absystech.fr>
* @package Optima
*/
 class emailing extends classes_optima {
	function __construct() { 
		parent::__construct();
		$this->table = "emailing_projet";
	}
		
	function fromMD5($id,$field=false) {
		$this->q->reset()
					->where("MD5(`id_".$this->table."`)",$id)
					->setDimension("cell")
					->setStrict();
		if ($field) {
			$this->q->addField($field);
		} else {
			$this->q->addField("id_".$this->table);
		}
		return $this->sa();
	}
		
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field,&$s,&$request){
		switch ($field) {
			case "id_user":
				return ATF::$usr->get('id_user');
			break;
			default:
				return parent::default_value($field,$s,$request);
		}
	
	}	
};
?>