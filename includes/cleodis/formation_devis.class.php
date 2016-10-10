<?
/**
* Classe formation_devis
* Cet objet permet de gérer les devis des formations
* @package Optima
*/
require_once dirname(__FILE__)."/../formation_devis.class.php";
class formation_devis_cleodis extends formation_devis {

	/**
	* Constructeur par défaut
	*/
	public function __construct() {		
		$this->table = "formation_devis";
		parent::__construct();	
		 
		/*-----------Colonnes Select all par défaut------------------------------------------------*/
		$this->colonnes['fields_column'] = array(
			 'formation_devis.numero_dossier'	
			,'formation_devis.id_societe'
			,'formation_devis.thematique'
			,'formation_devis.id_formateur'
			,'formation_devis.id_lieu_formation'
			,'formation_devis.id_apporteur_affaire'
			,'formation_devis.etat'=>array("renderer"=>"etat","width"=>30)
			,'formation_devis.date_retour'=>array("renderer"=>"updateDate","width"=>170)			
			,'formation_devis_etendre'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"formation_devisExpand","width"=>150)
			,'perdu'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"formation_devisPerdu","width"=>50)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70)		
			,'fichier_retour'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70,"renderer"=>"uploadFile")	
		);
		
		// Panel prinicpal
		$this->colonnes['primary'] = array(
			"id_societe"=>array("disabled"=>true)			
			,"id_formateur"
			,"id_lieu_formation"
			,"id_apporteur_affaire"
			,"numero_dossier"			
			,"thematique"
			,"type"=>array("data"=>array("normal","light"),"xtype"=>"combo","listeners"=>array("change"=>"ATF.formation_devis_type"))
			,"nb_heure"			
			,"date"
			,"etat"
			,"date_retour"
			,"montantHT"			
			,"id_contact"=>array(
				"obligatoire"=>true,
				"autocomplete"=>array(
					"function"=>"autocompleteAvecMail"
					,"mapping"=>array(
						array('name'=> 'email', 'mapping'=> 0)
						,array('name'=>'id', 'mapping'=> 1)
						,array('name'=> 'nom', 'mapping'=> 2)
						,array('name'=> 'detail', 'mapping'=> 3, 'type'=>'string' )
						,array('name'=> 'nomBrut', 'mapping'=> 'raw_2')
					)
				)
			),"date_validite"
			,"id_owner"
		);

		$this->colonnes['panel']['lignes'] = array(
				"formation_devis_ligne"=>array("custom"=>true)
			);

		$this->colonnes['panel']['fournisseurs'] = array(
				"formation_devis_fournisseur"=>array("custom"=>true)
			);

		$this->colonnes['panel']['prix_formation'] = array(
			"prix"
			,"acompte"
			,"remuneration_of"
		);	

		$this->colonnes['panel']['participants'] = array(
			"contact"=>array("custom"=>true)  
		);

		$this->colonnes['panel']['light'] = array(
			 "nb_participants"
		    ,"opca"
		);


		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['clone'] =  
		$this->colonnes['bloquees']['update'] =  array_merge(array('etat',"montantHT", "numero_dossier"));

		$this->colonnes['bloquees']['select'] =  array_merge(
			array_keys($this->colonnes['panel']['lignes'])
			,array_keys($this->colonnes['panel']['fournisseurs'])
			,array_keys($this->colonnes['panel']['participants'])			
		);


		$this->autocomplete = array(
			"view"=>array("formation_devis.id_formation_devis","formation_devis.id_societe")
		);


 		$this->fieldstructure();

 		$this->field_nom = "%numero_dossier% - %thematique%";

 		$this->foreign_key["id_formateur"] = "contact";
 		$this->foreign_key["id_apporteur_affaire"] = "societe";
 		$this->foreign_key["id_lieu_formation"] = "societe";
 		$this->foreign_key["id_owner"] = "user";

 		$this->panels['prix_formation'] = array('nbCols'=>2,'visible'=>true);
		$this->panels['participants'] = array("visible"=>true, 'nbCols'=>2);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['fournisseurs'] = array("visible"=>true, 'nbCols'=>1);

		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true, "no_upload"=>true);
		$this->files["fichier_retour"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->no_delete = true;
		$this->no_insert = true;

		$this->onglets = array(	
			"formation_participant"	
			,"formation_devis_ligne"
			,"formation_devis_fournisseur"	 
			,"formation_priseEnCharge"
			,"formation_commande"
			,"formation_commande_fournisseur"
			,"formation_attestation_presence"
			,"formation_facture"
			,"formation_bon_de_commande_fournisseur"
			,"formation_facture_fournisseur"
			,"suivi"
		);

		$this->addPrivilege("uploadFile","update");
		$this->addPrivilege("getMontantFournisseur");
		$this->addPrivilege("getMontantForFacture");
		$this->addPrivilege("getPriseEnCharge");
		$this->addPrivilege("perdu","update");


	}


	/** 
	* Impossible de modifier un devis qui n'est pas en attente
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id
	* @return boolean 
	*/
	public function can_update($id,$infos=false){
		if($this->select($id,"etat")=="attente"){
			return true;
		}else{
			throw new errorATF("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car il n'est plus en '".ATF::$usr->trans("attente")."'",892);
			return false; 
		}
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
			case "id_owner": return ATF::$usr->getID(); 
						
			case "date_validite":
				return date("Y-m-d", strtotime("+15 day"));
		}
		return parent::default_value($field);
	}

	public function opcaIn($id, $i){
		if($id){			
			$opcas = explode("|", $id);
			if(in_array($i["id_societe"], $opcas)){
				return true;
			}
		}		
		return false;
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
		$formation_devis_ligne =  json_decode($infos["values_formation_devis"]["formation_devis_ligne"],true);
		$formation_devis_fournisseur =  json_decode($infos["values_formation_devis"]["formation_devis_fournisseur"],true);

		$this->infoCollapse($infos);

		$infos["numero_dossier"] = $this->getRef(date("Y-m-d"));

		$participants = $infos["contact"];
		$opcas = $infos["opca"];
		
		unset($infos["contact"],$infos["opca"]);				
				
		if($infos["type"] != "light"){
			if(!$participants){	throw new errorATF("Il faut au moins 1 participant à la formation",875);	}

			if(!$formation_devis_ligne){  throw new errorATF("Il faut au moins 1 date pour la formation",875); }
			if(!$formation_devis_fournisseur){  throw new errorATF("Il faut au moins 1 fournisseur pour la formation",875); }
		}
		

		ATF::db($this->db)->begin_transaction();
//*****************************Transaction********************************
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);
		//Lignes reprise
		if($participants){
			foreach($participants as $key=>$item){
				ATF::formation_participant()->insert(array("id_formation_devis"=>$last_id, "id_contact"=>ATF::contact()->decryptId($item)));
			}
		}

		sort($formation_devis_ligne);
		if($formation_devis_ligne){
			foreach ($formation_devis_ligne as $key => $value) {				
				ATF::formation_devis_ligne()->insert(array("id_formation_devis"=>$last_id, 
														   "date"=>substr($value["formation_devis_ligne__dot__date"], 0 , 10),
														   "date_deb_matin"=> $value["formation_devis_ligne__dot__date_deb_matin"],
														   "date_fin_matin"=> $value["formation_devis_ligne__dot__date_fin_matin"],
														   "date_deb_am"=> $value["formation_devis_ligne__dot__date_deb_am"],
														   "date_fin_am"=> $value["formation_devis_ligne__dot__date_fin_am"]
													));
			}
		}
		
		if($formation_devis_fournisseur){
			foreach ($formation_devis_fournisseur as $key => $value) {				
				ATF::formation_devis_fournisseur()->insert(array("id_formation_devis"=>$last_id,															   
															   "id_societe"=> ATF::societe()->decryptId($value["formation_devis_fournisseur__dot__id_societe_fk"]),
															   "type"=> $value["formation_devis_fournisseur__dot__type"],
															   "montant"=>$value["formation_devis_fournisseur__dot__montant"]															   
														));
			}
		}

		if($opcas){
			$infos["opca"] = "";
			foreach ($opcas as $key => $value) {
				$infos["opca"] .= ATF::societe()->decryptId($value);
				if($key !== count($opcas)-1){
					$infos["opca"] .= "|";
				}
			}
		}		


		$montantHT = $infos["prix"]*count($participants)*$infos["nb_heure"];
		$montantHT = round($montantHT);
		parent::update(array("id_formation_devis"=> $last_id, "montantHT"=> $montantHT));
		


