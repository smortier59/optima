<?
class adherent_charge extends classes_optima {
	function __construct() {
			parent::__construct();
			
			$this->colonnes["fields_column"] = array(
				 "adherent_charge.id_adherent"	
				,"total_mensuel"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
				,"total_mensuel_conjoint"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
				
				,"total_annuel"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
				,"total_conjoint"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
				,"total_impaye"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
				
			);
			
			$this->colonnes['primary'] = array(
				"id_adherent"=>array("readonly"=>true, "disabled"=>true)
			);
			
			$this->colonnes['panel']['charge']= array(
				"charge_impot"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "impot"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"impot_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"impot_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				"charge_taxe_fonciere"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "taxe_fonciere"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"taxe_fonciere_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"taxe_fonciere_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				"charge_taxe_habitation"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "taxe_habitation"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"taxe_habitation_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"taxe_habitation_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_amende"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "amende_adherent"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield", "width"=> 200, "disabled"=>true)
					,"amende_conjoint"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield", "width"=> 200, "disabled"=>true)				
					,"amende"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_redevance"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "redevance"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"redevance_conjoint"=>array("custom"=>true,"readonly"=>true,"xtype"=>"textfield", "width"=> 200, "disabled"=>true)					
					,"redevance_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),	
				
				"charge_indus"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "indus"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"indus_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"indus_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
					
				"charge_loyer"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "loyer"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"loyer_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"loyer_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_remb_pret_immo"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "remb_pret_immo"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"remb_pret_immo_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"remb_pret_immo_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),	
				
				"charge_electricite"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "electricite"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"electricite_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"electricite_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),				
  				
  				"charge_gaz"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "gaz"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"gaz_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"gaz_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_autre_chauffage"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "autre_chauffage"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"autre_chauffage_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"autre_chauffage_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_eau"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "eau"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"eau_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"eau_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_assu_logement"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "assu_logement"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"assu_logement_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"assu_logement_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
			    
			   "charge_internet"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "internet"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"internet_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"internet_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_mobile"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "mobile"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"mobile_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"mobile_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
			   
