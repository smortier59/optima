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
			,'produit_fournisseur_loyer.frequence_loyer'
			
		);

		$this->colonnes['primary'] = array(
			 "id_fournisseur"=>array("custom"=>true,"autocomplete"=>array("function"=>"autocompleteFournisseurs"))
			,'id_produit'
			,'loyer'	
			,'frequence_loyer'	
			
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_produit');
		$this->colonnes['ligne'] =  array( 	
			 'produit_fournisseur_loyer.id_fournisseur'
			,'produit_fournisseur_loyer.id_produit'
			,'produit_fournisseur_loyer.loyer'
			,'produit_fournisseur_loyer.frequence_loyer'			
		);

		$this->field_nom = "%id_produit% %id_fournisseur%";

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