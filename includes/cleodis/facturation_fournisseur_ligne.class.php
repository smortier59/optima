<?
/**
* Classe facturation_fournisseur_ligne
* @package Optima
* @subpackage Cléodis
*/
class facturation_fournisseur_ligne extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = "facturation_fournisseur_ligne";

		$this->controlled_by = "facturation_fournisseur";




		$this->fieldstructure();
		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
	}
};
