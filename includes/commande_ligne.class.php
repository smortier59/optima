<?
/**
* Classe commande_ligne
* @package Optima
*/
class commande_ligne extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "commande";

		$this->field_nom = "id_commande_ligne";
	}

	/**
    * Permet d'avoir les lignes de devis dans l'ordre d'insertion
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	function select_all($order_by=false,$asc='desc',$page=false,$count=false,$parent=false){
		$asc="asc";
		$this->q->reset('limit,page');
		return parent::select_all($order_by,$asc,$page,$count);
	}
};
?>