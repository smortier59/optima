<?
/** Classe facture_fournisseur_affaire
* @package Optima
* @subpackage Absystech
*/

class facture_fournisseur_affaire extends classes_optima {
	/**
	* Constructeur !
	*/
	public function __construct() {
		parent::__construct($table_or_id);
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(			
			'facture_fournisseur_affaire.id_facture_fournisseur',
			'facture_fournisseur_affaire.id_affaire',
			'facture_fournisseur_affaire.nb_produit'			
		);

		$this->colonnes['primary'] = array(
			 "id_facture_fournisseur"
			,"id_affaire"
			,"nb_produit"
		);	

		$this->panels['primary'] = array("visible"=>true,'nbCols'=>3);

		$this->fieldstructure();


		$this->foreign_key["id_facture_fournisseur"] = "facture_fournisseur";
	}

}
?>