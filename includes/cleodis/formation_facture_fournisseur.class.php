<?
/**  
* Classe formation_bon_de_commande_fournisseur
* @package Optima
*/
class formation_facture_fournisseur extends classes_optima {
	/** 
	* Constructeur
	*/
	public function __construct() {
		$this->table = "formation_facture_fournisseur";
		parent::__construct();
		
		$this->colonnes["fields_column"] = array(
			'formation_facture_fournisseur.ref'
			,'formation_facture_fournisseur.id_formation_devis'
			,'formation_facture_fournisseur.id_formation_bon_de_commande_fournisseur'			
			,'formation_facture_fournisseur.etat'=>array("renderer"=>"etat","width"=>30)
			,'formation_facture_fournisseur.date'
			,'formation_facture_fournisseur.prix'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"€","type"=>"decimal","renderer"=>"money")
			,'formation_facture_fournisseur.date_paiement'=>array("renderer"=>"updateDate","width"=>170)
			,'formation_facture_fournisseur.date_echeance'=>array("renderer"=>"updateDate","width"=>170)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"align"=>"center","type"=>"file","width"=>100,"renderer"=>"uploadFile")
		);
			
		
		$this->colonnes['primary'] = array(
			'ref'
			,"id_formation_bon_de_commande_fournisseur"
			,"id_societe"
			,"numero_dossier"
			,"ref"
			,"prix"
			,"tva"
			,"date"				
		);

		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['clone'] =  
		$this->colonnes['bloquees']['update'] =  array_merge(array('id_formation_devis','etat',));

		$this->fieldstructure();
		
		$this->field_nom = "%ref%";		

		$this->no_insert = true;
		
		$this->no_update_all = false; // Pouvoir modifier massivement	

		$this->files["fichier_joint"]  = array("type"=>"pdf","no_generate"=>true);
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field){
		$id_bdc = ATF::formation_bon_de_commande_fournisseur()->decryptId(ATF::_r('id_formation_bon_de_commande_fournisseur'));
		$id_devis =  ATF::formation_bon_de_commande_fournisseur()->select(ATF::formation_bon_de_commande_fournisseur()->decryptId(ATF::_r('id_formation_bon_de_commande_fournisseur')) , "id_formation_devis");
		ATF::formation_priseEnCharge()->q->reset()->where("id_formation_devis", $id_devis);
		$formation_priseEnCharge = ATF::formation_priseEnCharge()->select_row();

		switch ($field) {				
			case "id_formation_bon_de_commande_fournisseur" :  return ATF::formation_bon_de_commande_fournisseur()->decryptId(ATF::_r('id_formation_bon_de_commande_fournisseur'));
			case "date" :  		return date("Y-m-d");
			case "tva" :  		return 1.2;
			case "numero_dossier" : 		return $formation_priseEnCharge["ref"];
			case "id_societe" : return ATF::formation_bon_de_commande_fournisseur()->select($id_bdc , "id_fournisseur");
			case "prix"	:		return ATF::formation_bon_de_commande_fournisseur()->select($id_bdc , "montant");
		}		
		return parent::default_value($field);
	}

	/** 
	* Surcharge de l'insert afin d'insérer les lignes de devis de créer le si il n'existe pas
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$this->infoCollapse($infos);

		$infos["id_formation_devis"] = ATF::formation_bon_de_commande_fournisseur()->select($infos["id_formation_bon_de_commande_fournisseur"] , "id_formation_devis");						

		$last_id=parent::insert($infos,$s);

		if(is_array($cadre_refreshed)){	ATF::formation_devis()->redirection("select",$infos["id_formation_devis"]);	}
		return $last_id;
	}




};

class formation_facture_fournisseur_cleodisbe extends formation_facture_fournisseur { };
class formation_facture_fournisseur_cap extends formation_facture_fournisseur { };
class formation_facture_fournisseur_exactitude extends formation_facture_fournisseur { };
?>