<?
/**  
* Classe formation_commande_fournisseur
* @package Optima
*/
class formation_commande_fournisseur extends classes_optima {
	/** 
	* Constructeur
	*/
	public function __construct() {
		$this->table = "formation_commande_fournisseur";
		parent::__construct();
		
		$this->colonnes["fields_column"] = array(
			 'formation_commande_fournisseur.id_formation_devis'
			,'formation_commande_fournisseur.date_envoi'=>array("renderer"=>"updateDate","width"=>170)
			,'formation_commande_fournisseur.date_retour'=>array("renderer"=>"updateDate","width"=>170)			
			,'genereBDCF'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"formation_genereBonCommandeF","width"=>150)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70)
			,'fichier_retour'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>100,"renderer"=>"uploadFile")
		);
			
		
		$this->colonnes['primary'] = array(
			 "id_formation_devis"
			,"id_formation_commande"
			,"objectif"
			,"id_user"
		);

		$this->fieldstructure();
		
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true, "no_upload"=>true);

		$this->no_insert = true;
		$this->no_delete = true;
		
		$this->no_update_all = false; // Pouvoir modifier massivement	


		$this->addPrivilege("uploadFile","update");			
	}

	/** 
	* Surcharge de l'insert afin de créer le pdf
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}
		$this->infoCollapse($infos);		

		ATF::db($this->db)->begin_transaction();
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			
			ATF::db($this->db)->commit_transaction();
		}


		if(is_array($cadre_refreshed)){	ATF::formation_devis()->redirection("select",$infos["id_formation_devis"]);	}
		return $this->cryptId($last_id);			
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
		$id_formation_commande = ATF::formation_commande()->decryptId(ATF::_r('id_formation_commande'));	
		switch ($field) {				
			case "id_formation_devis" :
				return ATF::formation_commande()->select($id_formation_commande , "id_formation_devis");
			case "id_formation_commande" :
				return $id_formation_commande;
			case "id_user" : return 97; 	    						
			case "date_envoi" :
				return date("Y-m-d");
		}
		return parent::default_value($field);
	}

};

class formation_commande_fournisseur_cleodisbe extends formation_commande_fournisseur { };
class formation_commande_fournisseur_cap extends formation_commande_fournisseur { };
