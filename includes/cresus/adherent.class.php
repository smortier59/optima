<?
class adherent extends classes_optima {
		function __construct() {
			parent::__construct();
			$this->colonnes["fields_column"] = array(	
				 "num_dossier"
				,"num_dossier_old"
				, 'adherent.nom'
				,'adherent.prenom'
				,'adherent.id_zonegeo'

			);	
			
			
			$this->colonnes['primary'] = array(
				"num_dossier"=>array('null'=>true,"custom"=>true,"readonly"=>true,"xtype"=>"textfield", "width"=>50)
				,"num_dossier_old"
				,"date_entree"
				,"id_site_accueil"
				,"id_pole_accueil"
				,"id_orientation"
				,"date_cloture"
				,"archive"
				,"raison_archive"
				,"age"=>array('null'=>true,"custom"=>true,"readonly"=>true,"xtype"=>"textfield", "width"=>50)
			);	
						
			$this->colonnes['panel']["identite"] = array(				
				 "adherent"=>array("custom"=>true,'null'=>false,'xtype'=>'compositefield','fields'=>array(					
					"civilite"=>array("width"=>50, "listeners"=>array("change"=>"ATF.civilite"))
					,"prenom"
					,"nom"
				))
				,"nom_jeune_fille"
				,"sexe_adherent"=>array("custom"=>true,'null'=>false,'xtype'=>'compositefield','fields'=>array(
					"sexe"=>array("width"=>50)
				))
				,"naissance"=>array("custom"=>true,'null'=>false,'xtype'=>'compositefield','fields'=>array(
					"date_naissance"=>array("listeners"=>array("change"=>"ATF.getAge"))
					,"ville_naissance"
					,"pays_naissance"
				))
				,"nationalite_adherent"=>array("custom"=>true,'null'=>false,'xtype'=>'compositefield','fields'=>array(	
					"nationalite"
					,"nationalite2"
				))
				,"age_adherent"=>array("custom"=>true,'false'=>true,'xtype'=>'compositefield','fields'=>array(
					"tranche_age"=>array("width"=>110)	
				))
				,"tel"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 'fixe' => array("tel"=>true,"renderer"=>"tel","width"=>120)
					,'mobile' => array("tel"=>true,"renderer"=>"tel","width"=>120)
				))
				,"personne_a_charge"=>array('null'=>false)
			);
			$this->colonnes['panel']["famille"] = array(
				  "situation_familiale"=>array('null'=>false)
				 ,"habitation"=>array('null'=>false)
				 ,"adresse_perso"=>array('null'=>false)
				 ,"zone_geo"=>array('null'=>false)	
				 ,"surface_habitable"				 				
				 ,"adresse_perso_2"	
				 ,"cp_ville"=>array("custom"=>true,'null'=>false,'xtype'=>'compositefield','fields'=>array(
						"cp"=>array("width"=>80)
						,"id_zonegeo"
				  ))			 	
				 ,"mail"
				 ,"securite_sociale"
				 ,"caf"				 
				 ,"compo_assurance"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
						"assurance"=>array("width"=>50)
						,"nom_assurance"
				 ))
				 ,"compo_mutuelle"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
						"mutuelle"=>array("width"=>50)
						,"nom_mutuelle"
				  ))
				  ,"cmu"=>array("custom"=>true,"xtype"=>"combo","listeners"=>array("change"=>"ATF.changeCmu"))
				  ,"tpn"=>array("hidden"=>true, "disabled"=>false)		 
			);
			
			$this->colonnes['panel']['lignes'] = array(
				"enfants"=>array("custom"=>true)
			);
			
			$this->colonnes['panel']["pro"] = array(
				 "profession"=>array('null'=>false)
				,"csp"=>array('null'=>false)
				,"demandeur_emploi"=>array('null'=>false)
				,"qualif_pro"
				,"niveau"
				,"employeur"
				,"tel_employeur"
				,"fax"
				,"adresse_employeur"
				,"cp_ville_emp"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
						"cp_employeur"=>array("width"=>80)
						,"ville_employeur"
				  ))
				,"info_ce"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
						"ce"=>array("width"=>50)
						,"adresse_ce"
				  ))	
					
				
			);
			$this->colonnes['panel']["conjoint"] = array(
				"nom_complet"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "nom_conjoint"
					,"prenom_conjoint"
				))
				,"nom_jf_conjoint"
				,"sexe_conjoint"
				,"naissance2"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					"date_naiss_conjoint"
					,"ville_naissance_conjoint"
					,"pays_naissance_conjoint"
				))
				,"nationalite_conjoint"
				,"secu_conjoint"
				,"profession_conjoint"
				,"employeur_conjoint"
				,"adresse_employeur_conjoint"
				,"tel_employeur_conjoint"
				,"fax_employeur_conjoint"
				,"info_ce2"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					"ce_conjoint"=>array("width"=>50)
					,"adresse_ce_conjoint"
			  	))
			);
			
			$this->colonnes['panel']["com"] = array(
				"commentaire"
			);
			
			$this->colonnes['panel']["evaluation"] = array(
				 "id_padd"
				,"id_precarite_habitat"
				,"id_impaye_energie"
			);
			
			$this->colonnes['panel']["autre"] = array(
				 "procedure_en_cours"
				,"coupure_edf_eau"
				,"choix_coupure"
				,"saisie_cpt_banc"
				,"abis_vente_huissier"
				,"ficp"
			);
		
			$this->panels['primary'] = array('nbCols'=>4,'visible'=>true);
			$this->panels['identite'] = array('nbCols'=>2,'visible'=>true);
			$this->panels['famille'] = array('nbCols'=>2,'visible'=>true);
			$this->panels['lignes'] = array('nbCols'=>1,'visible'=>false);
			$this->panels['pro'] = array('nbCols'=>3,'visible'=>true);
			$this->panels['conjoint'] = array('nbCols'=>2,'visible'=>false);
			$this->panels['com'] = array('nbCols'=>1,'visible'=>false);
			$this->panels['evaluation'] = array('nbCols'=>3,'visible'=>false);
			$this->panels['autre'] = array('nbCols'=>3,'visible'=>false);
			
			$this->autocomplete = array(
				 "view"=>array("adherent.prenom","adherent.nom","adherent.fixe","adherent.mobile")
				,"field"=>array("adherent.civilite","adherent.prenom","adherent.nom","adherent.num_dossier","adherent.fixe","adherent.mobile")
				,"show"=>array("adherent.civilite","adherent.prenom","adherent.nom","adherent.num_dossier","adherent.fixe","adherent.mobile")
				,"popup"=>array("adherent.nom","adherent.prenom","adherent.num_dossier","adherent.fixe","adherent.mobile")
			);						
			$this->fieldstructure();
			$this->onglets = array(
				"rdv"=>array('opened'=>true)				
				,"adherent_cotisation"=>array('opened'=>true)
				,"emprunt"=>array('opened'=>true)
				,"credit"=>array('opened'=>true)
				,"impaye"=>array('opened'=>true)
				,"adherent_enfant"=>array('opened'=>false)				
			);
			
			$this->colonnes['bloquees']['insert'] =  array('date_cloture','archive',"zone_geo","raison_archive", "num_dossier","num_dossier_old");			
			$this->colonnes['bloquees']['update'] =  array("id_pole_accueil", "zone_geo","num_dossier_old");
			$this->colonnes['bloquees']['select'] =  array("zone_geo","enfants");
									
			$this->field_nom = "%civilite% %prenom% %nom%";
		}
		
		
		public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){			
			$infos_ligne = json_decode($infos["values_".$this->table]["enfants"],true);
			$this->infoCollapse($infos);	
			
			// ************************* TESTS ****************************************
			
			$infosObli = array("civilite","prenom","nom","sexe","date_naissance","ville_naissance","pays_naissance","nationalite","tranche_age",
				  			   "situation_familiale","habitation","adresse_perso","cp","id_zonegeo", 
				  			    "profession","csp","demandeur_emploi" 
							   );
			unset($infos["age"]);
			foreach ($infosObli as $key => $value) {
				if(!$infos[$value]){
					throw new error("Le champs ".ATF::$usr->trans($value)." n'est pas renseigné !");
				}
			}
			// ************************* FIN TESTS ****************************************			
			
			// ************************* Récupération du Num de dossier AnnéeMois-Num-Pole ******************
			if(!$infos["id_adherent"]){
				$pole = ATF::pole_accueil()->select($infos["id_pole_accueil"], "lettre_dossier");
			
				$this->q->reset()->addField("MAX(num_dossier)", "max")
								 ->where("num_dossier",date(Y)."%-%-".$pole["lettre_dossier"], "AND", false, "LIKE");
				$res = $this->select_row();
				
				if($res["max"]){
					$num = explode("-", $res["max"]);
					$num = $num[1]+1;				
					$infos["num_dossier"] = date("Ym")."-".str_pad($num, 4, "0", STR_PAD_LEFT)."-".$pole["lettre_dossier"];				
				}else{$infos["num_dossier"] = date("Ym")."-0001-".$pole["lettre_dossier"];}
			}
			
			// ************************* Fin Récupération du Num de dossier ******************
			
			$infos["nom"] = strtoupper($infos["nom"]);
			$infos["nom_conjoint"] = strtoupper($infos["nom_conjoint"]);
			$infos["prenom"] = ucfirst(strtolower($infos["prenom"])); 
			$infos["prenom_conjoint"] =  ucfirst(strtolower($infos["prenom_conjoint"])); 
			
			ATF::db($this->db)->begin_transaction();
			
			if($infos["id_adherent"]){
				parent::update($infos,$s,NULL,$var=NULL,NULL,true);
				$id_adherent = $infos["id_adherent"];
			}else{
				$id_adherent = parent::insert($infos,$s,NULL,$var=NULL,NULL,true);
				ATF::adherent_ressource()->insert(array("id_adherent"=>$id_adherent));
				ATF::adherent_charge()->insert(array("id_adherent"=>$id_adherent));
			}					
			//*****************************Transaction********************************
			//On supprime les enfants pour l'update
			if($infos["id_adherent"]){
				ATF::adherent_enfant()->q->reset()->where("id_adherent" , ATF::adherent()->decryptId($infos["id_adherent"]));														  
				$enfants = ATF::adherent_enfant()->select_all();
				if($enfants){
					foreach ($enfants as $k => $v) {								
						ATF::adherent_enfant()->d($v["id_adherent_enfant"]);
					}
				}
			}
			foreach($infos_ligne as $key=>$item){				
				foreach($item as $k=>$i){					
					$k_unescape=util::extJSUnescapeDot($k);					
					$item[str_replace("adherent_enfant.","",$k_unescape)]=$i;
					unset($item[$k]);					
				}		
				
				if($item["prenom"] && $item["date_naissance"]){					
					$item["prenom"] = ucfirst($item["prenom"]);
					if(!$item["nom"]){ $item["nom"] = $infos["nom"];}
					$item["nom"] = strtoupper($item["nom"]);
					$item["date_naissance"] = substr($item["date_naissance"], 0 , 10);
					$item["note"] = $item["note"];								
					$item["id_adherent"] = $id_adherent;					
					ATF::adherent_enfant()->insert($item , $s);	
				}elseif(!$item["prenom"] || !$item["date_naissance"] ){
					ATF::db($this->db)->rollback_transaction();	
					throw new error("Les enfants doivent avoir un prénom et une date de naissance !");
				}				
			}
			//************************  FIN  *********************************************			
			
			ATF::db($this->db)->commit_transaction();
			
			ATF::adherent()->redirection("select",$id_adherent);	
								
		}
		
		public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){						
			return $this->insert($infos,$s);
		}

		public function last(){
			$this->q->reset()->addOrder("id_adherent", "desc")->setLimit(1);
			$res = $this->select_row();
			return $res["id_adherent"];
		}
	
		/**
		 * Retourne la valeur par défaut spécifique aux données passées en paramètres
		 * @author Yann GAUTHERON <ygautheron@absystech.fr>
		 * @param string $field
		 * @param array &$s La session
		 * @param array &$request Paramètres disponibles (clés étrangères)
		 * @return string
		 */
		public function default_value($field,$quickMail=false){				
			if(ATF::_r('id')){
				$adherent=ATF::adherent()->select(ATF::_r('id'));
			}elseif(ATF::_r('id_adherent')){
				$adherent=ATF::adherent()->select(ATF::_r('id_adherent'));
			}	
			
							
			switch($field){
			case "age" :
						$dateNaissance = explode("-",$adherent["date_naissance"]);
						$date = date("Y") - $dateNaissance[0];	
						return $date." ans";
			default:
				return parent::default_value($field);
			
			}
		}


		public function getRatio($id_adherent){
			ATF::adherent_charge()->q->reset()->where("id_adherent" , $id_adherent)
									 ->addField("electricite")
									 ->addField("gaz")
									 ->addField("eau")
									 ->addField("electricite_conjoint")
									 ->addField("gaz_conjoint")
									 ->addField("eau_conjoint");
			$charges = ATF::adherent_charge()->select_row();

			ATF::adherent_ressource()->q->reset()->where("id_adherent" , $id_adherent);
			$ressources = ATF::adherent_ressource()->select_row();
			
			$charges = $charges["electricite"] + $charges["gaz"] + $charges["eau"] + $charges["electricite_conjoint"] + $charges["gaz_conjoint"] + $charges["eau_conjoint"];	
			$ressources = $ressources["total_mensuel"] + $ressources["total_mensuel_conjoint"];
			
			return number_format($charges/$ressources , 3);
		}
}