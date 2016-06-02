<?
/** 
* Classe Commande fournisseur (bon de commmande)
* @package Optima
*/
class bon_de_commande_ligne extends classes_optima {
	public function __construct() { 
		parent::__construct(); 
		$this->table = __CLASS__;
		$this->controlled_by = "bon_de_commande";
		
	}
};
?>