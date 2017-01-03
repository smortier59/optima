<?
/**
* @package Optima
*/
class compte_produit extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('compte_produit.libelle');
		$this->colonnes['primary']=array("libelle");

		$this->fieldstructure();
		$this->field_nom= "%libelle%";
	}
};
?>