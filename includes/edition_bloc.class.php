<?
/**
* @package Optima
*/
class edition_bloc extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('edition_bloc.structure');
		$this->colonnes['primary']=array('id_edition','structure','id_module');
		
		$this->fieldstructure();
	}

};
?>