//*****************************************************************************
		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			
			ATF::db($this->db)->commit_transaction();
		}
		if(is_array($cadre_refreshed)){	ATF::formation_devis()->redirection("select",$last_id);	}
		return $last_id;
	}


	/** 
	* Surcharge de l'update afin d'insérer les lignes de devis de créer le si il n'existe pas
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}

		$formation_devis_ligne =  json_decode($infos["values_formation_devis"]["formation_devis_ligne"],true);
		$formation_devis_fournisseur =  json_decode($infos["values_formation_devis"]["formation_devis_fournisseur"],true);			

		$this->infoCollapse($infos);

		
		$participants = $infos["contact"];
		$opcas = $infos["opca"];

		unset($infos["contact"], $infos["opca"]);
		
		if($this->select($infos["id_formation_devis"], "etat") != "attente"){
			throw new errorATF("Impossible de modifier un devis qui n'est plus en attente !",875);
		}		
		
		if(!$participants){
			throw new errorATF("Il faut au moins 1 participant à la formation",875);
		}
		if(!$formation_devis_ligne){  throw new errorATF("Il faut au moins 1 date pour la formation",875); }
		if(!$formation_devis_fournisseur){  throw new errorATF("Il faut au moins 1 fournisseur pour la formation",875); }


		ATF::db($this->db)->begin_transaction();
//*****************************Transaction********************************
		$infos["id_formation_devis"] = $this->decryptId($infos["id_formation_devis"]);

		$last_id=$infos["id_formation_devis"];

		if($opcas){
			$infos["opca"] = "";
			foreach ($opcas as $key => $value) {
				$infos["opca"] .= ATF::societe()->decryptId($value);
				if($key !== count($opcas)-1){
					$infos["opca"] .= "|";
				}
			}
		}


		parent::update($infos,$s,NULL,$var=NULL,NULL,true);

		//On supprime tout les participants éxistant
		ATF::formation_participant()->q->reset()->where("id_formation_devis", $last_id);
		foreach (ATF::formation_participant()->select_all() as $key => $value) {
			ATF::formation_participant()->d($value["id_formation_participant"]);
		}
		//Lignes reprise
		if($participants){
			foreach($participants as $key=>$item){
				ATF::formation_participant()->insert(array("id_formation_devis"=>$last_id, "id_contact"=>ATF::contact()->decryptId($item)));
			}
		}
				
		//On supprime tout les dates éxistantes
		ATF::formation_devis_ligne()->q->reset()->where("id_formation_devis", $last_id);
		foreach (ATF::formation_devis_ligne()->select_all() as $key => $value) {
			ATF::formation_devis_ligne()->d($value["id_formation_devis_ligne"]);
		}
		sort($formation_devis_ligne);
		if($formation_devis_ligne){
			foreach ($formation_devis_ligne as $key => $value) {				
				ATF::formation_devis_ligne()->insert(array("id_formation_devis"=>$last_id, 
														   "date"=>substr($value["formation_devis_ligne__dot__date"], 0 , 10),
														   "date_deb_matin"=> $value["formation_devis_ligne__dot__date_deb_matin"],
														   "date_fin_matin"=> $value["formation_devis_ligne__dot__date_fin_matin"],
														   "date_deb_am"=> $value["formation_devis_ligne__dot__date_deb_am"],
														   "date_fin_am"=> $value["formation_devis_ligne__dot__date_fin_am"]
													));
			}
		}


		//On supprime tout les dates éxistantes
		ATF::formation_devis_fournisseur()->q->reset()->where("id_formation_devis", $last_id);
		foreach (ATF::formation_devis_fournisseur()->select_all() as $key => $value) {
			ATF::formation_devis_fournisseur()->d($value["id_formation_devis_fournisseur"]);
		}
		if($formation_devis_fournisseur){
			foreach ($formation_devis_fournisseur as $key => $value) {				
				ATF::formation_devis_fournisseur()->insert(array("id_formation_devis"=>$last_id,															   
															   "id_societe"=> ATF::societe()->decryptId($value["formation_devis_fournisseur__dot__id_societe_fk"]),
															   "type"=> $value["formation_devis_fournisseur__dot__type"],
															   "montant"=>$value["formation_devis_fournisseur__dot__montant"]															   
														));
			}
		}

		$montantHT = $infos["prix"]*count($participants)*$infos["nb_heure"];
		$montantHT = round($montantHT);
		parent::update(array("id_formation_devis"=> $last_id, "montantHT"=> $montantHT));
		


//*****************************************************************************
		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			
			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){	ATF::formation_devis()->redirection("select",$last_id);	}
		return $last_id;
	}




	/** 
	* Fonction permettant de retourner le nombre de jours ouvrés entre 2 dates
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param date $date_start - la date de début
	* @param date $date_stop - la date de fin
	* @return int le nombre de jours ouvrés entre 2 dates
	*/
	public function get_nb_jours($formation_devis_ligne) {		
		$nb = 0;
		foreach ($formation_devis_ligne as $key => $value) {
			if($value["date_deb_matin"] && $value["date_fin_matin"]){ $nb = $nb+0.5; }
			if($value["date_deb_am"] && $value["date_fin_am"]){ $nb = $nb+0.5; }
		}
		return $nb;

	}



	/** 
	* Méthode permettant de passer l'état d'un formation_devis à perdu
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function perdu($infos,&$s,$files=NULL,&$cadre_refreshed){
		$formation_devis=$this->select($infos["id_formation_devis"]);

		if($formation_devis["etat"]!="gagne"){
			ATF::db($this->db)->begin_transaction();
//***************************Transaction************************************************			
			$this->u(array("id_formation_devis"=>$formation_devis["id_formation_devis"],"etat"=>"perdu"),$s);			

			ATF::db($this->db)->commit_transaction();
////*****************************************************************************
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_formation_devis_perdu"),array("record"=>$this->nom($infos["id_formation_devis"])))
				,ATF::$usr->trans("notice_success_title")
			);
		
			$this->redirection("select_all",NULL,"formation_devis.html");
			return true; 
		}else{	
			throw new errorATF("Impossible de passer un devis gagné en 'perdu'",899);
		}
	}

	public function getCommandeFournisseur($id_devis){
		if($id_devis){
			$id_devis = $this->decryptId($id_devis);
			ATF::formation_commande()->q->reset()->where("id_formation_devis",$id_devis);
			return ATF::formation_commande()->select_all();
		}
		return NULL;
		
	}


	/**
    * Retourne la ref d'un devis formation
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param date $date
	* @return string ref
    */
    function getRef($date){
		$prefix="FCL".date("Y",strtotime($date));
		$this->q->reset()
				->addCondition("numero_dossier",$prefix."%","AND",false,"LIKE")
				->addField('SUBSTRING(`numero_dossier`,8)+1',"max_ref")
				->addOrder('numero_dossier',"DESC")
				->setDimension("row")
				->setLimit(1);
	
		$nb=$this->sa();

		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="0001";
		}
		return $prefix.$suffix;
	}	

	/**
    * Retourne le montant d'un devis formation fournisseur
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos id_formation_devis, id_fournisseur
	* @return float montant
    */
	public function getMontantFournisseur($infos){
		if($infos["id_formation_devis"] && $infos["id_fournisseur"]){
			ATF::formation_devis_fournisseur()->q->reset()->where("id_formation_devis", $this->decryptId($infos["id_formation_devis"]))
														  ->where("id_societe", ATF::formation_devis_fournisseur()->decryptId($infos["id_fournisseur"]));
			$fournisseur =ATF::formation_devis_fournisseur()->select_row();
			if($fournisseur["montant"]){
				return $fournisseur["montant"];
			}
		}
		return 0.00;
	}


	public function getMontantForFacture($infos){
		if($infos["type"] == "acompte"){	return $this->select($infos["id_formation_devis"] , "acompte");  } 
		elseif($infos["type"] == "normale"){ return $this->select($infos["id_formation_devis"] , "montantHT"); }
		return 0.00;
	}

	public function getPriseEnCharge($id_devis){
		$id_devis = $this->decryptId($id_devis);
		ATF::formation_priseEnCharge()->q->reset()->where("id_formation_devis", $id_devis);
		$res = ATF::formation_priseEnCharge()->select_all();
		if(!empty($res)){
			return true;
		}
		return false;
	}


	
};

class formation_devis_cleodisbe extends formation_devis_cleodis { };
class formation_devis_cap extends formation_devis_cleodis { };
?>