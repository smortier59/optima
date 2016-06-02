<?
/**
* @package Optima
*/
class horaire extends classes_optima{
	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('horaire.horaire');
		$this->colonnes['primary']=array("horaire");
		
		$this->fieldstructure();
	}
};
?>