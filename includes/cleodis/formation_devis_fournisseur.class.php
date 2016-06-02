<?
/**  
* Classe formation_commande_fournisseur
* @package Optima
*/
class formation_devis_fournisseur extends classes_optima {
	/** 
	* Constructeur
	*/
	public function __construct() {
		$this->table = "formation_devis_fournisseur";
		parent::__construct();
		
		$this->colonnes["fields_column"] = array(
			 'formation_devis_fournisseur.id_formation_devis'
			,'formation_devis_fournisseur.id_societe'
			,'formation_devis_fournisseur.type'
			,'formation_devis_fournisseur.montant'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"€","type"=>"decimal","renderer"=>"money")		
		);	
		

		$this->fieldstructure();

		$this->no_insert = true;
		$this->no_delete = true;
		$this->no_update = true;					
	}
};

class formation_devis_fournisseur_cleodisbe extends formation_devis_fournisseur { };
class formation_devis_fournisseur_cap extends formation_devis_fournisseur { };
class formation_devis_fournisseur_exactitude extends formation_devis_fournisseur { };
