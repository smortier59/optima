<?	
/** Classe pack_produit_ligne
* @package Optima
* @subpackage Cleodis
*/
class pack_produit_ligne extends classes_optima {
	function __construct() {
		parent::__construct(); 
		$this->table = "pack_produit_ligne";
		$this->controlled_by = "pack_produit";
		$this->colonnes['fields_column'] = array( 
			 'pack_produit_ligne.produit'
			,'pack_produit_ligne.quantite'
			,'pack_produit_ligne.type'
			,'pack_produit_ligne.ref'
			,'pack_produit_ligne.id_fournisseur'
			,'pack_produit_ligne.neuf'
			,'pack_produit_ligne.prix_achat'=>array("renderer"=>"money")
		);

		$this->colonnes['primary'] = array(
			"id_pack_produit"
			,"id_produit"=>array("autocomplete"=>array(
				"mapping"=>ATF::produit()->autocompleteMapping
			))
			,"id_fournisseur"
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_pack_produit_ligne','id_pack_produit')	;
		$this->colonnes['ligne'] =  array( 	
			 "pack_produit_ligne.id_produit"=>array("hidden"=>true)
			,"pack_produit_ligne.produit"
			,"pack_produit_ligne.quantite"
			,"pack_produit_ligne.type"
			,"pack_produit_ligne.ref"
			,"pack_produit_ligne.id_fournisseur"
			,"pack_produit_ligne.prix_achat"
			,'pack_produit_ligne.neuf'
			,'pack_produit_ligne.commentaire'
		);

		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
	
		$this->fieldstructure();	
		$this->foreign_key['id_fournisseur'] =  "societe";
	}

	

  	
	
};

class pack_produit_ligne_cleodisbe extends pack_produit_ligne { };
class pack_produit_ligne_cap extends pack_produit_ligne { };
class pack_produit_ligne_exactitude extends pack_produit_ligne { };

?>