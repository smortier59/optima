<?
/**
* Classe formation_devis
* Cet objet permet de gérer les devis des formations
* @package Optima
*/
require_once dirname(__FILE__)."/../formation_commande.class.php";
class formation_commande_cleodis extends formation_commande {

	/**
	* Constructeur par défaut
	*/
	public function __construct() {		
		$this->table = "formation_commande";
		parent::__construct();
		 
		/*-----------Colonnes Select all par défaut------------------------------------------------*/
		$this->colonnes['fields_column'] = array(	
			'formation_commande.id_formation_devis'
			,'formation_commande.ref'			
			,'formation_commande.date'			
			,'formation_commande.date_envoi'=>array("renderer"=>"updateDate","width"=>170)
			,'formation_commande.date_retour'=>array("renderer"=>"updateDate","width"=>170)
			,'genereBDCF'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"formation_genereBDCF","width"=>150)
			,'genereFacture'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"formation_genereFacture","width"=>150)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70)		
			,'fichier_retour'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>100,"renderer"=>"uploadFile")
		);
		
		// Panel prinicpal
		$this->colonnes['primary'] = array(
			"id_formation_devis"=>array("disabled"=>true)			
			,"date"			
		);

		
		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['clone'] =  
		$this->colonnes['bloquees']['update'] =  array_merge(array('ref'));

 		$this->fieldstructure();

 		$this->field_nom = "%ref%";


 		$this->panels['prix_formation'] = array('nbCols'=>2,'visible'=>true);

		$this->files["fichier_joint"]  = array("type"=>"pdf","preview"=>true, "no_upload"=>true);
		$this->files["fichier_retour"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);


		$this->no_insert = true;

		$this->addPrivilege("uploadFile","update");

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
		switch ($field) {				
			case "date" :
				return date("Y-m-d");
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
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}
		$this->infoCollapse($infos);

		ATF::db($this->db)->begin_transaction();

		$infos["id_formation_devis"] = ATF::formation_devis()->decryptId($infos["id_formation_devis"]);

//*****************************Transaction********************************
		$infos["ref"] = ATF::formation_devis()->select($infos["id_formation_devis"] , "numero_dossier");
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);		

		ATF::formation_participant()->q->reset()->where("id_formation_devis" , $infos["id_formation_devis"]);
		$participants = ATF::formation_participant()->select_all();
		
		foreach($participants as $key=>$item){
            ATF::formation_attestation_presence()->insert(array("id_formation_commande"=>$last_id, "id_contact"=>$item["id_contact"], "id_formation_devis"=> $infos["id_formation_devis"]));
        }

        ATF::formation_devis()->u(array("id_formation_devis"=> $infos["id_formation_devis"], "etat"=>"gagne"));

//*****************************************************************************
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
	* Surcharge de delete afin de supprimer les lignes de commande et modifier l'état de l'affaire et du devis
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $infos le ou les identificateurs de l'élément que l'on désire inséré
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		if (is_numeric($infos) || is_string($infos)) {
			$id=$this->decryptId($infos);
			$commande=$this->select($id);

			ATF::db($this->db)->begin_transaction();
			parent::delete($id,$s);

			//Devis
			ATF::formation_devis()->q->reset()->addCondition("id_formation_devis",$commande["id_formation_devis"])->setDimension("row");
			$devis=ATF::formation_devis()->sa();	
			$devis_update = array("id_formation_devis"=>$devis["id_formation_devis"],"etat"=>"attente");
			ATF::formation_devis()->u($devis_update);
							
			ATF::db($this->db)->commit_transaction();
			

		}else{
			foreach ($infos as $key => $value) {
				$this->delete($value);
			}
		}
	}

	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){	
		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {			
			if(!$i["formation_commande.id_formation_devis_fk"]){ $i["formation_commande.id_formation_devis_fk"] = $i["id_formation_devis"]; }
			ATF::formation_bon_de_commande_fournisseur()->q->reset()->where("formation_bon_de_commande_fournisseur.id_formation_devis", $i["formation_commande.id_formation_devis_fk"])->setCount();
			$bdc = ATF::formation_bon_de_commande_fournisseur()->sa();			
			if($bdc["count"]>0) $return['data'][$k]['factureAllow'] = true;
		}
		return $return;
	}


};

class formation_commande_cleodisbe extends formation_commande_cleodis { };
class formation_commande_cap extends formation_commande_cleodis { };
class formation_commande_exactitude extends formation_commande_cleodis { };
?>