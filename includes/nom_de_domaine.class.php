<?
/**
* @package Optima
*/
class nom_de_domaine extends classes_optima {
	function __construct() { //hé
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column']  = array(
			'nom_de_domaine.id_societe'
			,'nom_de_domaine.nom_de_domaine'
			,'nom_de_domaine.serveur_dns'
			,'nom_de_domaine.id_registrar'=>array("width"=>100,"align"=>"center")
			,'nom_de_domaine.date_creation'=>array("width"=>100,"align"=>"center")
			,'nom_de_domaine.date_expiry'=>array("width"=>100,"align"=>"center")
			,'nom_de_domaine.etat'=>array("width"=>30,"align"=>"center","renderer"=>"etat")
		);
		$this->fieldstructure();
	}
};
?>