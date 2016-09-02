<?	
/** Classe produit_fournisseur
* @package Optima
* @subpackage Cleodis
*/
class produit_fournisseur extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = __CLASS__;
			$this->colonnes['fields_column'] = array( 
			 'produit_fournisseur.id_fournisseur'
			,'produit_fournisseur.id_produit'
			,'produit_fournisseur.prix_prestation'
			,'produit_fournisseur.recurrence'
			,'produit_fournisseur.departement'
		);

		$this->colonnes['primary'] = array(
			 "id_fournisseur"=>array("custom"=>true,"autocomplete"=>array("function"=>"autocompleteFournisseurs"))
			,"id_produit"
			,"prix_prestation"
			,"recurrence"
			,"departement"
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_produit');
		$this->colonnes['ligne'] =  array( 	
			 'produit_fournisseur.id_fournisseur'
			,'produit_fournisseur.id_produit'
			,'produit_fournisseur.prix_prestation'
			,'produit_fournisseur.recurrence'
			,'produit_fournisseur.departement'
		);


		$this->foreign_key["id_fournisseur"] = "societe";
		
		
		$this->fieldstructure();	
		$this->selectAllExtjs=true; 
	}

	/**
    * Permet d'avoir les lignes de produit dans l'ordre d'insertion
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */ 
	function select_all($order_by=false,$asc='asc',$page=false,$count=false,$parent=false){

		return parent::select_all($order_by,$asc,$page,$count);
	}
};

?>