			   "charge_autre_habi"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "autre_habi"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"autre_habi_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"autre_habi_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				))
			  
			);
				
			$this->colonnes['panel']['deplacement']= array(				
				"charge_assu_auto"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "assu_auto"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"assu_auto_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"assu_auto_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_entretien_carbu"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "entretien_carbu"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"entretien_carbu_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"entretien_carbu_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
			    
			    "charge_abo_tec"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "abo_tec"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"abo_tec_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"abo_tec_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				 "charge_autre_dep"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "autre_dep"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"autre_dep_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"autre_dep_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				))
			);
			
			$this->colonnes['panel']['banque']= array(
				 "charge_frais_gestion"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "frais_gestion"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"frais_gestion_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"frais_gestion_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				 "charge_acces_compte"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "acces_compte"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"acces_compte_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"acces_compte_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				 "charge_mutuelle"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "mutuelle"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"mutuelle_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"mutuelle_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_pel"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "pel"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"pel_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"pel_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_assurance_vie"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "assurance_vie"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"assurance_vie_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"assurance_vie_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_livret_epargne"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "livret_epargne"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"livret_epargne_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"livret_epargne_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_contrat_obseque"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "contrat_obseque"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"contrat_obseque_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"contrat_obseque_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_autre_banque"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "autre_banque"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"autre_banque_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"autre_banque_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				))
			);
			
			$this->colonnes['panel']['autre']= array(	
				"charge_pension_ali"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "pension_ali"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"pension_ali_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"pension_ali_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
			    
			    "charge_courses"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "courses"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"courses_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"courses_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_habillement"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "habillement"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"habillement_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"habillement_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_transports"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "transports"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"transports_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"transports_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_cantine"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "cantine"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"cantine_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"cantine_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),			    
			   
			   "charge_soins"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "soins"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"soins_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"soins_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_cigarette"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "cigarette"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"cigarette_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"cigarette_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				)),
				
				"charge_presse"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "presse"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)
					,"presse_conjoint"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)					
					,"presse_charge"=>array("listeners"=>array("change"=>"ATF.updateCharge"),"formatNumeric"=>true, "width"=> 200)	
				))		    
			    
			);	
						
			$this->colonnes['panel']['conso_annuelle'] = array(
				 "conso_elec"
				,"conso_gaz"
				,"conso_eau"
			);
			
			$this->colonnes['panel']['total'] = array(
				 "total_mensuel"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
				,"total_mensuel_conjoint"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
				,"total_impaye" =>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
				,"total_annuel" =>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
				,"total_conjoint" =>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
				
			);
			
			$this->panels['primary'] = array('nbCols'=>1,'visible'=>true);
			$this->panels['charge'] = array('nbCols'=>1,'visible'=>true);
			$this->panels['deplacement'] = array('nbCols'=>1,'visible'=>true);
			$this->panels['banque'] = array('nbCols'=>1,'visible'=>true);
			$this->panels['autre'] = array('nbCols'=>1,'visible'=>true);
			$this->panels['conso_annuelle'] = array('nbCols'=>3,'visible'=>false);
			$this->panels['total'] = array("visible"=>true,'nbCols'=>3);
			
			
								
			//$this->foreign_key["conjoint_nom"] = "adherent";
			$this->fieldstructure();
			
			$this->no_select = 
			$this->no_insert = true;			
			
			$this->field_nom = "id_adherent_charge";
	}


	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		unset($infos["adherent_charge"]["total_mensuel"], $infos["adherent_charge"]["total_mensuel_conjoint"], $infos["adherent_charge"]["total_mensuel_impaye"], 
		      $infos["adherent_charge"]["total_annuel"] , $infos["adherent_charge"]["total_conjoint"] , $infos["adherent_charge"]["total_impaye"]);
		parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);		
	}
	
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		unset($infos["adherent_charge"]["total_mensuel"], $infos["adherent_charge"]["total_mensuel_conjoint"], $infos["adherent_charge"]["total_mensuel_impaye"], 
		      $infos["adherent_charge"]["total_annuel"] , $infos["adherent_charge"]["total_conjoint"] , $infos["adherent_charge"]["total_impaye"]);
			
		parent::update($infos,$s);
		ATF::adherent()->redirection("select",ATF::adherent()->cryptId($infos["adherent_charge"]["id_adherent"]));	
	}
	
	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$adherentMensuel = "";
		$conjointMensuel = "";
		$impayeMensuel = "";		
		
		$panel = array("charge", "deplacement", "banque", "autre");
		foreach($panel as $cle=>$v){			
			foreach($this->colonnes['panel'][$v] as $key=>$item){
				foreach($item["fields"] as $k=>$i){
					if(($k != "amende_adherent") &&	($k != "amende_conjoint") &&	($k != "redevance_conjoint")){
						if($k == "amende"){							
								$impayeMensuel = $impayeMensuel."+ IFNULL(".$this->table.".".$k.", 0)";							
						}else{											
							if(!strpos($k, "_conjoint") && (!strpos($k, "_charge"))){												
									if($adherentMensuel){
										$adherentMensuel = $adherentMensuel."+ IFNULL(".$this->table.".".$k.", 0)";
									}else{
										$adherentMensuel = "IFNULL(".$this->table.".".$k .", 0)";
									}
							}elseif(strpos($k, "_conjoint")){							
								if($conjointMensuel){
									$conjointMensuel = $conjointMensuel."+ IFNULL(".$this->table.".".$k.", 0)";
								}else{
									$conjointMensuel = "IFNULL(".$this->table.".".$k.", 0)";
								}
							}else{
								if($impayeMensuel){
									$impayeMensuel = $impayeMensuel."+ IFNULL(".$this->table.".".$k.", 0)";
								}else{
									$impayeMensuel = "IFNULL(".$this->table.".".$k.", 0)";
								}
							}									
						}
					}
				}								
			}
		}
		
		$this->q->addField("SUM(".$adherentMensuel.")","total_mensuel")
				->addField("SUM(".$conjointMensuel.")","total_mensuel_conjoint")
				->addField("SUM(".$impayeMensuel.")","total_impaye")
				->addField("ROUND(SUM(".$adherentMensuel.")*12,2)","total_annuel")
				->addField("ROUND(SUM(".$conjointMensuel.")*12,2)","total_conjoint")
				->addGroup("adherent_charge.id_adherent");					
		$return = parent::select_all($order_by,$asc,$page,$count);		

		if($return[0]){
			foreach ($return as $key => $value) {			
				$return[$key]["num_dossier"] = ATF::adherent()->select($value["adherent_charge.id_adherent_fk"], "num_dossier");
			}	
		}

		return $return;
	}
	
	/** Select_all pour afficher les totaux de charge de l'adherent
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function selectCalcule($id_adherent){		
		$this->q->reset()->where("adherent_charge.id_adherent",ATF::adherent()->decryptId($id_adherent));
		return $this->select_all();
	}
	
	
	public  function getAdherent_charge($id_adherent){		
		$this->q->reset()->where("id_adherent" , ATF::adherent()->decryptId($id_adherent));
		$charge = $this->select_row();		
		return $charge["adherent_charge.id_adherent_charge"];
	}
	
	
	/**
	 * Retourne la valeur par défaut spécifique aux données passées en paramètres
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param string $field
	 * @param array &$s La session
	 * @param array &$request Paramètres disponibles (clés étrangères)
	 * @return string
	 */
	public function default_value($field,$quickMail=false){								
		if(ATF::_r('id')){
			$adherent=ATF::adherent_charge()->select(ATF::_r('id'), "id_adherent");
		}elseif(ATF::_r('id_adherent_charge')){
			$adherent=ATF::adherent_charge()->select(ATF::_r('id_adherent_charge') , "id_adherent");
		}	
				
		if($field == "total_mensuel" || $field == "total_annuel" || $field == "total_mensuel_conjoint" || $field == "total_conjoint" || $field == "total_impaye"){
			$totaux = $this->selectCalcule($adherent);			
		}				
		switch($field){
			case "total_mensuel":
				return $totaux[0]["total_mensuel"];
			case "total_annuel":
				return $totaux[0]["total_annuel"];
			case "total_mensuel_conjoint":
				return $totaux[0]["total_mensuel_conjoint"];
			case "total_conjoint":
				return $totaux[0]["total_conjoint"];			
			case "total_impaye" : 
				return $totaux[0]["total_impaye"];	
			default:
				return parent::default_value($field);		
		}
	}
}
