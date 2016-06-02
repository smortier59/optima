<?
/**
* @package Optima
*/
class politesse extends classes_optima{
	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('politesse.politesse','politesse.type'=>array("width"=>100,"align"=>"center"));
		
		$this->fieldstructure();
		
	}
};
?>