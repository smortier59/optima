<?
/**
* @package Optima
*/
class livraison extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = __CLASS__; 
		
		//Colonnes SELECT ALL
		$this->colonnes['fields_column'] = array('livraison.id_societe','livraison.livraison','livraison.date','bon_de_livraison'=>array("custom"=>true,"nosort"=>true));
		$this->colonnes['bloquees']['insert']  = array('id_livraison');

		//Colonnes SELECT 
		$this->colonnes['primary'] = array(//'id_affaire'
											//,'id_societe'
											//,'id_commande'
											'livraison'
											,'date'
											,'id_transporteur'
											//,'id_expediteur'
											,'code_de_tracabilite'
											);
		
		//IMPORTANT, complète le tableau de colonnes avec les infos MYSQL des colonnes
		$this->fieldstructure();		
		
		/*Non affichage des icones insert/update/delete*/
		$this->no_insert = true;
		$this->no_update = true;
	}

};
?>