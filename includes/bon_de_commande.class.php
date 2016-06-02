<?
/** 
* Classe Commande fournisseur (bon de commmande)
* @package Optima
*/
class bon_de_commande extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array('bon_de_commande.ref'
												,'bon_de_commande.id_affaire'
												,'bon_de_commande.id_fournisseur'
												,'bon_de_commande.resume'
												,'bon_de_commande.etat'
												);
		$this->fieldstructure();		
	}
};
?>