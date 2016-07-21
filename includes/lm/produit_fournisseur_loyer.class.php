<?	
/** Classe produit_fournisseur_loyer
* @package Optima
* @subpackage Cleodis
*/
class produit_fournisseur_loyer extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = __CLASS__;
			$this->colonnes['fields_column'] = array( 
			 'produit_fournisseur_loyer.id_fournisseur'
			,'produit_fournisseur_loyer.id_produit'
			,'produit_fournisseur_loyer.loyer'
			,'produit_fournisseur_loyer.nb_loyer'
			,'produit_fournisseur_loyer.periodicite'
			,'produit_fournisseur_loyer.nature'
			,'produit_fournisseur_loyer.departement'
			,'produit_fournisseur_loyer.ordre'
		);

		$this->colonnes['primary'] = array(
			 "id_fournisseur"=>array("custom"=>true,"autocomplete"=>array("function"=>"autocompleteFournisseurs"))
			,'id_produit'
			,'loyer'
			,'nb_loyer'
			,'periodicite'
			,'nature'
			,'departement'
			,'ordre'
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_produit');
		$this->colonnes['ligne'] =  array( 	
			 'produit_fournisseur_loyer.id_fournisseur'
			,'produit_fournisseur_loyer.id_produit'
			,'produit_fournisseur_loyer.loyer'
			,'produit_fournisseur_loyer.nb_loyer'
			,'produit_fournisseur_loyer.periodicite'
			,'produit_fournisseur_loyer.nature'
			,'produit_fournisseur_loyer.departement'
			,'produit_fournisseur_loyer.ordre'
		);

		$this->field_nom= "%id_fournisseur% %id_produit%";

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