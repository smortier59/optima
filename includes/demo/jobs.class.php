<?
/** 
* Classe jobs (offre d'emploi)
* @package Optima
* @subpackage AbsysTech
* @codeCoverageIgnore	
*/
class jobs extends classes_optima {
	/**
	* Constructeur !
	*/
	public function __construct() {
		parent::__construct();

		$this->table = __CLASS__; 
		$this->colonnes['fields_column'] = array(
			'jobs.date'=>array("width"=>100,"align"=>"center")
			,'jobs.intitule'
			,'jobs.pole'=>array("width"=>100)
			,'jobs.statut'=>array("width"=>50,"renderer"=>"etat","align"=>"center")
		);
		$this->colonnes['bloquees']['insert'] = array("date,statut"); 
		$this->colonnes['bloquees']['update'] = array("date"); 
		$this->fieldstructure();

	}

};
?>