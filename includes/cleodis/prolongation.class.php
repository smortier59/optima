<?	
/** 
* Classe prolongation
* @package Optima
* @subpackage Cléodis
*/
class prolongation extends classes_optima {	
	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		$this->table = "prolongation"; 
		$this->colonnes['fields_column'] = array( 
			 'prolongation.ref'
			 ,'prolongation.date_debut'
			 ,'prolongation.date_fin'
			 ,'prolongation.id_refinanceur'
   			 ,'prolongation.date_fin'
			 ,'facturation'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
			 ,'retour_avenant'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>60)
   			 ,'prolongation.date_arret'=>array("renderer"=>"updateDate","width"=>170)
		);

		$this->colonnes['primary'] = array(
			"ref"=>array("disabled"=>true),
			"id_societe"=>array("disabled"=>true),
			"id_affaire"=>array("disabled"=>true),
			"id_refinanceur",
			"date_debut"=>array("disabled"=>true),
			"date_arret"
		);

		$this->colonnes['panel']['loyer_prolongation_lignes'] = array(
			"loyer_prolongation"=>array("custom"=>true)
		);
		
		$this->colonnes['panel']['total'] = array(
			"prix"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
		);

		
		$this->panels['loyer_prolongation_lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>1);
		$this->controlled_by = "commande";
		// Champs masqués
		$this->colonnes['bloquees']['insert'] = 
		$this->colonnes['bloquees']['cloner'] = 
		$this->colonnes['bloquees']['update'] = array("date_fin","date_arret");	
		$this->fieldstructure();
		$this->files["facturation"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"force_generate"=>true);
		$this->files["retour_avenant"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		
		$this->no_insert = true;
		$this->no_update = true;
		$this->selectAllExtjs=true; 
	}
	
	public function uploadFileFromSA(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$infos['display'] = true;
		$class = ATF::getClass($infos['extAction']);
		if (!$class) return false;
		if (!$infos['id']) return false;
		if (!$files) return false;
		
		$id = $class->decryptID($infos['id']);
		
		$id_affaire = $class->select($id, "id_affaire");

		foreach ($files as $k=>$i) {
			if (!$i['size']) return false;
			$id_pdf_affaire = ATF::pdf_affaire()->insert(array("id_affaire"=>$id_affaire, "provenance"=>ATF::$usr->trans($class->name(), "module")." ".$k." ref : ".$class->select($id, "ref")));
			$this->store($s,$id,$k,$i);

			copy($class->filepath($id,$k), ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint"));
			
		}
		ATF::$cr->block('generationTime');
		ATF::$cr->block('top');
		
		
		
		$o = array ('success' => true );
		return json_encode($o);
	}

	/**
	* Permet de modifier la date sur un select_all
	* @param array $infos id_table, key (nom du champs à modifier),value (nom du champs à modifier)
	* @return boolean
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function updateDate($infos){

		if ($infos['value'] == "undefined") $infos["value"] = "";

		$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
		$infosMaj["id_".$this->table]=$infos["id_".$this->table];
		$infosMaj[$infos["key"]]=$infos["value"];
		
		if(array_key_exists("date_arret",$infosMaj)){
			return $this->updateFinProlongation($infosMaj,$infos);
		}elseif($this->u($infosMaj)){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
				,ATF::$usr->trans("notice_success_title")
			);
		}
		return true;
	}

	/**
	* Permet de modifier l'etat sur une modif de date de validite
	* @param array $infosMaj
	* @param array $infos
	* @return array $infosMaj
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function updateFinProlongation($infosMaj,$infos){
		$prolongation=$this->select($infosMaj["id_prolongation"]);

//*****************************Transaction********************************
		ATF::db($this->db)->begin_transaction();

		if($infosMaj[$infos["key"]]){
			$infosMaj["date_arret"]=date("Y-m-d",strtotime($infosMaj["date_arret"]));
			if($infosMaj["date_arret"]<$prolongation["date_debut"]){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("La date insérée est inférieure à la date de prolongation",879);
			}

			ATF::facturation()->q->reset()->addCondition("id_affaire",$prolongation["id_affaire"],"AND")
										  ->addCondition("date_periode_fin",$infosMaj["date_arret"],"AND",false,">=");
			
			$facturation=ATF::facturation()->sa();
			
			//On supprime toutes les facturations pour ne pas qu'elles soient facturées
			foreach($facturation as $item){
				//Sauf si elles ont déjà été facturées
				if($item["id_facture"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Il existe une facture (".ATF::facture()->nom($item["id_facture"]).") sur la période ".$item["date_periode_debut"]." - ".$facturation["date_periode_fin"]."",878);
				}else{
					ATF::facturation()->d($item["id_facturation"]);
				}
			}

			$this->u(
						array(
							"id_prolongation"=>$prolongation["id_prolongation"],
							"date_arret"=>$infosMaj["date_arret"]
						)
					);
			
		}else{
			$this->u(
						array(
							"id_prolongation"=>$prolongation["id_prolongation"],
							"date_arret"=>NULL
						)
					);
		}

		ATF::db($this->db)->commit_transaction();
//		ATF::db($this->db)->rollback_transaction();
		return true;
	}

	/**
    * Retourne la valeur par défaut spécifique aux données des formulaires
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field,&$s,&$request){
		if ($id_commande = ATF::_r('id_commande')) {
			$commande=ATF::commande()->select($id_commande);
		}
		switch ($field) {
			case "id_societe":
				return $commande["id_societe"];
			case "id_affaire":
				return $commande["id_affaire"];
			case "id_refinanceur":
				return 4;
			case "ref":
				return $commande["ref"];
			case "date_debut":
				return date("Y-m-d",strtotime($commande["date_evolution"]."+1 day"));
		}
	
		return parent::default_value($field,$s,$request);
	}	
	
	/**
    * Permet de savoir s'il existe une prolongation pour une affaire
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_affaire
	* @return boolean
    */   	
	public function existProlongation($id_affaire){
		$this->q->reset()->addCondition("id_affaire",$id_affaire)
						 ->setCount();
		$count=$this->sa();
		if($count["count"]>0){
			return true;
		}else{
			return false;
		}
	}

	/**
    * Renvoi unr prolongation relié a une affaire
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id_affaire
	* @return array
    */   	
	public function getByaffaire($id_affaire){
		$this->q->reset()->addCondition("id_affaire",$id_affaire)->setDimension('row');
		return $this->sa();
	}

	/** 
	* Surcharge de l'insert afin d'insérer les lignes de devis de créer l'affaire si elle n'existe pas
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		
		$infos_loyer_prolongation = json_decode($infos["values_".$this->table]["loyer_prolongation"],true);
		$this->infoCollapse($infos);

		$commande=ATF::commande()->select($infos["id_commande"]);
		$infos["ref"]=$commande["ref"];
		if($commande["date_evolution"]){
			$infos["date_debut"]=date("Y-m-d",strtotime($commande["date_evolution"]."+1 day"));
			$date_debut=$infos["date_debut"];
		}else{
			throw new errorATF("Un contrat ne peut pas avoir de prolongation s'il n'a pas de date de fin !",875);
		}
		unset($infos["prix"],$infos["id_commande"]);		
		
		if($infos_loyer_prolongation){
//*****************************Transaction********************************
			ATF::db($this->db)->begin_transaction();

			////////////////Loyers
			$infos["id_prolongation"]=parent::insert($infos,$s,$files);
			foreach($infos_loyer_prolongation as $key=>$item){
				foreach($item as $k=>$i){
					$k_unescape=util::extJSUnescapeDot($k);
					$item[str_replace("loyer_prolongation.","",$k_unescape)]=$i;
					unset($item[$k]);
				}
	
				$item["id_affaire"]=$infos["id_affaire"];
				$item["id_prolongation"]=$infos["id_prolongation"];
				$item["index"]=util::extJSEscapeDot($key);
				$item["date_debut"]=$date_debut;
				unset($item["loyer_total"]);
				if($item["frequence_loyer"]){
					if($date_debut){
						$item["date_fin"]=$date_debut;
						if($item['duree']){
							if($item['frequence_loyer']=="an"){
								$frequence=12;
							}elseif($item['frequence_loyer']=="semestre"){
								$frequence=6;
							}elseif($item['frequence_loyer']=="trimestre"){
								$frequence=3;
							}else{
								$frequence=1;
							}
							
							$item["date_fin"]=date("Y-m-d",strtotime($item['date_debut']."+".($frequence*$item['duree'])." month"));
							$item["date_fin"]=date("Y-m-d",strtotime($item["date_fin"]."- 1 day"));
							
							$infos["date_fin"]=$item["date_fin"];
							
							$date_debut=date("Y-m-d",strtotime($infos['date_fin']."+ 1 day"));
							
						}
					}
					ATF::loyer_prolongation()->i($item,$s);
				}else{
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Il n'y a pas de fréquence pour un loyer",876);
				}
			}
			

			if($infos["date_arret"] && $infos["date_fin"]>=date("Y-m-d",strtotime($infos["date_arret"]))){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("Erreur : la date d'arrêt (".date("Y-m-d",strtotime($infos["date_arret"])).") est inférieur à la date de fin (".$infos["date_fin"].") ",880);
			}
			
			$this->u($infos);
			$objCommande = new commande_cleodis($commande['id_commande']);
			ATF::facturation()->insert_facturation_prolongation($objCommande,$prolongation["date_debut"]);
//*****************************************************************************
			ATF::db($this->db)->commit_transaction();
			
			if(is_array($cadre_refreshed)){
				ATF::affaire()->redirection("select",$infos["id_affaire"]);
			}
			return $infos["id_prolongation"];
		}else{
			throw new errorATF("Prolongation sans loyer",877);
		}
		
	}


	
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		ATF::db($this->db)->begin_transaction();
		if (is_numeric($infos) || is_string($infos)) {
			$id=$this->decryptId($infos);
			$prolongation=$this->select($id);
			parent::delete($id,$s);
			$this->delete_file($id);
			ATF::facturation()->delete_special($prolongation["id_affaire"],"prolongation");
		} elseif (is_array($infos) && $infos) {
			foreach($infos["id"] as $key=>$item){
				$this->delete($item,$s,$files);
			}
		}
		ATF::db($this->db)->commit_transaction();
		ATF::affaire()->redirection("select",$prolongation["id_affaire"]);
		return true;
	}
	
	
	/** 
	* Permet de rendre null les dates de prolongation d'une affaire et de ses loyer_prolongation
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_affaire 
	* @param string $type 
	*/
	function unsetDate($id_affaire){
		$this->q->reset()->addCondition("id_affaire",$id_affaire)
						 ->setDimension("row");
		$prolongation=$this->sa();
		if($prolongation){
			ATF::loyer_prolongation()->q->reset()->addCondition("id_prolongation",$prolongation["id_prolongation"]);
			$loyer_prolongation=ATF::loyer_prolongation()->sa();
			foreach($loyer_prolongation as $key=>$item){
				$item['date_debut']=NULL;
				$item['date_fin']=NULL;
				ATF::loyer_prolongation()->u($item);
			}
			$prolongation['date_debut']=NULL;
			$prolongation['date_fin']=NULL;
			$this->u($prolongation);
		}
		return true;
	}
};
