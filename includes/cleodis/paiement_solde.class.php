<?
/**
* @package Optima
*/
class paiement_solde extends classes_optima{

	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->colonnes['fields_column']=array('paiement_solde.paiement_solde');
		$this->colonnes['primary']=array('paiement_solde');
		
		$this->fieldstructure();
	}
};
?>