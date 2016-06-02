<?
/**
* @package Optima
*/
class edition extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('edition.edtion');
		$this->colonnes['primary']=array('edition','description');
		
		$this->fieldstructure();
		
	}

};
?>