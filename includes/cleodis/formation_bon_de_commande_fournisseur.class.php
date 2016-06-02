<?
/**  
* Classe formation_bon_de_commande_fournisseur
* @package Optima
*/
class formation_bon_de_commande_fournisseur extends classes_optima {
	/** 
	* Constructeur
	*/
	public function __construct() {
		$this->table = "formation_bon_de_commande_fournisseur";
		parent::__construct();
		
		$this->colonnes["fields_column"] = array(
			 'formation_bon_de_commande_fournisseur.ref'
			,'formation_bon_de_commande_fournisseur.id_formation_devis'
			,'formation_bon_de_commande_fournisseur.id_societe'
			,'formation_bon_de_commande_fournisseur.id_contact'
			,'formation_bon_de_commande_fournisseur.montant'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"€","type"=>"decimal","renderer"=>"money")
			,'solde_ht'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"€","type"=>"decimal","renderer"=>"money")
			,'generateFFF'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"generateFFF","width"=>150)	
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70)	
		);
			
		
		$this->colonnes['primary'] = array(
			 "id_formation_devis"
			,"ref"
			,"id_societe"			
			,"thematique"
			,"id_fournisseur"=>array("autocomplete"=>array("function"=>"autocompleteFournisseurFormationDevis",
															"mapping"=>array(
																			array('name'=> 'id', 'mapping'=> 0)
																			,array('name'=>'detail', 'mapping'=> 1)	
																			,array('name'=> 'nom', 'mapping'=> 1)
																			,array('name'=> 'nomBrut', 'mapping'=> 'raw_1')
																		)))
			,"montant"
			,"id_contact"=>array(
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
			)
				
			,"date"
			,"commentaire"
			
		);

		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['clone'] =  
		$this->colonnes['bloquees']['update'] =  array_merge(array('ref'));


		$this->fieldstructure();


		$this->foreign_key["id_fournisseur"] = "societe";	

		$this->field_nom = "%ref%";
		
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true, "no_upload"=>true);

		$this->no_insert = true;
		
		$this->no_update_all = false; // Pouvoir modifier massivement			
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
		$id_devis = ATF::formation_devis()->decryptId(ATF::_r('id_formation_devis'));
		
		switch ($field) {				
			case "id_formation_devis" :	return $id_devis;
			case "id_societe" :  		return ATF::formation_devis()->select($id_devis , "id_societe");
			case "thematique" : 		return ATF::formation_devis()->select($id_devis , "thematique");
			case "date" : 				return date("Y-m-d");
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
		$infos["ref"] = $this->getRef(date("Y-m-d"));

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
		$this->infoCollapse($infos);

		ATF::db($this->db)->begin_transaction();
//*****************************Transaction********************************
		$last_id=$infos["id_formation_bon_de_commande_fournisseur"];
		parent::update($infos,$s,NULL,$var=NULL,NULL,true);	
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
		return $last_id;
	}



	/**
    * Retourne la ref
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id_parent
	* @return string ref
    */
    function getRef($date){
		$prefix="FCL".date("Y",strtotime($date));
		$this->q->reset()
				->addCondition("ref",$prefix."%","AND",false,"LIKE")
				->addField('SUBSTRING(`ref`,8)+1',"max_ref")
				->addOrder('ref',"DESC")
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
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q
			->addJointure("formation_bon_de_commande_fournisseur","id_formation_bon_de_commande_fournisseur","formation_facture_fournisseur","id_formation_bon_de_commande_fournisseur")			
			->addField("`formation_bon_de_commande_fournisseur`.`montant` - (SUM(
														IF(
															(`formation_facture_fournisseur`.`prix`)
															,`formation_facture_fournisseur`.`prix`
															,0)
														)
													)"
													,"solde_ht")
			->addGroup("formation_bon_de_commande_fournisseur.id_formation_bon_de_commande_fournisseur");
			
		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			if ($i["solde_ht"]>0 || !$i["solde_ht"]) {
				$return['data'][$k]['factureFournisseurAllow'] = true;	
			}			
		}
		return $return;
	}

	
};

class formation_bon_de_commande_fournisseur_cleodisbe extends formation_bon_de_commande_fournisseur { };
class formation_bon_de_commande_fournisseur_cap extends formation_bon_de_commande_fournisseur { };
class formation_bon_de_commande_fournisseur_exactitude extends formation_bon_de_commande_fournisseur { };
