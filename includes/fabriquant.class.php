<?php
/** 
* @package Optima
*/
class fabriquant extends classes_optima{
	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('fabriquant.fabriquant');
		$this->colonnes['primary']=array('fabriquant');
		

		$this->field_nom = "%fabriquant%";
		$this->fieldstructure();
		
	}
};
?>