<?	
/** Classe facturation_fournisseur_detail
* @package Optima
* @subpackage LMA
*/
class facturation_fournisseur_detail extends classes_optima {
	function __construct() {
		parent::__construct(); 
		
		$this->colonnes['fields_column'] = array( 			
			'facturation_fournisseur_detail.id_produit_fournisseur_loyer'
			,'facturation_fournisseur_detail.quantite'			
		);
		
		
		$this->fieldstructure();
		
		$this->foreign_key['id_produit_fournisseur_loyer'] =  "produit_fournisseur_loyer";		
		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
	}
};
?>