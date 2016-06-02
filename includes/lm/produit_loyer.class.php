<?	
/** Classe produit_loyer
* @package Optima
* @subpackage Cleodis
*/
class produit_loyer extends classes_optima {
	
	function __construct() {
		parent::__construct(); 
		$this->table = __CLASS__;
			$this->colonnes['fields_column'] = array( 
			 'produit_loyer.duree'=>array("align"=>"right","suffix"=>"x")
			,'produit_loyer.loyer'=>array("aggregate"=>array("min","max"),"renderer"=>"money")
			,'produit_loyer.type'
		);

		$this->colonnes['primary'] = array(
			"duree"
			,"loyer"
			,"type"
			,"id_produit"
		);
		
		$this->colonnes['bloquees']['insert'] =  array('id_produit');
		$this->colonnes['ligne'] =  array( 	
			"produit_loyer.loyer"
			,"produit_loyer.duree"
			,"produit_loyer.type"
		);
		
		$this->no_insert=true;
		$this->no_update=true;
		$this->no_delete=true;
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