<?
/** Classe collaborateur
* @package Optima
* @subpackage LM
*/
class collaborateur extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = 'affaire';
		$this->colonnes["fields_column"] = array(
			 'collaborateur.nom'
			,"collaborateur.prenom"
			,"collaborateur.email"
			,"collaborateur.enable"
			,"collaborateur.id_magasin"
		);
		$this->fieldstructure();


		$this->field_nom = "%nom% %prenom%";
	}
}