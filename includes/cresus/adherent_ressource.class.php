<?
class adherent_ressource extends classes_optima {
	function __construct() {
			parent::__construct();
			
			$this->colonnes["fields_column"] = array(
				 "adherent_ressource.id_adherent"	
				,"total_mensuel"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
				,"total_mensuel_conjoint"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
				,"total_annuel"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
				,"total_conjoint"=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money")
			);
			
			$this->colonnes['primary'] = array(
				 "id_adherent"=>array("readonly"=>true, "disabled"=>true)
			);
			
			$this->colonnes['panel']['ressource']= array(		
			   "ressource_salaire"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					"salaire"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"salaire_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)	
				))
				
				,"ressource_pension"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(				
					 "pension"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300, "width"=> 300)
					,"pension_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
				
				,"ressource_indemnite"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					"indemnite"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"indemnite_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
				
				,"ressource_ijss"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					"ijss"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"ijss_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
				
				
				,"ressource_assedic"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					"assedic"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"assedic_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
				
				,"ressource_rmi"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					"rmi"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)					
					,"rmi_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
				
				,"ressource_primes"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					"primes"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"primes_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))	
					
				,"ressource_retraite"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "retraite"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"retraite_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_rsa"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "rsa"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"rsa_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_autre"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "autre"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"autre_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_alloc_fam"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "alloc_fam"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"alloc_fam_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_alloc_log"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "alloc_log"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"alloc_log_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_apl"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "apl"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"apl_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_alloc_parent_iso"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "alloc_parent_iso"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"alloc_parent_iso_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_alloc_adulte_handi"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "alloc_adulte_handi"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"alloc_adulte_handi_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_pension_ali"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "pension_ali"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"pension_ali_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_presta_comp"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					 "presta_comp"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
					,"presta_comp_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)
				))
					
				,"ressource_autre_revenu"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
					"autre_revenu"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)					
					,"autre_revenu_conjoint"=>array("listeners"=>array("change"=>"ATF.updateRessource"), "width"=> 300)	
				))
			);
			
			$this->colonnes['panel']['total'] = array(
				 "total_mensuel"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield", "width"=> 300)
				,"total_mensuel_conjoint"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield", "width"=> 300)
				,"total_annuel" =>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield", "width"=> 300)
				,"total_conjoint" =>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield", "width"=> 300)
			);
			
			$this->colonnes['panel']['droit']= array(
				 "tpn"
				,"fsl"
				,"autre_droit"
			);
			
			$this->panels['primary'] = array('nbCols'=>1,'visible'=>true);			
			$this->panels['ressource'] = array('nbCols'=>1,'visible'=>true);			
			$this->panels['total'] = array("visible"=>true,'nbCols'=>2);
			$this->panels['droit'] = array('nbCols'=>3,'visible'=>false);
			
								
			//$this->foreign_key["conjoint_nom"] = "adherent";
			$this->fieldstructure();
			
			

			$this->no_select = 
			$this->no_insert = true;
			
			$this->onglets = array(
				"adherent_charge"=>array('opened'=>true)
				,"credit"=>array('opened'=>true)
				,"emprunt"=>array('opened'=>true)
				,"impaye"=>array('opened'=>true)			
			);

			$this->field_nom = "id_adherent";
			
	}

	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		unset($infos["adherent_ressource"]["total_mensuel"], $infos["adherent_ressource"]["total_annuel"],
			  $infos["adherent_ressource"]["total_mensuel_conjoint"],$infos["adherent_ressource"]["total_conjoint"]);	
			
		parent::update($infos,$s);
		ATF::adherent()->redirection("select",ATF::adherent()->cryptId($infos["adherent_ressource"]["id_adherent"]));	
	}
	
	
	
	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$adherentMensuel = "";
		$conjointMensuel = "";
		
		foreach($this->colonnes['panel']['ressource'] as $key=>$item){
			foreach($item["fields"] as $k=>$i){					
				if(!strpos($k, "_conjoint")){	
					if($adherentMensuel !== ""){
						$adherentMensuel = $adherentMensuel."+ IFNULL(".$this->table.".".$k." ,0)";
					}else{
						$adherentMensuel = "IFNULL(".$this->table.".".$k." ,0)";
					}
				}else{
					if($conjointMensuel !== ""){
						$conjointMensuel = $conjointMensuel."+ IFNULL(".$this->table.".".$k." ,0)";
					}else{
						$conjointMensuel = "IFNULL(".$this->table.".".$k." ,0)";
					}
				}									
			}								
		}
		
		$this->q->addField("SUM(".$adherentMensuel.")","total_mensuel")
				->addField("SUM(".$conjointMensuel.")","total_mensuel_conjoint")
				->addField("ROUND(SUM(".$adherentMensuel.")*12,2)","total_annuel")
				->addField("ROUND(SUM(".$conjointMensuel.")*12,2)","total_conjoint")				
				->addGroup("adherent_ressource.id_adherent_ressource");
					
		$return =  parent::select_all($order_by,$asc,$page,$count);	
		
		if($return[0]){
			foreach ($return as $key => $value) {			
				$return[$key]["num_dossier"] = ATF::adherent()->select($value["adherent_ressource.id_adherent_fk"], "num_dossier");
			}	
		}
		
		return $return;	
	}	
	
	/** Select_all pour afficher les totaux de ressources de l'adherent
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function selectCalcule($id_adherent){		
		$this->q->reset()->where("adherent_ressource.id_adherent",ATF::adherent()->decryptId($id_adherent));
		return $this->select_all();
	}

	public  function getAdherent_ressource($id_adherent){		
		$this->q->reset()->where("id_adherent" , ATF::adherent()->decryptId($id_adherent));
		$ressource = $this->select_row();		
		return $ressource["adherent_ressource.id_adherent_ressource"];
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
			$adherent=ATF::adherent_ressource()->select(ATF::_r('id'), "id_adherent");
		}elseif(ATF::_r('id_adherent_ressource')){
			$adherent=ATF::adherent_ressource()->select(ATF::_r('id_adherent_ressource') , "id_adherent");
		}	
				
		if($field == "total_mensuel" || $field == "total_annuel" || $field == "total_mensuel_conjoint" || $field == "total_conjoint"){
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
			default:
				return parent::default_value($field);		
		}
	}

}
