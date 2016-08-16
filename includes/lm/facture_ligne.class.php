<?	
/** Classe facture_ligne
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../facture_ligne.class.php";
class facture_ligne_lm extends facture_ligne {
	function __construct() {
		parent::__construct(); 
		$this->controlled_by = "facture";
		$this->colonnes['fields_column'] = array( 
			 'facture_ligne.produit'
			,'facture_ligne.quantite'
			,'facture_ligne.ref'
			,'facture_ligne.prix_achat'=>array("renderer"=>"money")
			,'facture_ligne.afficher'
		);

		$this->colonnes['primary'] = array(
			"id_commande"
			,"id_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::produit()->autocompleteMapping
			))
			,"id_fournisseur"
		);
		
		$this->colonnes['bloquees']['insert'] = array('id_facture_ligne','id_facture');
		
		$this->fieldstructure();
		
		$this->foreign_key['id_fournisseur'] =  "societe";
		$this->no_insert=false;
		$this->no_update=false;
		$this->no_delete=false;
	}
};
?>