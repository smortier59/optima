<?
/** Classe affaire_garant
* @package Optima
* @subpackage Cléodis
*/
class affaire_garant extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = 'affaire';
		$this->colonnes["fields_column"] = array(
		    "affaire_garant.id_affaire"
			,"affaire_garant.id_societe"
			,"affaire_garant.id_contact"
		);
		$this->fieldstructure();
	}
}