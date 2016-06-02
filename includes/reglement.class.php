<?
/**
* @package Optima
*/
class reglement extends classes_optima{
	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('reglement.reglement');
		$this->colonnes['primary']=array("reglement");
		
		$this->fieldstructure();
	}
};
?>