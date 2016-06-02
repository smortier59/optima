<?
/** 
* @package Optima
*/
class compte_absystech extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('compte_absystech.compte_absystech',
												'compte_absystech.type','compte_absystech.code');
		$this->colonnes['primary']=array("compte_absystech",
											"type","code");
		
		$this->fieldstructure();
	}
};
?>