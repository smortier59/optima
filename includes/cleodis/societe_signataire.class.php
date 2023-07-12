<?
/** Classe societe_signataire
* @package Optima
* @subpackage Cléodis
*/
class societe_signataire extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = 'societe';
		$this->colonnes["fields_column"] = array(
		    "societe_signataire.id_societe"
			,"societe_signataire.id_contact"
		);
		$this->fieldstructure();
	}
}