<?
class facturation_fournisseur_detail extends classes_optima {
	function __construct($table_or_id=NULL) {
		$this->table="facturation_fournisseur_detail";
		parent::__construct($table_or_id);
		$this->colonnes['fields_column'] = array( 
			  'facturation_fournisseur_detail.id_facturation_fournisseur'
			 ,'facturation_fournisseur_detail.id_produit_fournisseur_loyer'
		);
		$this->fieldstructure();
		

		$this->foreign_key["id_facturation_fournisseur"] = "facturation_fournisseur";
		$this->foreign_key["id_produit_fournisseur_loyer"] = "produit_fournisseur_loyer";
	}	
}