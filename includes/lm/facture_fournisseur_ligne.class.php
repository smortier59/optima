<?
/**
* Classe facture_fournisseur_ligne
* @package Optima
* @subpackage Cléodis
*/
class facture_fournisseur_ligne extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = "facture_fournisseur_ligne";

		$this->controlled_by = "facture_fournisseur";

		$this->fieldstructure();
		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
	}
};
?>