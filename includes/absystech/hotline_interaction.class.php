<?
/**  
* Classe Hotline
* @package Optima
* @subpackage AbsysTech
*/
class hotline_interaction extends classes_optima {
	/**
	* Contructeur par défaut !
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		//Colonnes SELECT ALL
		$this->colonnes['fields_column'] =array(
			'hotline_interaction.date'=>array("width"=>100,"align"=>"center")
			//,'hotline_interaction.temps'=>array("width"=>100,"align"=>"center")
			//,'hotline_interaction.temps_passe'=>array("width"=>100,"align"=>"center")
			,"hotline_interaction.credit_presta"=>array("align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","width"=>80)
			,"hotline_interaction.credit_dep"=>array("align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","width"=>80)
			,"duree_presta"=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","renderer"=>"temps","width"=>120)
			,"duree_pause"=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","renderer"=>"temps","width"=>120)
			,"duree_dep"=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","renderer"=>"temps","width"=>120)			
			,'hotline_interaction.detail'=>array("truncate"=>false)
			,'hotline_interaction.id_user'=>array("width"=>150)
			,'hotline_interaction.id_ordre_de_mission'=>array("width"=>150)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","width"=>50,"align"=>"center")
		);
		
        $this->colonnes['primary'] = array(
            "detail"=>array("editor"=>"simpleEditor")
        );
        $this->panels['primary'] = array('nbCols'=>1);
		

        $this->colonnes['panel']['horaires_fs'] = array(
        	 "heure_depart_dep"=>array("listeners"=>array("change"=>"ATF.changeHeureDep","select"=>"ATF.selectHeureDep"))
        	,"heure_debut_presta"=>array("listeners"=>array("change"=>"ATF.changeHeurePresta","select"=>"ATF.selectHeurePresta"))        	
        	,"duree_pause"=>array("listeners"=>array("change"=>"ATF.changeDureePause","select"=>"ATF.selectDureePause"), "min"=>"00:00")
        	,"heure_fin_presta"=>array("listeners"=>array("change"=>"ATF.changeHeurePresta","select"=>"ATF.selectHeurePresta"))
        	,"heure_arrive_dep"=>array("listeners"=>array("change"=>"ATF.changeHeureDep","select"=>"ATF.selectHeureDep"))
        );
        $this->panels['horaires_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>false,'visible'=>true);


        $this->colonnes['panel']['duree_credit_fs'] = array(
        	 "duree_presta"=>array("listeners"=>array("change"=>"ATF.changeDureePresta","select"=>"ATF.selectDureePresta"), "min"=>"00:00")
        	,"credit_presta"=>array("listeners"=>array("change"=>"ATF.changeCreditPresta"))
        	,"duree_dep"=>array("disabled"=>true, "min"=>"00:00")        	
        	,"credit_dep"=>array("listeners"=>array("change"=>"ATF.changeCreditDep"))      
        	,"matos"=>array("xtype"=>"switchbutton","default"=>"non")  
        	,"champ_alerte"=>array("custom"=>true, "xtype"=>"textarea","targetCols"=>1,"hidden"=>true) 	
        );
        $this->panels['duree_credit_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>false,'visible'=>true);

        // Sous panel de temps
        $this->colonnes['panel']['temps_fs'] = array(
        	"nature"=>array("data"=>array("internal", 'relation_client', 'interaction'),"xtype"=>"combo", "listeners"=>array("select"=>"ATF.selectNature"))
        	,"date"
        	,"deplacements"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'horaires_fs')
        	,"prestations"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'duree_credit_fs')
        );
        $this->panels['temps_fs'] = array('nbCols'=>2,'isSubPanel'=>true,'collapsible'=>false,'visible'=>true);
        
                               

        // Sous panel de paramétrage
        $this->colonnes['panel']['options_fs'] = array(
            "etat_wait"=>array("xtype"=>"switchbutton","default"=>"non")
            ,"send_mail"=>array("xtype"=>"switchbutton","null"=>true)
            ,"mep_mail"=>array("xtype"=>"switchbutton","default"=>"non")
            ,"visible"=>array("xtype"=>"switchbutton")            
            ,"id_contact"
            ,"id_user"=>array("custom"=>true)
            ,"id_ordre_de_mission"
            ,"sp_transfert"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'transfert_fs')    
            ,"notifications"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'notifications_fs')
        );
        $this->panels['options_fs'] = array('nbCols'=>2,'isSubPanel'=>true,'collapsible'=>false,'visible'=>true);
        
        // Sous panel de paramétrage
        $this->colonnes['panel']['notifications_fs'] = array(
            "anotherNotify"=>array("custom"=>true)  
            ,"actifNotify"=>array("custom"=>true)   
        );
        $this->panels['notifications_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);
        
        // Sous panel de paramétrage
        $this->colonnes['panel']['transfert_fs'] = array(
            "transfert"=>array("custom"=>true)
            ,"transfert_pole"=>array("custom"=>true)           
        );
        $this->panels['transfert_fs'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);
        
        // Panel qui contient tous les sous panel
        $this->colonnes['panel']['parametre'] = array(
            "sp_temps"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'temps_fs'),
            "options"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'options_fs')
        );
        $this->panels['parametre'] = array("visible"=>true);
        
        //Panel intéraction du ticket
        /*$this->colonnes['panel']['lignes_interactions'] = array(
            "interactions"=>array("custom"=>true)
            ,"rappel-hotline"=>array("custom"=>true,"xtype"=>"displayfield","readonly"=>true)
            ,"rappelDetail-hotline"=>array("custom"=>true,"xtype"=>"displayfield","readonly"=>true)
        );
        $this->panels['lignes_interactions'] = array('nbCols'=>1);*/
        
        $this->fieldstructure();    


		$this->color["etat"]["done"] = "#AAAAAA";
		$this->colonnes['bloquees']['insert'] = array("etat","id_hotline","id_ordre_de_mission","temps_passe","temps");
		$this->colonnes['bloquees']['update'] = array("etat","id_hotline","transfert","transfert_pole","etat_wait","send_mail","avancement","anotherNotify","actifNotify","temps_passe","temps");
		$this->colonnes['bloquees']['select'] = array("transfert","transfert_pole","etat_wait","send_mail","avancement","temps_passe","temps");
		$this->field_nom = "%hotline_interaction.detail%";
		//$this->no_update = true;
		$this->pole_concerne=array('dev'=>1,'system'=>0,'telecom'=>0);
		$this->facturation_ticket=array('oui'=>1,'non'=>1);
		$this->stats_filtre=array('pole_concerne','facturation_ticket','etat');
		$this->liste_user=$this->getUserActif();
		$this->etat=$this->listingEtat();
		$this->files["fichier_joint"] = array("multiUpload"=>true);
		
		// Quick_action
		$this->quick_action['select'] = array('update','delete','print');
		$this->addPrivilege("changeUser");
		$this->addPrivilege("export_marge_hotline");
		
	}
	
	/**
	* Mise à jour spécifique
	*/
	public function delete($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$mail=true,$pointage=true){
		
		foreach ($infos['id'] as $id) {
			$id_ODM = $this->select($id,"id_ordre_de_mission");
			if ($id_ODM) {
				$odm = array("id_ordre_de_mission"=>$id_ODM,"etat"=>"en_cours");	
				ATF::ordre_de_mission()->u($odm);
				ATF::hotline()->createNotice("odm_retour_a_en_cours");
			}
		}
		
		parent::delete($infos,$s,$files,$cadre_refreshed,$mail,$pointage);

		api::sendUDP(array("data"=>array("type"=>"interaction")));
	}
	
	/**
	* Mise à jour spécifique
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$mail=true,$pointage=true){
		$this->infoCollapse($infos);
		$infos["update"]=true;
		$this->insert($infos,$s,$files,$cadre_refreshed,$mail,$pointage);
	}
	
	/**
	* INSERTION d'une interaction de hotline
    * @author Jérémie GWIAZDOWSKI <jgwiazdowski@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos Les informations de l'interaction
	* @param array $s La session
	* @param array $files Les fichiers uploadés
	* @param boolean $mail True si on désire
	* @param boolean $pointage True si on désire créer le pointage
    * @return boolean true 
    */   
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$mail=true,$pointage=true) {
       	
		$this->infoCollapse($infos);

		/*---------------Fonctionnalité de trace hotline----------------------*/
		if($infos["internal"]){
			$data = array(
				 "duree_presta"=>$infos["duree_presta"]?$infos["duree_presta"]:"00:00"
				,"heure_debut_presta"=>date("h:i")
				,"heure_fin_presta"=>date("h:i")
				,"detail"=>$infos["detail"]
				,"id_user"=>(($infos["id_user"])?$infos["id_user"]:ATF::$usr->getID())
				,"id_hotline"=>$infos["id_hotline"]
				,"visible"=>$infos["visible"]
				,"nature"=>"internal"
			);
			return parent::insert($data,$s,$files);
		}

		$no_test_credit = true;
		if(!isset($infos["no_test_credit"])){
			$no_test_credit = false;
		}
		unset($infos["no_test_credit"]);
		
		// Début de transaction
		ATF::db($this->db)->begin_transaction();
				
		/*---------------Vérification des informations passées----------------------*/
		if(!$infos){
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF(ATF::$usr->trans("aucunes_infos",$this->table));
		}

		if(!$infos["heure_depart_dep"])  $infos["heure_depart_dep"] = $infos["heure_debut_presta"];
		if(!$infos["heure_arrive_dep"])  $infos["heure_arrive_dep"] = $infos["heure_fin_presta"];

		$spdebutpresta = explode(":", $infos["heure_debut_presta"]);
		$spfinpresta = explode(":" , $infos["heure_fin_presta"]);
		$debut_presta = intval($spdebutpresta[0])*60 + intval($spdebutpresta[1]);
		$fin_presta = intval($spfinpresta[0])*60 + intval($spfinpresta[1]);
		
		$spdebut_dep = explode(":", $infos["heure_depart_dep"]);
		$spfin_dep = explode(":" , $infos["heure_arrive_dep"]);
		$debut_dep = intval($spdebut_dep[0])*60 + intval($spdebut_dep[1]);
		$fin_dep = intval($spfin_dep[0])*60 + intval($spfin_dep[1]);

		if($debut_dep > $debut_presta){
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF(ATF::$usr->trans("L'heure de début de mission est superieure à l'heure de début de prestation !",$this->table));
		}

		if($fin_presta > $fin_dep){
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF(ATF::$usr->trans("L'heure de fin de mission est inferieure à l'heure de fin de prestation !",$this->table));
		}


		//Test de présence d'un texte
		if (!$infos["detail"]){
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF(ATF::$usr->trans("joindre_un_texte_explicatif_a_l_interaction",$this->table));
		} 
		
		$hotline = ATF::hotline()->select($infos["id_hotline"]);
		/*---------------Gestion du temps----------------------*/
		if((!$infos["duree_presta"] || $infos["duree_presta"] =="00:00" || $infos["duree_presta"] =="0:00") && !$no_test_credit){
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF(ATF::$usr->trans("duree_presta_non_renseigne",$this->table));
		} 	
		

		$duree_pause = 0;

		if($infos["duree_pause"]){
			$spdureepause = explode(":", $infos["duree_pause"]);
			$duree_pause = intval($spdureepause[0])*60 + intval($spdureepause[1]);

			$duree_presta = $fin_presta - $debut_presta;
			if($duree_presta < $duree_pause){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF(ATF::$usr->trans("La durée de pause est superieure à la durée de prestation !",$this->table));
			}

		}

		
	
		$duree_presta = $fin_presta - $debut_presta - $duree_pause;
		$p = array('tps'=>$duree_presta);
		$ticket_presta = $this->_credit($p);

		
		if($duree_presta < 0){			
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("L'heure du début de la prestation est supérieure à l'heure de fin !");
		} 

		$ticket_presta = number_format(($duree_presta)/60 , 2);

		$id_societe = ATF::hotline()->select($infos["id_hotline"], "id_societe");
		

		$duree_dep = 0;
		if($infos["duree_dep"] && ($infos["duree_dep"] !=="00:00" && $infos["duree_dep"] !=="0:00" && $infos["duree_dep"] !=="00:00:00")){			

			/*if($fin_dep - $debut_dep < 0){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("[DEPLACEMENT] L'heure du départ déplacement est supérieure à l'heure d'arrivée !");
			}*/

			$duree_dep =  explode(":", $infos["duree_dep"]);
			$duree_dep = 60*intval($duree_dep[0]) + intval($duree_dep[1]);


			// if(ATF::societe()->select($id_societe, "forfait_dep") == 0.00){		
			// 	$ticket_dep =  explode(":", $infos["duree_dep"]);
			// 	$ticket_dep = number_format((60*intval($ticket_dep[0]) + intval($ticket_dep[1]))/60 , 2);
			// }else{
			// 	$ticket_dep = ATF::societe()->select($id_societe, "forfait_dep");
			// }
		}

		$p = array('tps'=>$duree_dep,"field"=>"credit_dep");
		$ticket_dep = $this->_credit($p);

		
		$infos["duree_presta"]=$infos["duree_presta"].":00";
		$infos["duree_dep"]=$infos["duree_dep"].":00";

		$temps = $duree_presta + $duree_dep;
		$temps = gmdate("H:i:s", $temps*60);		


	
		/*---------------Gestion de l'ordre de mission----------------------*/
		if($infos["id_ordre_de_mission"]) ATF::ordre_de_mission()->update(array("id_ordre_de_mission"=>$infos["id_ordre_de_mission"],"etat"=>"termine"));
				
		/*---------------Gestion de l'état de la requête----------------------*/
		// 1ère contrainte : Changement de l'état suite à la mise en attente du client ($infos["etat_wait"]=true)
		// 2ème contrainte : Requête en état wait/fixing uniquement (les requêtes terminées ou non prises en charge ne peuvent pas avoir de changement d'état)
		// 3ème contrainte : Les requêtes en état wait passent en état fixing lors d'une interaction
		// 4ème contrainte : Seulement si la facturation est validée ou qu'il n'y a pas de facturation
		if($hotline["etat"]=="fixing" || $hotline["etat"]=="wait"){
			if($infos["etat_wait"]=="oui" || ($hotline["facturation_ticket"]=="oui" && !$hotline["ok_facturation"]) || ($hotline["facturation_ticket"]=="oui" && $hotline["ok_facturation"]=="non")){
				ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"etat"=>"wait","disabledInternalInteraction"=>true));
			}else{
				ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"etat"=>"fixing","disabledInternalInteraction"=>true));
			}
			/*---------------Gestion de la mise en attente de MEP one shot----------------------*/
			if ($infos['mep_mail']=="oui") {
				ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"wait_mep"=>"oui","etat"=>"fixing","disabledInternalInteraction"=>true));
			}
		}
		
		
		/*---------------Cas de le requête non encore validée----------------------*/
		// Lorsque la requête n'est pas encore validée il ne faut pas passer celle-ci en fixing !
		if($hotline["facturation_ticket"]=="oui" && !$hotline["ok_facturation"] || $hotline["facturation_ticket"]=="oui" && $hotline["ok_facturation"]=="non"){
			ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"etat"=>"wait","disabledInternalInteraction"=>true));
		}	
		
		/*---------------Insertion/Maj de l'interaction----------------------*/
		$hotline = ATF::hotline()->select($infos["id_hotline"]);
		//Patch pour bloquer les interactions en mode non-visible
		if($hotline["visible"]=="non")  $infos["visible"]="non";
		$id_hotline_interaction=NULL;
		$data = array(
			// "temps_passe"=>$infos["temps_passe"]
			//,"temps"=>$infos["temps"]
			 "heure_debut_presta"=>$infos["heure_debut_presta"]
			,"heure_fin_presta"=>$infos["heure_fin_presta"]
			,"duree_presta"=>$infos["duree_presta"]
			,"duree_pause"=>$infos["duree_pause"]
			,"heure_depart_dep"=>$infos["heure_depart_dep"]
			,"heure_arrive_dep"=>$infos["heure_arrive_dep"]
			,"duree_dep"=>$infos["duree_dep"]
			,"credit_dep"=>$infos["credit_dep"]
			,"credit_presta"=>$infos["credit_presta"]
			,"date"=>$infos["date"]
			,"detail"=>$infos["detail"]
			,"id_user"=>(($infos["id_user"])?$infos["id_user"]:ATF::$usr->getID())
			,"id_contact"=>$infos["id_contact"]
            ,"id_hotline"=>$infos["id_hotline"]
            ,"visible"=>$infos["visible"]
            ,"nature"=>$infos["nature"]
			,"id_ordre_de_mission"=>((isset($infos["id_ordre_de_mission"]))?$infos["id_ordre_de_mission"]:NULL)
		);


		// Pas de file si pas de files !
		if($infos["filestoattach"]) $data["filestoattach"]=$infos["filestoattach"];
		
		//Petit message pour le transfert
		if($infos["transfert"]){
			$data["detail"]="Requête transférée par ".ATF::user()->nom($infos["id_user"])." à ".ATF::user()->nom($infos["transfert"])."<br />".$data["detail"];
		}elseif($infos['transfert_pole']){
			$data["detail"]="Requête transférée par ".ATF::user()->nom($infos["id_user"])." au pôle ".$infos["transfert_pole"]."<br />".$data["detail"];
		}
		
		//Gestion de la mise à jour (update)
		if($infos["update"]){
			$id_hotline_interaction=$this->decryptId($infos["id_hotline_interaction"]);
			$data["id_hotline_interaction"]=$id_hotline_interaction;
			$hotline["id_hotline"]
				=$infos["id_hotline"]
				=$data["id_hotline"]
				=$this->select($data["id_hotline_interaction"],"id_hotline");
			parent::update($data,$s,$files);
		}else{		    
			$id_hotline_interaction = parent::insert($data,$s,$files);
		}

		// GESTION DES ALERTES
		/*if(!$no_test_credit){
			if( ($ticket_presta > $infos["credit_presta"]) || ($ticket_dep > $infos["credit_dep"])){
				if(!$infos["champ_alerte"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Merci de saisir votre justification !",1020);
				} 
				$alerte = array(
					"alerte"=>$infos["champ_alerte"]
					,"id_user"=>ATF::$usr->getID()
					,"id_hotline"=>$infos["id_hotline"]
					,"id_hotline_interaction"=>$id_hotline_interaction
				);
				if ($infos['id_champ_alerte']) {
					$alerte['id_alerte'] = $infos['id_champ_alerte'];
					ATF::alerte()->u($alerte);
				} else {
					ATF::alerte()->i($alerte);					
				}

			}
		}*/


		//L'interaction fait mention de materiel a facturer
		//On notifie Emma
		$id_user_matos = 54;
		if(ATF::$codename == "att") $id_user_matos = 34;
		if($infos["matos"] == "oui") ATF::alerte()->insert(array("id_hotline"=>$infos["id_hotline"],"id_hotline_interaction"=>$id_hotline_interaction,"alerte"=>$infos["detail"],  "id_user"=>$id_user_matos, "nature"=>"materiel"));

		$interaction=$this->select($id_hotline_interaction);
		
		/*---------------Gestion de la feuille de pointage----------------------*/
		//Si on ne trouve rien c'est que la ligne de pointage n'est pas créée, exemple :
		//On créer une interaction sur un tickets qui n'est pas a notre charge.
		//On créer une interaction sur un ticket qu'on a pris en charge côté nebula.
		if($pointage){
			ATF::pointage()->addPointage($infos["id_hotline"],$id_hotline_interaction,$infos["id_user"],$infos["date"],$temps);
		}
					
		/*---------------Transfert à l'utilisateur----------------------*/
		if ($infos["transfert"]) {
			//Récupération du pôle de l'utilisateur
			$pole=explode(",",ATF::user()->select($infos["transfert"],"pole"));
			ATF::hotline()->update(
				array(
					"id_hotline"=>$infos["id_hotline"]
					,"id_user"=>$infos["transfert"]
					,"pole_concerne"=>((is_array($pole) && isset($pole[0]) && !empty($pole[0]))?$pole[0]:"dev")
					,"disabledInternalInteraction"=>true)
			);
			//Mise à jour de l'état
			ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"etat"=>"fixing","disabledInternalInteraction"=>true));
			//Récupération de l'email du nouvel utilisateur en charge
			$email=ATF::user()->select($infos["transfert"],"email");
			
			ATF::hotline_mail()->createMailUserTransfert($hotline["id_hotline"],$id_hotline_interaction,$email);
			ATF::hotline_mail()->sendMail();
			
			//Notice
			ATF::hotline()->createMailNotice("hotline_transfert_user");
		}
		
		/*---------------Transfert de Pôle----------------------*/
		if($infos['transfert_pole']){
			ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"pole_concerne"=>$infos["transfert_pole"],"disabledInternalInteraction"=>true,"id_user"=>$infos["transfert"]?$infos["transfert"]:NULL));
			//Récupération de l'email du nouveau utilisateur en charge
			$email="hotline.".$infos["transfert_pole"]."@absystech.fr";
		
			ATF::hotline_mail()->createMailPoleTransfert($hotline["id_hotline"],$id_hotline_interaction,$email);
			ATF::hotline_mail()->sendMail();
			
			//Notice
			ATF::hotline()->createMailNotice("hotline_transfert_pole");
		}
		
		/*---------------Gestion de l'envoi de mail----------------------*/
		//Mail au client
		if($mail && $infos["send_mail"]=="oui" && $interaction["visible"]=="oui"){
			
			try{
				ATF::hotline_mail()->createMailInteraction($hotline["id_hotline"],$id_hotline_interaction,$infos["filestoattach"]["fichier_joint"],$infos["anotherNotify"],$infos['mep_mail']);	
			
				if($infos["filestoattach"]["fichier_joint"]){
					//Ajout du fichier joint
					$path = $this->filepath($id_hotline_interaction,"fichier_joint");	
					$mail=ATF::hotline_mail()->getCurrentMail();
					$mail->addFile($path,"fichier_joint.zip",true);	
				}
									
				ATF::hotline_mail()->sendMail();
				//Notice
				ATF::hotline()->createMailNotice("hotline_interaction_mail_to_contact");
			}catch(errorATF $e){
				//Notice
				//ATF::hotline()->createNotice("hotline_interaction_no_mail_to_contact");
			}
		}
		
		//Envoi d'un mail en interne
		//Recherche des intervenant ayant travaillé sur la hotline
		$inters=$this->ss("id_hotline",$infos["id_hotline"],"id_user");	
		$team=array();
		foreach($inters as $inter){
			if(!empty($inter["id_user"]) && ATF::$usr->getId()!=$inter["id_user"] && !in_array($inter["id_user"])){
				array_push($team,ATF::user()->select($inter["id_user"],"email"));
			}
		}

		//Ajout des actifs selectionné
		$lesactifs = is_array($infos["actifNotify"])?$infos["actifNotify"]:explode(",",$infos["actifNotify"]);
		foreach($lesactifs as $actif){
			if (!empty($actif)) {
				array_push($team,ATF::user()->select($actif,"email"));
			}
		}
		
		if(count($team)>0){
			$team = array_flip(array_flip($team)); // Suppression des doublons
			
			ATF::hotline_mail()->createMailInteractionInternal(implode(",",$team),$hotline["id_hotline"],$id_hotline_interaction,$infos["filestoattach"]["fichier_joint"]);
			if($infos["filestoattach"]["fichier_joint"]){
				//Ajout du fichier joint
				$path = $this->filepath($id_hotline_interaction,"fichier_joint");	
				$mail=ATF::hotline_mail()->getCurrentMail();
				$mail->addFile($path,"fichier_joint.zip",true);	
			}
			ATF::hotline_mail()->sendMail();
			//Notice
			ATF::hotline()->createMailNotice("hotline_interaction_mail_to_users");
		}
				
		/*---------------Mise à jour de la date de modification hotline----------------------*/
		ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"date_modification"=>date("Y-m-d H:i:s"),"disabledInternalInteraction"=>true));
		
		/*---------------Redirection + Notice----------------------*/
		ATF::hotline()->redirection("select",$infos["id_hotline"],"hotline-select-".$this->cryptId($infos["id_hotline"]).".html");
		ATF::hotline()->createNotice("hotline_interaction_done");
		
		if(ATF::db($this->db)->commit_transaction()){

			api::sendUDP(array("data"=>array("type"=>"interaction")));
		}


		return $id_hotline_interaction;		
	}
					
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Jérémie GWIAZDOWSKI <jgwiazdowski@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field,&$s,&$request){
		switch ($field) {
			case "id_user":
				return ATF::$usr->getID();
				break;
			case "detail":	
				if(ATF::_r('id_ordre_de_mission')){
					return ATF::$usr->trans("hotline_odm_detail");
				}
				break;			
			case "visible":
					//Recherche de la hotline
					if(strlen($request["id_hotline"])==32){
						$hotline_visible=ATF::hotline()->select($this->decryptId($request["id_hotline"]),"visible");
					}
					if($hotline_visible){
						return $hotline_visible;
					}else{
						return parent::default_value($field);
					}
				break;
			case "rappel-hotline":
				return ATF::hotline()->nom(ATF::_r('id_hotline'));
				break;
			case "rappelDetail-hotline":
				return nl2br(ATF::hotline()->select(ATF::_r('id_hotline'),"detail"));
				break;
			default:
				return parent::default_value($field);
		}
	}	

	/**
	* Donne le temps de presta+deplacement sur une interaction
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id_hotline_interaction l'id hotline_interaction correspondant
	* @return int le temps total travaillé en base 10. 1 = 1 heure
	*/
	public function getBillingTimeV2($id_hotline_interaction){
		$tps_presta = $this->getTime($id_hotline_interaction,"duree_presta");
		$tps_pause = $this->getTime($id_hotline_interaction,"duree_pause");
		$tps_dep = $this->getTime($id_hotline_interaction,"duree_dep");

		$temps = ($tps_presta + $tps_dep) - $tps_pause;

		return $temps;
	}


	public function getCreditV2($id_hotline_interaction){
		$id_hotline = $this->select($id_hotline_interaction , "id_hotline");

		if(ATF::hotline()->select($id_hotline, "facturation_ticket") == "non"){
			$nb = 0.00;
		}else{
			$credit = $this->getTime($id_hotline_interaction,"credit");	
		
			$credit = explode(".", strval($credit));
	                       
	        if($credit[1] > 0){
	            if($credit[1] <= 250){
	                $nb = 0.25;
	            }elseif($credit[1] <= 500){
	                $nb = 0.50;
	            }elseif($credit[1] <= 750){
	                $nb = 0.75; 
	            }else{
	                $nb = 1;
	            }
	        }   
	        $nb = $nb + $credit[0];
		}		

        return $nb;
	}


	/**
	* Donne le temps FACTURE sur une interaction
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_hotline_interaction l'id hotline_interaction correspondant
	* @return int le temps total travaillé en base 10. 1 = 1 heure
	*/
	public function getBillingTime($id_hotline_interaction){
		return $this->getTime($id_hotline_interaction,"temps");
	}
	
	/**
	* Donne le temps PASSE sur une interactiion
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_hotline_interaction l'id hotline_interaction correspondant
	* @return int le temps total travaillé en base 10. 1 = 1 heure
	*/
	public function getTotalTime($id_hotline_interaction){
		return $this->getTime($id_hotline_interaction,"temps_passe");
	}

	/**
	* Retourne le temps passé ou facturé sur une interaction bien formatté
	* @author Quentin JANON <qjanon@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id L'identifiant de l'interaction 
    * @return int le temps en base 10 (1 = 1h)
    */   
	private function getTime($id_hotline_interaction,$temps) {
		if($temps == "credit"){
			$this->q->reset()
				->addField("(`credit_presta`+`credit_dep`)","credit")
				->addCondition('id_hotline_interaction',$id_hotline_interaction)
				->setDimension('cell');
		}else{
			$this->q->reset()
				->addField("ROUND(TIME_TO_SEC(".$temps.")/3600,2)","temps")
				->addCondition('id_hotline_interaction',$id_hotline_interaction)
				->setDimension('cell');
		}

		
		return $this->sa();
	}

	/*
	* Permet de récupérer les différents états d'une requête hotline
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function listingEtat(){
		//on récupère les etats formaté tel : enum('free','fixing','wait','done','payee','annulee')
		preg_match_all("`'([[:alpha:]]*)'`",ATF::hotline()->desc['etat']['Type'],$liste);
		//et on le mets sous forme array('free'=>1,'fixing'=>1,'wait'=>1,'done'=>1,'payee'=>1,'annulee'=>1)
		foreach($liste[1] as $key=>$nom){
			$etat[$nom]=1;
		}
		return $etat;
	}


	//************************STATS*****************************/

	/**
	* Retourne les années pour lesquelles la hotline a été utilisée
	*		pour une société donnée si elle est en param
	* @author Maïté Glinkowski <mglinkowski@absystech.fr>
	* @author Fanny DECLERCK<fdeclerck@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int id_societe
	* @return array key =year item=year
	*/
	public function get_years($id_societe=NULL){
		$this->q->reset()
				->addField("YEAR( hotline_interaction.date )","year")
				->addJointure($this->table,"id_hotline","hotline","id_hotline",NULL,NULL,NULL,NULL,"inner")
				->addJointure("hotline","id_societe","societe","id_societe",NULL,NULL,NULL,NULL,"inner")
				->addCondition("hotline.pole_concerne",NULL,NULL,false,"IS NOT NULL")
				->addGroup("year")
				->addOrder("year");	
				
		if($id_societe){
			$this->q->addCondition("hotline.id_societe",$id_societe);
		}			
		
		foreach(parent::select_all() AS $tab){
			$r[$tab['year']] = $tab['year'];
		}
		return $r;
	}

	/**
	* Renvoi les societes concernées par la hotline,
	*		en fonction de l'année et en fonction du pole concerne,
	*		sous forme d'un tableau pour l'affichage du graphe des statistiques
	* @author Maïté Glinkowski <mglinkowski@absystech.fr>
	* @author Fanny DECLERCK<fdeclerck@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int annee
	* @return array key =id_societe item=societe
	*/
	public function societe_options($annee){
		$this->q->reset()
				->addField("societe.id_societe","id")
				->addField("societe.societe","societe")
				->addJointure($this->table,"id_hotline","hotline","id_hotline",NULL,NULL,NULL,NULL,"inner")
				->addJointure("hotline","id_societe","societe","id_societe",NULL,NULL,NULL,NULL,"inner")
				->addCondition("YEAR( hotline_interaction.date )",$annee)
				->addGroup("id")
				->addOrder("societe")
				->addOrder("id");		


		foreach($this->stats_filtre as $name){
			foreach($this->$name as $valeur=>$check){
				if($check)$this->q->addCondition("hotline.$name",$valeur,"OR","hotline.$name");
			}
		}
		
		foreach(parent::select_all() as $tab){
			$r[$tab['id']] = $tab['societe'];
		}
		return $r;
	}
	
	/**
	* Statistiques 
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param tab session
	* @param string annee
	* @param string id_societe
	* @param string pole 
	* @param string id_user
	* return enregistrements
	*/
	public function stats_special($annee,$id_societe=NULL,$id_user=NULL,$groupe){
		$this->q->reset();
		if(!$id_societe && $groupe!="util"){
			$this->q->addField("societe.societe","label")
					->addField("hotline.id_societe","ident");
		}else{
			$this->q->addField("CONCAT(`user`.`prenom`,' ',`user`.`nom`)","label")
					->addField("hotline_interaction.id_user","ident");
		}
		
		if($id_societe){
			$this->q->addCondition("hotline.id_societe",$id_societe);
		}
		if($id_user){
			$this->q->addCondition("hotline_interaction.id_user",$id_user,false,"hot_id_user");
		}
		
		foreach($this->stats_filtre as $name){
			foreach($this->$name as $valeur=>$check){
				if($check)$this->q->addCondition("hotline.$name",$valeur,"OR","hotline.$name");
			}
		}
		
		$this->q->addField("DATE_FORMAT(`".$this->table."`.`date`,'%Y')","y")
				->addField("DATE_FORMAT(`".$this->table."`.`date`,'%m')","mois")
				->addField("REPLACE(SUBSTR(SEC_TO_TIME(sum(TIME_TO_SEC(`hotline_interaction`.`temps_passe`))),'1','5'),':','.')",'tps')
				->setStrict()
				->addJointure($this->table,"id_user","user","id_user")
				->addJointure($this->table,"id_hotline","hotline","id_hotline")
				->addJointure("hotline","id_societe","societe","id_societe")
				->addCondition("YEAR( hotline_interaction.date )",$annee)
				->addCondition("hotline_interaction.id_user",NULL,NULL,false,"IS NOT NULL")
				->addGroup("ident")
				->addGroup("mois")
				->addOrder("mois")
				->addOrder("ident");
		$result=parent::select_all();
		
		foreach (util::month() as $k=>$i) {
			$graph['categories']["category"][$k] = array("label"=>substr($i,0,4));
		}
	
		$graph['params']['caption'] = "Nombre d heure d interaction";
		$graph['params']['yaxisname'] = "Temps (heures)";
	
		/*parametres graphe*/		
		$this->paramGraphe($dataset_params,$graph);
		
		foreach ($result as $val_) {
			$val_["mois"] = strlen($val_["mois"])<2?"0".$val_["mois"]:$val_["mois"];
			if (!$graph['dataset'][$val_["ident"]]) {
				$graph['dataset'][$val_["ident"]]["params"] = array_merge($dataset_params,array(
					"seriesname"=>preg_replace("`('|&)`","",$val_["label"])
					,"color"=>dechex(rand(0,16777216))
				));
				
				for ($m=1;$m<13;$m++) { /* Initialisation de tous les set à 0 */
					$graph['dataset'][$val_["ident"]]['set'][strlen($m)<2?"0".$m:$m] = array("value"=>0,"alpha"=>100,"titre"=>preg_replace("`('|&)`","",$val_["label"])." : 0");
				}
			}
			
			$graph['dataset'][$val_["ident"]]['set'][$val_["mois"]] = array("value"=>$val_['tps'],"alpha"=>100,"titre"=>preg_replace("`('|&)`","",$val_["label"])." : ".$val_['tps']);
		
			/* ajout de l'url */
			$graph['dataset'][$val_["ident"]]['set'][$val_["mois"]]["link"]=urlencode($this->table.".html,stats=1&annee=".$annee."&mois=".$val_["mois"]."&societe=".$id_societe."&user=".$id_user."&groupe=".$groupe."&serie=".preg_replace("`('|&)`","",$val_["label"]));
		}
		return $graph;
	}
	
	/**
	* Statistiques graphe2 (60 derniers jours)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array session
	* return enregistrements
	*/
	public function stats(){	
		$this->q->reset()
				->addField('hotline_interaction.id_user','id_user')	
				->addField("REPLACE(SUBSTR(SEC_TO_TIME(sum(case hotline.facturation_ticket when 'oui' then TIME_TO_SEC(hotline_interaction.temps)  else 0 end)),'1','5'),':','.')",'tps_charge_client')
				->addField("REPLACE(SUBSTR(SEC_TO_TIME(sum(case hotline.facturation_ticket when 'non' then TIME_TO_SEC(hotline_interaction.temps_passe)
																							when 'oui' then TIME_TO_SEC(hotline_interaction.temps_passe)-TIME_TO_SEC(hotline_interaction.temps)  else 0 end)),'1','5'),':','.')",'tps_charge_absystech')	
				->setStrict()
				->addJointure("hotline_interaction","id_hotline","hotline","id_hotline")
				->addCondition("(TO_DAYS(NOW()) - TO_DAYS(hotline_interaction.date))",'60',NULL,false,"<=")
				->addCondition("hotline_interaction.id_user",NULL,NULL,false,"IS NOT NULL")
				/*mis en commentaire au cas où l'on souhaiterait le réutiliser (que les hotlines finies)
				->addCondition("hotline.etat",'done',NULL,'resolu')
				->addCondition("hotline.etat",'payee',NULL,'payee')
				->addCondition("hotline.etat",'annulee',NULL,'annulee')
				->addSuperCondition("resolu,payee","OR","A",false)
				->addSuperCondition("A,annulee","OR","B",false)*/
				->addGroup("hotline_interaction.id_user")
				->addOrder('hotline_interaction.id_user','asc');				
		$result=parent::select_all();
		
		foreach ($result as $i) {
			$graph['categories']["category"][$i["id_user"]] = array("label"=>ATF::user()->nom($i["id_user"]));
		}
		
		$graph['params']['caption'] = "Nombre d heure d interaction sur les 60 derniers jours";
		$graph['params']['yaxisname'] = "Temps (heures)";
		
		/*parametres graphe*/		
		$this->paramGraphe($dataset_params,$graph);	
			
		$liste_etat=array('tps_charge_client'=>"006600",'tps_charge_absystech'=>"CC0000");	
			
		foreach ($result as $val_) {
			foreach($liste_etat as $etat=>$couleur){
				if (!$graph['dataset'][$etat]) {
					$graph['dataset'][$etat]["params"] = array_merge($dataset_params,array(
						"seriesname"=>ATF::$usr->trans($etat,'stats')
						,"color"=>$couleur
					));
					
					foreach ($result as $val_2) { 
						$graph['dataset'][$etat]['set'][$val_2["id_user"]] = array("value"=>0,"alpha"=>100,"titre"=>ATF::$usr->trans($etat,'stats')." : 0");
					}
				}
				$graph['dataset'][$etat]['set'][$val_["id_user"]] = array("value"=>$val_[$etat],"alpha"=>100,"titre"=>ATF::$usr->trans($etat,'stats')." : ".str_replace(".","h",$val_[$etat]));
				
				/* ajout de l'url */
				$graph['dataset'][$etat]['set'][$val_["id_user"]]["link"]=urlencode($this->table.".html,&user=".$val_["id_user"]."&stats=2");
			}
		}
		return $graph;
	}
	
	/**
	* Statistiques graphe3 (30 dernières semaines)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array session
	* @param date date_fin : date a laquelle les données ne doivent pas dépassées
	* return enregistrements
	*/
	public function stats30($date_fin,$widget=false){
		if (!$date_fin) {
			$date_fin = date("Y-m-d",time());
		}
		
		if ($widget) {
			$graph['params']['showLegend'] = "0";
			$graph['params']['bgAlpha'] = "0";
			$nb_semaine=10;
		} else {
			$graph['params']['caption'] = "Nombre d heure d interaction sur les 30 dernières semaines";
			$graph['params']['yaxisname'] = "Temps (heures)";
			$nb_semaine=30;
		}
		
		//facturation a non : temps, facturation a oui, temps_passe-temps		
		$this->q->reset()
				->addField("DATE_FORMAT(hotline_interaction.date,'%u')",'semaine')	
				->addField("DATE_FORMAT(MIN(hotline_interaction.date),'%d/%m/%Y')",'date_debut')
				->addField("DATE_FORMAT(MAX(hotline_interaction.date),'%d/%m/%Y')",'date_fin')
				->addField("REPLACE(SUBSTR(SEC_TO_TIME(sum(
					IF(    
						hotline.id_societe!=1,
						case hotline.facturation_ticket 
							when 'oui' then ((hotline_interaction.credit_presta)+(hotline_interaction.credit_dep))*3600
							else IF(hotline.id_affaire IS NOT NULL,(TIME_TO_SEC(hotline_interaction.duree_presta)+TIME_TO_SEC(hotline_interaction.duree_dep)-TIME_TO_SEC(hotline_interaction.duree_pause)),0) 
						end,
						0
					))),'1','5'),':','.')",'tps_charge_client')
				->addField("REPLACE(SUBSTR(SEC_TO_TIME(sum(
					IF(
						hotline.id_societe!=1,
						IF(
							hotline.facturation_ticket!='oui' AND hotline.id_affaire IS NULL,
							(TIME_TO_SEC(hotline_interaction.duree_presta)+TIME_TO_SEC(hotline_interaction.duree_dep)-TIME_TO_SEC(hotline_interaction.duree_pause)),
							0
						),
						0
					))),'1','5'),':','.')",'tps_charge_absystech')	
				->addField("REPLACE(SUBSTR(SEC_TO_TIME(sum(
					IF(
						hotline.id_societe=1,
						(TIME_TO_SEC(hotline_interaction.duree_presta)+TIME_TO_SEC(hotline_interaction.duree_dep)-TIME_TO_SEC(hotline_interaction.duree_pause)),
						0
					))),'1','5'),':','.')",'tps_rechdev')	
				->addField('MIN(hotline_interaction.date)','date_hot')
				->setStrict()
				->addJointure("hotline_interaction","id_hotline","hotline","id_hotline")
				->where("DATE_ADD(hotline_interaction.date, INTERVAL ".$nb_semaine." WEEK)","'".$date_fin."'",NULL,false,">=",false,false,true)
				->where("hotline_interaction.date","'".$date_fin."'",NULL,false,"<=",false,false,true)
				->whereIsNotNull("hotline_interaction.id_user")
				->addGroup("DATE_FORMAT(hotline_interaction.date,'%x %u')")
				->addOrder('date_hot','asc');
		$result=parent::select_all();
		
		foreach ($result as $i) {
			$graph['categories']["category"][$i['semaine']] = array("label"=>$i['semaine']);
		}
		
		/*parametres graphe*/		
		$this->paramGraphe($dataset_params,$graph);
			
		$liste_etat=array('tps_rechdev'=>"0000FF",'tps_charge_client'=>"00FF00",'tps_charge_absystech'=>"FF0000");	
			
		foreach ($result as $val_) {
			foreach($liste_etat as $etat=>$couleur){
				if (!$graph['dataset'][$etat]) {
					$graph['dataset'][$etat]["params"] = array_merge($dataset_params,array(
						"seriesname"=>ATF::$usr->trans($etat,'stats')
						,"color"=>$couleur
					));
					
					foreach ($result as $val_2) { 
						$graph['dataset'][$etat]['set'][$val_2['semaine']] = array("value"=>0,"alpha"=>100,"titre"=>"Du ".$val_2['date_debut']." au ".$val_2['date_fin']." : 0");
					}
				}
				$graph['dataset'][$etat]['set'][$val_['semaine']] = array("value"=>$val_[$etat],"alpha"=>100,"titre"=>"Du ".$val_['date_debut']." au ".$val_['date_fin']." : ".str_replace(".","h",$val_[$etat]));

				/* ajout de l'url */
				$graph['dataset'][$etat]['set'][$val_['semaine']]["link"]=urlencode($this->table.".html,stats=3&charge=".$etat."&date_debut=".date("Y-m-d",strtotime(str_replace("/","-",$val_['date_debut'])))."&date_fin=".date("Y-m-d",strtotime(str_replace("/","-",$val_['date_fin']))));
			}
		}
		return $graph;
	}
	
	/**
	* Statistiques graphe4 (12 dernières semaines par utilisateur)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array session
	* @param date date_fin : date a laquelle les données ne doivent pas dépassées
	* return enregistrements
	*/
	public function statsChargeParUser($date_fin){	
		$this->q->reset()
				->addField("DATE_FORMAT(hotline_interaction.date,'%u')",'semaine')	
				->addField("DATE_FORMAT(MIN(hotline_interaction.date),'%d/%m/%Y')",'date_debut')
				->addField("DATE_FORMAT(MAX(hotline_interaction.date),'%d/%m/%Y')",'date_fin')
				->addField("REPLACE(SUBSTR(SEC_TO_TIME(sum(TIME_TO_SEC(`hotline_interaction`.`temps_passe`))),'1','5'),':','.')",'tps')
				->addField("hotline_interaction.id_user",'id_user')
				->addField("MIN(hotline_interaction.date)",'date_hot')		
				->setStrict()
				->addJointure("hotline_interaction","id_hotline","hotline","id_hotline")
				->addCondition("DATE_ADD(hotline_interaction.date, INTERVAL 12 WEEK)","'".$date_fin."'",NULL,false,">=",false,false,true)
				->addCondition("hotline_interaction.date","'".$date_fin."'",NULL,false,"<=",false,false,true)
				->addCondition("hotline_interaction.id_user",NULL,NULL,false,"IS NOT NULL")
				->addGroup("hotline_interaction.id_user")
				->addGroup("DATE_FORMAT(hotline_interaction.date,'%x %u')")
				->addOrder('date_hot','asc');
		$result=parent::select_all();

		foreach ($result as $i) {
			$graph['categories']["category"][$i['semaine']] = array("label"=>$i['semaine'],'date_debut'=>$i['date_debut'],'date_fin'=>$i['date_fin']);
		}
		
		$graph['params']['caption'] = "Nombre d heure d interaction sur les 12 dernières semaines par utilisateur";
		$graph['params']['yaxisname'] = "Temps (heures)";
		
		/*parametres graphe*/		
		$this->paramGraphe($dataset_params,$graph);

		foreach ($result as $val_) {
			
			if(!isset($nom[$val_["id_user"]])){
				$nom[$val_["id_user"]]=ATF::user()->nom($val_["id_user"]);
			}
		
			if (!$graph['dataset'][$val_["id_user"]]) {
				$graph['dataset'][$val_["id_user"]]["params"] = array_merge($dataset_params,array(
					"seriesname"=>$nom[$val_["id_user"]]
					,"color"=>$couleur
				));

				//pour éviter d'écraser (donc pour optimiser le temps de chargement
				if(!isset($graph['dataset'][$val_["id_user"]]['set'])){
					foreach ($graph['categories']["category"] as $val_2) { 
						if(!isset($graph['dataset'][$val_["id_user"]]['set'][$val_2['label']])){
							$graph['dataset'][$val_["id_user"]]['set'][$val_2['label']] = array("value"=>0,"alpha"=>100,"titre"=>ATF::user()->nom($val_["id_user"])." (Du ".$val_2['date_debut']." au ".$val_2['date_fin'].") : 0");
						}
					}
				}
			}
			
			$graph['dataset'][$val_["id_user"]]['set'][$val_['semaine']] = array("value"=>$val_['tps']
																				,"alpha"=>100
																				,"titre"=>$nom[$val_["id_user"]]." (Du ".$val_['date_debut']." au ".$val_['date_fin'].") : ".str_replace(".","h",$val_['tps'])
																				/* ajout de l'url */
																				,"link"=>urlencode($this->table.".html,stats=4&user=".$val_["id_user"]."&date_debut=".date("Y-m-d",strtotime(str_replace("/","-",$val_['date_debut'])))."&date_fin=".date("Y-m-d",strtotime(str_replace("/","-",$val_['date_fin'])))));
			
		}

		return $graph;
	}
	
	/**
	* Statistiques graphe5 (production)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string annee
	* return enregistrements
	*/
	public function statsProduction($annee){
		$this->q->reset();
		
		foreach($this->liste_user as $id_user=>$check){
			if($check){
				$this->q->addCondition("hotline_interaction.id_user",$id_user,"OR","hot_id_user");
				//pour chaque user, on va chercher le nombre d'heure de congé
				$this->recupTpsConge($tps_conge,$id_user,$annee);
			}
		}
		
		$this->q->addField("DATE_FORMAT(`".$this->table."`.`date`,'%m')","mois")
				->addField("REPLACE(SUBSTRING_INDEX(SEC_TO_TIME(sum(case hotline.facturation_ticket when 'oui' then TIME_TO_SEC(hotline_interaction.temps)  else 0 end)),':',2),':','.')",'tps_facture')
				->addField("REPLACE(SUBSTRING_INDEX(SEC_TO_TIME(sum(case hotline.facturation_ticket when 'non' then TIME_TO_SEC(hotline_interaction.temps_passe)
																									when 'oui' then TIME_TO_SEC(hotline_interaction.temps_passe)-TIME_TO_SEC(hotline_interaction.temps)  else 0 end)),':',2),':','.')",'tps_non_facture')
				->setStrict()
				->addJointure($this->table,"id_hotline","hotline","id_hotline")
				->addCondition("YEAR( hotline_interaction.date )",$annee)
				->addCondition("hotline_interaction.id_user",NULL,NULL,false,"IS NOT NULL")
				->addGroup("mois")
				->addOrder("mois");
		$result=parent::select_all();

		foreach (util::month() as $k=>$i) {
			$graph['categories']["category"][$k] = array("label"=>substr($i,0,4));
		}
	
		$graph['params']['caption'] = "Production";
		$graph['params']['yaxisname'] = "Taux (".urlencode("%").")";
		$graph['params']['yAxisMaxValue'] = 100;
	
		/*parametres graphe*/		
		$this->paramGraphe($dataset_params,$graph);
		
		$liste_etat=array('tps_facture'=>"006600",'tps_non_facture'=>"CC0000",'tps_conge'=>"0000FF",'tps_non_produit'=>"OOOOOO");	
		
		$tps_non_produit=$this->TpsNonProduit($annee);
		foreach($tps_non_produit as $mois=>$valeur){
			$tps_non_produit[$mois]=$valeur*array_sum($this->liste_user);
		}

		foreach ($result as $val_) {
			//transformation en vrai minute
			if(substr($tps_conge[$val_['mois']],-1)=="5"){
				$explo=explode('.',$tps_conge[$val_['mois']]);
				$tps_conge[$val_['mois']]=$explo[0].".30";
			}
			//gestion des minutes ex: 15.56+5.14=21.10 et non pas 20.70	
			$sum_fac_non_fac=$this->AddTime($val_['tps_facture'],$val_['tps_non_facture']);
			$sum_fac_conge=$this->AddTime($sum_fac_non_fac,$tps_conge[$val_['mois']]);
			$valeur=$this->SubTime($tps_non_produit[$val_['mois']],$sum_fac_conge);

			//valeur en %
			$pourcent_fact=($val_['tps_facture']*100)/$tps_non_produit[$val_['mois']];
			$pourcent_non_fac=($val_['tps_non_facture']*100)/$tps_non_produit[$val_['mois']];
			$pourcent_conge=floor(($tps_conge[$val_['mois']]*100)/$tps_non_produit[$val_['mois']]);
			$pourcent_valeur=max(100-floor($pourcent_fact)-floor($pourcent_non_fac)-$pourcent_conge,0);

			foreach($liste_etat as $etat=>$couleur){
				if (!$graph['dataset'][$etat]) {
					$graph['dataset'][$etat]["params"] = array_merge($dataset_params,array(
						"seriesname"=>ATF::$usr->trans($etat,'stats')
						,"color"=>$couleur
					));
					
					//Initialisation
					for ($m=1;$m<13;$m++) {
						if($etat=="tps_non_produit"){
							$graph['dataset'][$etat]['set'][strlen($m)<2?"0".$m:$m] = array("value"=>100,"alpha"=>15,"titre"=>ATF::$usr->trans($etat,'stats')." : 100".urlencode("%")." (".str_replace('.','h',$tps_non_produit[(strlen($m)<2?"0".$m:$m)])."h)");
						}else{
							$graph['dataset'][$etat]['set'][strlen($m)<2?"0".$m:$m] = "";
						}
					}
				}
				
				if($etat=="tps_non_produit"){
					$graph['dataset'][$etat]['set'][$val_['mois']] = array("value"=>$pourcent_valeur,"alpha"=>15,"titre"=>ATF::$usr->trans($etat,'stats')." : ".$pourcent_valeur.urlencode("%")." (".(preg_match('`\.`',$valeur)?str_replace('.','h',$valeur):$valeur."h").")");
				}elseif($etat=="tps_conge"){
					if($pourcent_conge){
						$graph['dataset'][$etat]['set'][$val_['mois']] = array("value"=>$pourcent_conge,"alpha"=>100,"titre"=>ATF::$usr->trans($etat,'stats')." : ".$pourcent_conge.urlencode("%")." (".(preg_match('`\.`',$tps_conge[$val_['mois']])?str_replace('.','h',$tps_conge[$val_['mois']]):$tps_conge[$val_['mois']]."h").")");
					}else{
						$graph['dataset'][$etat]['set'][$val_['mois']] ="";
					}
				}else{
					$pourcent=floor(($val_[$etat]*100)/$tps_non_produit[$val_['mois']]);
					$graph['dataset'][$etat]['set'][$val_['mois']] = array("value"=>$pourcent,"alpha"=>100,"titre"=>ATF::$usr->trans($etat,'stats')." : ".$pourcent.urlencode("%")." (".str_replace('.','h',$val_[$etat]).")");
				}
			}
		}

		return $graph;
	}
		
	/**
	* Lorsque l'on clique sur un graphe, on redirige vers le resultat qu'on souhaite sur le select_all filtré par la barre sur laquelle on a cliqué
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/	
	public function statsFiltrage(){
		/* nom du filtre */
		$donnees['name']='Filtre de stats';
		/* Pour que chaque condition soit reliée en AND */
		$donnees['mode']='AND';
		
		//pour les différents graphes
		switch(ATF::_r('stats')){
			//Graphe 1
			case 1:
				//pour la date
				$this->setConditionFiltre($donnees,$this->table.".date",'LIKE%',ATF::_r('annee')."-".ATF::_r('mois'),0);
				
				if(ATF::_r('user')){
					$this->setConditionFiltre($donnees,$this->table.".id_user",'LIKE',ATF::user()->nom(ATF::_r('user')),1);
				}
				if(ATF::_r('societe')){
					$this->setConditionFiltre($donnees,"hotline.id_societe",'LIKE',ATF::societe()->select(ATF::_r('societe'),'societe'),2);
				}
				$i=2;
				
				foreach($this->stats_filtre as $name){
					foreach($this->$name as $valeur=>$check){
						if($check==0){
							$i=$i+1;
							if(!$donnees['choix_join']){
								$donnees['choix_join']="left";
								$donnees['jointures'][0]['nom_module']="hotline";
								$donnees['jointures'][0]['module']="hotline.id_hotline";
								$donnees['jointures'][0]['liste_champs']="hotline_interaction.id_hotline";
							}
							$this->setConditionFiltre($donnees,"hotline.$name",'!=',$valeur,$i);	
						}
					}
				}
				
				$i=$i+1;			
				if(!ATF::_r('societe') && ATF::_r('groupe')!='util'){
					$this->setConditionFiltre($donnees,"hotline.id_societe",'LIKE',ATF::_r('serie'),$i);
				}elseif(!ATF::_r('user')){
					$this->setConditionFiltre($donnees,$this->table.".id_user",'LIKE',ATF::_r('serie'),$i);
				}
			break;
			//Graphe 2
			case 2:
				$this->setConditionFiltre($donnees,$this->table.".date",'>=',date("Y-m-d",strtotime(date("Y-m-d")." -60 days")),0);
				$this->setConditionFiltre($donnees,$this->table.".id_user",'LIKE',ATF::user()->nom(ATF::_r('user')),1);
			break;
			//Graphe 3
			case 3:
				$donnees['choix_join']="left";
				$donnees['jointures'][0]['nom_module']="hotline";
				$donnees['jointures'][0]['module']="hotline.id_hotline";
				$donnees['jointures'][0]['liste_champs']="hotline_interaction.id_hotline";
					
				$this->setConditionFiltre($donnees,$this->table.".date",'>=',ATF::_r('date_debut'),0);
				$this->setConditionFiltre($donnees,$this->table.".date",'<=',ATF::_r('date_fin')." 23:59:59",1);
				if(ATF::_r('charge')=='tps_charge_client'){
					$this->setConditionFiltre($donnees,'hotline.facturation_ticket','=','oui',2);
				}else{
					$this->setConditionFiltre($donnees,'hotline.facturation_ticket','=','non',2);
				}
			break;
			//Graphe 4
			case 4:
				$this->setConditionFiltre($donnees,$this->table.".date",'>=',ATF::_r('date_debut'),0);
				$this->setConditionFiltre($donnees,$this->table.".date",'<=',ATF::_r('date_fin')." 23:59:59",1);
				$this->setConditionFiltre($donnees,$this->table.".id_user",'LIKE',ATF::user()->nom(ATF::_r('user')),2);
			break;
		}
		
		$insertion=array(
			"filtre_optima"=>$donnees['name']
			,"id_module"=>ATF::module()->from_nom($this->table)
			,"id_user"=>ATF::$usr->getID()
			,"options"=>serialize($donnees)
			,"type"=>"prive");

		//si le filtre existe déjà on le supprime
		ATF::filtre_optima()->q->reset()
								->addCondition('id_module',ATF::module()->from_nom($this->table))
								->addCondition('id_user',ATF::$usr->getID())
								->addCondition('filtre_optima',$donnees['name'])
								->addCondition('type',"prive")
								->setDimension('farro');
		$ancien_filtre=ATF::filtre_optima()->select_all();
								
		if($ancien_filtre[0]){
			$insertion['id_filtre_optima']=$ancien_filtre[0]['id_filtre_optima'];
			ATF::filtre_optima()->update($insertion);
			$id_filtre=$ancien_filtre[0]['id_filtre_optima'];
		}else{						
			//sinon on le créé
			$id_filtre=ATF::filtre_optima()->insert($insertion);
		}
		
		return $id_filtre;
	   
	} 
	
	/**
	* Permet de sauvegarder la liste des users sur lesquelles afficher la charge
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function changeUser($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$dif=array_diff_key($this->liste_user,array_flip($infos['tabuser']));

		foreach($this->liste_user as $key=>$item){
			if(isset($dif[$key]))$this->liste_user[$key]=0;
			else $this->liste_user[$key]=1;
		}

		$infos['current_class']=ATF::stats();
		ATF::$cr->add('main','stats_menu.tpl.htm',$infos);
	}
	
	/** Permet de calculer le temps passé en période de congé
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param float $tps_conge : pointeur pour le récupérer pour le graphe de stats
	* @param integer $id_user : user sur qui checker les congés
	* @param string $annee : année sur laquelle checker les congés
	*/
	public function recupTpsConge(&$tps_conge,$id_user,$annee){
		// pour chaque mois de l'annee
		foreach (util::month() as $k=>$i) {		
			ATF::conge()->q->reset()
							->addCondition('conge.id_user',$id_user)
							->addCondition("DATE_FORMAT(conge.date_debut,'%Y-%m')",$annee."-".$k,'OR','intermonth')
							->addCondition("DATE_FORMAT(conge.date_fin,'%Y-%m')",$annee."-".$k,'OR','intermonth')
							->addCondition('conge.etat',"ok");
			foreach(ATF::conge()->sa() as $key=>$item){
				switch($item['periode']){
					case 'am':
					case 'pm':
						$tps_conge[$k]+=3.50;
					break;
					case 'jour':
						$tps_conge[$k]+=7;
					break;
					case 'autre':
						//si l'annee n'est pas identique sur debut et fin
						if(date('Y',strtotime($item['date_debut']))<$annee){
							$item['date_debut']=$annee."-01-01 00:00:00";
						}elseif(date('Y',strtotime($item['date_fin']))>$annee){
							$item['date_fin']=$annee."-12-31 23:59:59";
						}
						
						//si le mois n'est pas identique sur debut et fin
						if(date('m',strtotime($item['date_debut']))!=$k){
							$item['date_debut']=$annee."-".$k."-01 00:00:00";
						}elseif(date('m',strtotime($item['date_fin']))!=$k){
							$item['date_fin']=$annee."-".$k."-31 23:59:59";
						}

						//nombre de jour ouvre
						$nb_ouvre=util::get_nb_open_days(strtotime($item['date_debut']),strtotime($item['date_fin']));
						//nombre de jour ouvré * 7h (nbre heure contractuelle par jour)
						$tps_conge[$k]+=$nb_ouvre*7;
					break;
				}
			}
		}	
	}
	
	/** 
	* Récupère, pour chaque mois de l'année, le temps théorique pendant lequel on doit travaillé
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $annee : année en question
	* @return array $tps_non_produit : nombre d'heure théorique trouvé par mois
	*/
	public function TpsNonProduit($annee){
		foreach (util::month() as $k=>$i) {
			//nombre de jour ouvre
			$nb_ouvre=util::get_nb_open_days(strtotime($annee."-".$k."-01"),strtotime($annee."-".$k."-".$this->nb_jours($k,$annee)));
			//nombre de jour ouvré * 7h (nbre heure contractuelle par jour)
			$tps_non_produit[$k]+=$nb_ouvre*7;
		}
		return $tps_non_produit;
	}
	
	/** 
	* Récupère le nombre de jours qu'il y a dans le mois et l'année
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $mois : mois en question
	* @param string $annee : année en question
	* @return string : nombre de jour trouvé
	*/
	public function nb_jours($month,$annee){
		if ($month=="04"||$month=="06"||$month=="09"||$month=="11") return 30;
		else if ($month=="02") {
			if ($this->is_bissextile($annee)) return 29;
			else return 28;
		}
		else return 31;	
	}
	
	/**
	* Détecte si l'année passée est bissextile
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $annee : année en question
	* @return boolean
	*/
	public function is_bissextile($annee){
		if ($annee==null) $annee = date("Y");
		return  ( ($annee%400==0)?true: ( ($annee%4==0 && $annee%100!=0)?true:false ) );
	}
	
	/** 
	* Additionne deux heures formatés spécialement
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $time1 : format => h.m
	* @param string $time2 : format => h.m
	*/
	public function AddTime($time1,$time2) {
		$tps1=explode('.',$time1);
		$tps2=explode('.',$time2);
		
		$heures=$tps1[0]+$tps2[0];
		
		$minutes=$tps1[1]+$tps2[1];
		if($minutes>=60){
			$heures+=floor(($tps1[1]+$tps2[1])/60);
			$minutes=($tps1[1]+$tps2[1])%60;
		}
		
		return $heures.".".($minutes<10?"0".$minutes:$minutes);
	} 
	
	/** 
	* Soustrait deux heures formatés spécialement
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $time1 : format => h.m
	* @param string $time2 : format => h.m
	*/
	public function SubTime($time2,$time1) {
		$tps2=explode('.',$time2);
		$tps1=explode('.',$time1);
		
		$heures=$tps2[0]-$tps1[0];
		
		$minutes=$tps2[1]-$tps1[1];
		if($minutes<0){
			$heures-=1;
			$minutes=60+$minutes;
		}

		return $heures.".".($minutes<10?"0".$minutes:$minutes);
	}  
	
	/**
	* Filtre sur l'état de la requête
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param string $filtre : etat, pole ou facturation_ticket
	* @param string $nom : différents états d'une requête, facturation_ticket oui ou non, noms des pôles
	* @param bool $etat
	*/
	public function modifEtat($filtre,$nom,$etat){
		$filtres=$this->$filtre;
		$filtres[$nom]=($etat=="true"?1:0);
		$this->$filtre=$filtres;
		//on ne peut ecrire comme cela : $this->$filtre[$nom]=($etat=="true"?1:0);, sinon il cherche un attribut qui serait la valeur du tableau $filtre[$nom]
	}
	
	/**
	* Retourne les users qui ont créé des hotline_interaction
	*	sous forme d'un tableau  key =id_user item=nom
	* @author Maïté Glinkowski <mglinkowski@absystech.fr>
	* @author Fanny DECLERCK<fdeclerck@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return array
	*/
	public function get_user(){
		ATF::user()->q->reset()
			->addField("user.id_user","id")
			->addField("CONCAT(`user`.`civilite`,' ',`user`.`prenom`,' ',`user`.`nom`)","nom")
			->setStrict()
			->addJointure("user","id_user","hotline_interaction","id_user",NULL,NULL,NULL,NULL,"inner")
			->addGroup('id');
		
		foreach(ATF::user()->sa() AS $tab){
			$r[$tab['id']]  = $tab['nom'];
		}
		return $r;
	}
	
	/**
	* Retourne les users qui ont créé des hotline_interaction, seulement ceux qui ont un profil et actif
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return array
	*/
	public function getUserActif(){
		ATF::user()->q->reset()
			->addField("user.id_user","id")
			->addField("CONCAT(`user`.`civilite`,' ',`user`.`prenom`,' ',`user`.`nom`)","nom")
			->setStrict()
			->addJointure("user","id_user","hotline_interaction","id_user",NULL,NULL,NULL,NULL,"inner")
			->addCondition('etat','normal')
			->addConditionNotNull('id_profil')
			->addGroup('id');
		
		foreach(ATF::user()->sa() AS $tab){
			$r[$tab['id']]  = 1;
		}
		return $r;
	}
	
	/**
	* Retourne les interactions ayant eu lieues depuis la dernière activité
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function getRecentForMobile($countUnseenOnly=false){
		$this->q->reset()
			->addField("hotline_interaction.date","date")
			->addField("hotline_interaction.id_user")
			->addField("hotline_interaction.id_contact")
			->addField("hotline_interaction.detail","detail")
			->addField("hotline_interaction.id_hotline","id")
			
			// Ces dernières 72h
			->andWhere("hotline_interaction.date",date("Y-m-d H:i:s",time()-86400*3),"date",">");
			
		if ($countUnseenOnly) {
			$this->q->setCountOnly()
				->andWhere("hotline_interaction.date",ATF::$usr->last_activity,"date",">");
			return parent::select_all();
		} else {	
			$return = parent::select_all();
			foreach ($return as $k=>$i) {
				$return[$k]["user"] = $return[$k]["hotline_interaction.id_user"];
				$return[$k]["contact"] = $return[$k]["hotline_interaction.id_contact"];
				
				$return[$k]["humanDate"] = ATF::$usr->date_trans($return[$k]["date"],true,false,true);
				
				if ($return[$k]["date"]>ATF::$usr->last_activity) {
					$return[$k]["date"] = "=> ".ATF::$usr->trans("unseen");
				}
				
			}
			return $return;
		}
	}

	/**
	* Retourne Vrai si l'intervenant est deja intervenu sur l'id_hotline
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id_intervenant l'id de l'intervenant 
	* @param $id_hotline l'id de la requete hotline
	* @return boolean Vrai si l'intervenant est deja intervenu sur la requete
	*/
	public function isIntervenant($id_intervenant, $id_hotline){
		$id_hotline = ATF::hotline()->decryptId($id_hotline);
		$id_intervenant = ATF::user()->decryptId($id_intervenant);

		$this->q->reset()->where("id_hotline", $id_hotline)
						 ->where("id_user",$id_intervenant);
		$res = $this->select_all();
		if(is_array($res)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	* Méthode spéciale par défaut "saCustom"
	* Appel la méthode de classe particulière à utiliser,  si $method =flase on utilise select_all
	* Utilisation dans generic_select_all
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $s : la session
	* @param string $method : methode de classe particulière à utiliser
	* @return array Résultat de la requête
	*/
	public function select_data(&$s,$method=false){
		if (!$method) {
			$method="saCustom";
		}
		return parent::select_data($s,$method);
	}

	/**
	* Surcharge du select-All
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function saCustom(){
		if ($this->q->count!==false && $this->q->count!==1){
			$this->q->setCount();
		}
		$this->q
			->addField("hotline_interaction.visible")
			->addField("TIME_TO_SEC(hotline_interaction.duree_dep)/3600","duree_dep")
			->addField("TIME_TO_SEC(hotline_interaction.duree_presta)/3600","duree_presta")
			->addField("TIME_TO_SEC(hotline_interaction.duree_pause)/3600","duree_pause");

		$return = 	$this->select_all();
		return $return;
	}

	public function getInteractionIntoDate($date_deb, $date_fin){
		ATF::hotline_interaction()->q->reset()
									->setStrict()										
									->where("hotline_interaction.date", $date_deb." 00:00:00' AND '".$date_fin." 23:59:59","AND",false,"BETWEEN")
									->where("hotline_interaction.duree_presta","00:00:00","AND",false,"!=")
									->whereIsNotNull("hotline_interaction.id_user");
		if(ATF::$codename == "absystech"){
			ATF::hotline_interaction()->q->where("hotline_interaction.id_user",30,"AND","usr","!=") //Guirec
										 ->where("hotline_interaction.id_user",62,"AND","usr","!=") // Laurent Hubau
										 ->where("hotline_interaction.id_user",57,"AND","usr","!=") // Gauthier
										 ->where("hotline_interaction.id_user",3,"AND","usr","!=") // Sebasien
										 ->where("hotline_interaction.id_user",64,"AND","usr","!=") // Thibaut
										 ->where("hotline_interaction.id_user",54,"AND","usr","!="); // Emma
		}else{
			ATF::hotline_interaction()->q->where("hotline_interaction.id_user",33,"OR","usr","=") // Guirec
										 ->where("hotline_interaction.id_user",47,"OR","usr","="); //Laurent
		}										
		return ATF::hotline_interaction()->sa();
	}

	public function getDataMargeHotline($data, $id_user , &$workedHour){
		$return = array();
		foreach ($data as $key => $value) {
      		$compteur=0;
    		foreach ($value as $khi => $vhi) {
    			if($compteur == 0){    				
    				$hotline = ATF::hotline()->select($vhi["id_hotline"]);	    		
		    		$return[$key]["id_hotline"] = $hotline["id_hotline"];
		    		$return[$key]["hotline"] = $hotline["hotline"];
		    		$return[$key]["societe"] = ATF::societe()->select($hotline["id_societe"],"societe");
		    		$return[$key]["date"] =date("d/m/Y", strtotime($hotline["date"]));
		    		$id_affaire = $hotline["id_affaire"];

		    		if($hotline["date_fin"]){ $return[$key]["date_fin"] = date("d/m/Y", strtotime($hotline["date_fin"])); }
		    		else{ $return[$key]["date_fin"] = ""; }
    			}

    			if ($id_affaire && !$affaires[$id_affaire]) {
	    			$affaires[$id_affaire] = ATF::affaire()->select($id_affaire);
    			}

    			$temps_passe = (ATF::hotline()->getSecond($vhi["duree_presta"])
    						   +ATF::hotline()->getSecond($vhi["duree_dep"]))
    			   			   -ATF::hotline()->getSecond($vhi["duree_pause"]);

    			if($id_user) $workedHour[$id_user] += round($temps_passe/3600,2);

    			$return[$key]["temps_passe"] += round($temps_passe/3600,2);

				if($temps_passe != 0){
					$facturation_ticket = $hotline["facturation_ticket"];

					if($facturation_ticket == "oui"){
						$temps = ($vhi["credit_presta"]+$vhi["credit_dep"]);
						$return[$key]["temps_facture"] += round($temps,2);
						$return[$key]["marge_brute"] += round($temps*__TAUX_HORAIRE_TICKET__,2);
						$return[$key]["affaire"] = "";
    					$return[$key]["taux_horaire"] = __TAUX_HORAIRE_TICKET__;    					
    												
					}else{						
						if($id_affaire){	
							if (in_array($affaires[$id_affaire]["etat"],array("commande","facture","terminee"))) {
								// Affaires signées
								if(! isset($THAffaire[$id_affaire])){							
									$THAffaire[$id_affaire] = ATF::hotline()->getTauxHorraire($id_affaire);
								}
								$return[$key]["marge_brute"] += round(($temps_passe/3600)*$THAffaire[$id_affaire],2);
								$return[$key]["affaire"] = ATF::affaire()->select($id_affaire , "affaire");
	    						$return[$key]["taux_horaire"] = round($THAffaire[$id_affaire],2);

							} else {
								// Affaires non signées
								if(! isset($THAffaire[$id_affaire])){							
									$THAffaire[$id_affaire] = ATF::hotline()->getTauxHorraire($id_affaire);
								}
								$return[$key]["marge_brute"] = round(($temps_passe/3600)*$THAffaire[$id_affaire],2);
								$return[$key]["affaire"] = ATF::affaire()->select($id_affaire , "affaire");
	    						$return[$key]["taux_horaire"] = 0;
							}
						}else{
							$return[$key]["temps_facture"] = 0;
							$return[$key]["marge_brute"] = 0;
    						$return[$key]["affaire"] = "";
    						$return[$key]["taux_horaire"] = 0;

						}
					}
					$return[$key]["cout_homme"] = round($return[$key]["temps_passe"]*__COUT_HORAIRE_TECH__,2);
					$return[$key]["marge_nette"] = round($return[$key]["marge_brute"] - $return[$key]["cout_homme"] ,2);
				}
				$compteur++;
	    	}
    	}
    	return $return;
	}

	public function export_marge_hotline($infos){
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php"; 
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";  
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());        
		$workbook = new PHPExcel;        
        

		$this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

		if($this->q->between_date["debut"]){ $date_deb = $this->q->between_date["debut"]; }
		else { $date_deb = date("Y-01-01"); }
		
		if($this->q->between_date["fin"]){ $date_fin = $this->q->between_date["fin"]; }
		else { $date_fin = date("Y-m-d"); }

		
		$workedHour = array();
		$workedHour[0] = ATF::hotline()->getJoursOuvres($date_deb, $date_fin);

				
		$users = array();

		$result= $this->getInteractionIntoDate($date_deb, $date_fin);

		foreach ($result as $key => $value) {
			$data[$value["id_hotline"]][$value["id_hotline_interaction"]] = $value;
			$users[$value["id_user"]][$value["id_hotline"]][$value["id_hotline_interaction"]] = $value;
		}
		
		foreach ($users as $key => $value) {
			$feuilles[] = array("titre" => ATF::user()->nom($key) , 
								"user" => $key); 
		}
		$feuilles[]["titre"] = "Tout";
		
		$premfeuille = true;

		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
				

		foreach ($feuilles as $key => $value) {				
			if ($premfeuille){	
				$workbook->setActiveSheetIndex($key);	
			    $sheet = $workbook->getActiveSheet();				
			    $sheet->setTitle($value["titre"]);
			    $this->ajoutTitreExport($sheet); 	
			    $this->ajoutDataExport($sheet, $users[$value["user"]] ,$value["user"], $workedHour);		    
			    $premfeuille = false;
			}else{
				$sheet = $workbook->createSheet($key);
				$workbook->setActiveSheetIndex($key);	
				$sheet = $workbook->getActiveSheet();
				$sheet ->setTitle($value["titre"]);
				$this->ajoutTitreExport($sheet);
				if($value["titre"] == "Tout"){	$this->ajoutDataExport($sheet,$data, NULL, $workedHour); }
				else{	$this->ajoutDataExport($sheet, $users[$value["user"]], $value["user"], $workedHour);	}
				
			}
		}  
		
		$writer = new PHPExcel_Writer_Excel5($workbook);
		
		$writer->save($fname);           
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=Export_Marge_Hotline.xls');			
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");         
		fpassthru($fh);   
		unlink($fname);   
		PHPExcel_Calculation::getInstance()->__destruct();
	}

	/** Mise en place des titres         
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr> 
     */     
    public function ajoutTitreExport(&$sheet, $titre){

    	$titre = array("#","Hotline","Société","Affaire","Date","Date de cloture","Nombre heures passées","Nombre heures facturées","Marge brute", "Taux horaire","Coût homme","Marge Nette");
  
    	foreach ($titre as $key => $value) { $row_data[] = array($value,150); }
    	$i=0;
    	foreach($row_data as $col=>$titre){
			$sheet->setCellValueByColumnAndRow($i , 1, $titre[0]);
			$sheet->getColumnDimension($col)->setWidth($titre[1]);  
			$i++;
        }
    }



    /** Insertion des données dans le tableau         
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr> 
    */     
    public function ajoutDataExport(&$sheet, $data, $id_user ,&$workedHour){
    	$row_auto=1;
   		
    	$result = $this->getDataMargeHotline($data, $id_user , $workedHour);

      	foreach ($result as $key => $value) {
      		$row_data[$key][0] = $value["id_hotline"];
			$row_data[$key][1] = $value["hotline"];
			$row_data[$key][2] = $value["societe"];
			$row_data[$key][3] = $value["affaire"];
			$row_data[$key][4] = $value["date"];
			$row_data[$key][5] = $value["date_fin"];
			$row_data[$key][6] = $value["temps_passe"];
			$row_data[$key][7] = $value["temps_facture"];
			$row_data[$key][8] = $value["marge_brute"];
			$row_data[$key][9] = $value["taux_horaire"];
			$row_data[$key][10] = $value["cout_homme"];
			$row_data[$key][11] = $value["marge_nette"];
      	}

    	$i=0;
    	$j=2;
    	foreach ($row_data as $ligne => $value){
	    	foreach($value as $col=>$titre){
				$sheet->setCellValueByColumnAndRow($i , $j, $titre);		
				$i++;				
	        }
	        $i=0;
	        $j++;
	    }

	    $j+3;
	    if($id_user){
	   		$sheet->setCellValueByColumnAndRow(0 , $j, "Temps pointé");
	   		$sheet->setCellValueByColumnAndRow(1 , $j, $workedHour[$id_user]);
	   		$j++;
	   		$temps_partiel = number_format(ATF::user()->select($id_user ,"temps_partiel"),2);
	   		$sheet->setCellValueByColumnAndRow(0 , $j, "Temps travaillé");
	   		$sheet->setCellValueByColumnAndRow(1 , $j, ($workedHour[0]*7*$temps_partiel));
	   		$j++;
	   		$sheet->setCellValueByColumnAndRow(0 , $j, "Non pointé");
	   		$npt = ($workedHour[0]*7*$temps_partiel)-$workedHour[$id_user];
	   		$workedHour["ALL"]["pointe"] += $workedHour[$id_user];
	   		$workedHour["ALL"]["travaille"] += $workedHour[0]*7*$temps_partiel;
	   		if($npt > 0){
	   			$workedHour["ALL"]["non_pointe"] += $npt;
		   		$sheet->setCellValueByColumnAndRow(3 , $j, "");
	   			$sheet->setCellValueByColumnAndRow(6 , $j, round($npt,2));
		   		$sheet->setCellValueByColumnAndRow(8 , $j, 0);
		   		$sheet->setCellValueByColumnAndRow(9 , $j, __TAUX_HORAIRE_TICKET__);
		   		$sheet->setCellValueByColumnAndRow(10 , $j, round($npt*__COUT_HORAIRE_TECH__ ,2));
		   		$sheet->setCellValueByColumnAndRow(11 , $j, 0 - round($npt*__COUT_HORAIRE_TECH__ ,2));
	   		}	   		
	    }else{
	    	$sheet->setCellValueByColumnAndRow(0 , $j, "Temps pointé");
	   		$sheet->setCellValueByColumnAndRow(1 , $j, $workedHour["ALL"]["pointe"]);
	   		$j++;
	   		$sheet->setCellValueByColumnAndRow(0 , $j, "Temps travaillé");
	   		$sheet->setCellValueByColumnAndRow(1 , $j, $workedHour["ALL"]["travaille"]);
	   		$j++;
	   		$sheet->setCellValueByColumnAndRow(0 , $j, "Non pointé");
	   		$npt = $workedHour["ALL"]["non_pointe"];	
	   		$sheet->setCellValueByColumnAndRow(3 , $j, "");   			
   			$sheet->setCellValueByColumnAndRow(6 , $j, round($npt,2));
	   		$sheet->setCellValueByColumnAndRow(8 , $j, 0);
	   		$sheet->setCellValueByColumnAndRow(9 , $j, __TAUX_HORAIRE_TICKET__);
	   		$sheet->setCellValueByColumnAndRow(10 , $j, round($npt*__COUT_HORAIRE_TECH__ ,2));
	   		$sheet->setCellValueByColumnAndRow(11 , $j, 0 - round($npt*__COUT_HORAIRE_TECH__ ,2));
	   		   		
	    }
	   
    }



	/**
	* Permet de récupérer la liste des interaction hotline pour telescope
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr> 
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/ 
	//$order_by=false,$asc='desc',$page=false,$count=false,$noapplyfilter=false
	public function _GET($get,$post) {

		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "hotline_interaction.id_hotline_interaction_fk";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;

		$colsData = array(
			"hotline_interaction.id_hotline_interaction"=>array(),
			"hotline_interaction.id_hotline"=>array(),
			"hotline_interaction.date"=>array(),
			"SUBSTR(hotline_interaction.heure_debut_presta,1,5)"=>array("alias"=>"heure_debut_presta"),
			"SUBSTR(hotline_interaction.heure_fin_presta,1,5)"=>array("alias"=>"heure_fin_presta"),
			"SUBSTR(hotline_interaction.heure_depart_dep,1,5)"=>array("alias"=>"heure_depart_dep"),
			"SUBSTR(hotline_interaction.heure_arrive_dep,1,5)"=>array("alias"=>"heure_arrive_dep"),
			"hotline_interaction.credit_presta",
			"SUBSTR(hotline_interaction.duree_presta,1,5)"=>array("alias"=>"tps_passe"),
			"SUBSTR(hotline_interaction.duree_pause,1,5)"=>array("alias"=>"tps_pause"),
			"hotline_interaction.credit_dep",
			"hotline_interaction.detail"=>array(),
			"hotline_interaction.id_user"=>array(),
			"hotline_interaction.id_contact"=>array(),
			"hotline_interaction.visible"=>array(),
			"hotline_interaction.matos"=>array(),
			"hotline_interaction.nature"=>array(),
			"hotline.id_affaire"=>array("alias"=>"id_affaire"),
			"alerte.alerte"=>array(),
			"alerte.id_alerte"=>array()
		);


		$this->q->reset();

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			$this->q->setSearch($get["search"]);
		}

		if ($get['id']) {
			$this->q->where("hotline_interaction.id_hotline_interaction",$get['id'])->setLimit(1);
		} elseif ($get['id_hotline']) {
			$this->q->where("hotline_interaction.id_hotline",$get['id_hotline'])->setLimit($get['limit']);
		} else {
			$this->q->setLimit($get['limit']);
		}

		switch ($get['tri']) {
			case 'id_hotline':
			case 'id_user':
			case 'id_contact':
				$get['tri'] = "hotline_interaction.".$get['tri'];
			break;
		}


		$this->q->addField($colsData);

		$this->q->from("hotline_interaction","id_contact","contact","id_contact");
		$this->q->from("hotline_interaction","id_hotline","hotline","id_hotline");
		$this->q->from("hotline_interaction","id_user","user","id_user");
		$this->q->from("hotline_interaction","id_hotline_interaction","alerte","id_hotline_interaction");


		$this->q->setToString();
		$sql = $this->select_all($get['tri'],$get['trid'],$get['page'],true);
		$this->q->unsetToString();
		
		header("TS-sql-debug: ".$sql);
		$data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);

		foreach ($data["data"] as $k=>$lines) {
			foreach ($lines as $k_=>$val) {
				if (strpos($k_,".")) {
					$tmp = explode(".",$k_);
					$data['data'][$k][$tmp[1]] = $val;
					unset($data['data'][$k][$k_]);
				}				
			}
			$lines = $data['data'][$k];

			if (!$data['data'][$k]['id_user_fk']) {
				unset($data['data'][$k]['id_user'],$data['data'][$k]['id_user_fk']);
			}

			$data['data'][$k]['detail'] = $this->remove_empty_tags_recursive($data['data'][$k]['detail']);

			if ($lines["id_user_fk"]) {
				$v = $this->getTime($lines['id_hotline_interaction_fk'],"duree_presta");

				if ($v != "0.00") $data['data'][$k]['duree_presta'] = $v;
				$v = $this->getTime($lines['id_hotline_interaction_fk'],"duree_pause");
				if ($v != "0.00") $data['data'][$k]['duree_pause'] = $v;
				$v = $this->getTime($lines['id_hotline_interaction_fk'],"duree_dep");
				if ($v != "0.00") $data['data'][$k]['duree_dep'] = $v;
			}
		}

		if ($get['id']) {
	        $return = $data['data'][0];			
	        $return['isFacturable'] = ATF::hotline()->_isFacturable(array("id_hotline_interaction"=>$get['id']));
		} elseif ($get['id_hotline']) {
	        $return = $data['data'];
	        foreach ($return as $k=>$o) {
		        $isFacturable = ATF::hotline()->_isFacturable(array("id_hotline_interaction"=>$o['id_hotline_interaction_fk']));
	        	$return[$k]['isFacturable'] = $isFacturable;
	        }
		} else {
			// Envoi des headers
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);

	        $return = $data['data'];			
		}

		return $return;
	}

	/**
	* Permet de virer tous les tags vide d'une chaine de caractère, et virer les multiple BR qui s'enchaine.
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr> 
	* @param $str string Chaine a traitée
	* @param $repto string replacementstring chaine modifié.
	*/ 
	public function remove_empty_tags_recursive ($str, $repto = NULL){
	    //** Return if string not given or empty.
	    if (!is_string ($str) || trim ($str) == '') return $str;
	    //** Recursive empty HTML tags.
	    $str = preg_replace(
	        //** Pattern written by Junaid Atari.
	        '/<([^<\/>]*)>([\s]*?|(?R))<\/\1>/imsU',
	        //** Replace with nothing if string empty.
	        !is_string ($repto) ? '' : $repto,
	        //** Source string
	        $str
	    );

	    // ENleve les multiple BR
	    $str = preg_replace("/(<br\s*\/?>\s*)+/", "<br/>", $str);
	    return $str;
	}

	/**
	* Permet d'insérer une interaction hotline depuis telescope
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr> 
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array COntient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/ 
	public function _POST($get,$post) {

    	$return = array();

        try {
        	
	        if (!$post) throw new Exception("POST_DATA_MISSING",1000);
	        // Check des champs obligatoire
	        if (!$post['id_hotline']) throw new Exception("ID_HOTLINE_MISSING",1100);
	        if (!$post['detail'] || $post['detail']=="<p><br></p>") throw new Exception("CONTENT_MISSING",1101);
	        if (!$post['temps_passe'] || $post['temps_passe']=="00:00:00") throw new Exception("TEMPS_PASSE_MISSING",1102);
	        if (!$post['date']) $post['date'] = date("Y-m-d H:i:s");

	        // Mapping pour BDD Optima
	        $tps = substr($post['temps_passe'],0,5);
	        if ($tps == "00:00") {
	        	$tps = "00:01";
	        }
	        $post['temps_passe'] = $post['duree_presta'] = $tps;

	        if (!$post['heure_debut_presta'] || !$post['heure_fin_presta']) {
	        	// On créer un date time
	        	$date = new DateTime();
	        	// On stock la date car c'est la date de fin
	        	$dayEnd = $date->format('d');
	        	$post['heure_fin_presta'] = $date->format('H:i:s');
	        	// On initialise l'interval a soustraire grace au temps passé
	        	$tosub = new DateInterval("PT".str_replace(":", "H", $tps)."M");
	        	$date->sub($tosub);
	        	$post['heure_debut_presta'] = $date->format('H:i:s');
	        	$dayBegin = $date->format('d');

	        	if ($dayEnd != $dayBegin) {
	        		throw new errorATF("Impossible d'enregistrer l'interaction car elle chevauche deux jours. Veuillez résuire le temps passé.");
	        	}
	        }


	        if ($post['visible']=="on") $post['visible'] = "oui";
	        else $post['visible'] = "non";

	        if ($post['send_mail']=="on") $post['send_mail'] = "oui";
	        else $post['send_mail'] = "non";

	        // Calcul du nombre de crédit
	        if (!$post['credit_presta']) {
	        	$tmp = explode(":", $post['temps_passe']);

	        	$creditMin = $tmp[1]/60;

	        	$post['credit_presta'] = round($creditMin + $tmp[0],2);
	        }

			if (!$post['id_user']) {
 	        	$post['id_user'] = ATF::$usr->getId();
 	        }	        

	        // Insertion
	        $id = self::insertTS($post);

	        $p = array("id"=>$id);
        	$return['result'] = self::_GET($p);

        	// Traitement de l'id_user
        	if ($return["result"]["id_user"] && !$return["result"]["id_user_fk"]) {
        		$return["result"]["id_user_fk"] = $return["result"]["id_user"];
        		$return["result"]["id_user"] = ATF::user()->nom($return["result"]["id_user"]);
        	}
        	// Récupération des notices créés
        	$return['notices'] = ATF::$msg->getNotices();
	        return $return;
        } catch (errorATF $e) {
        	throw $e;
        } catch (Exception $e) {
        	throw $e;
        }
        return false;
	}	


	/**
	* INSERTION d'une interaction de hotline depuis telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos Les informations de l'interaction
	* @param array $s La session
	* @param array $files Les fichiers uploadés
	* @param boolean $mail True si on désire
	* @param boolean $pointage True si on désire créer le pointage
    * @return boolean true 
    */   
	public function insertTS($infos,$mail = true) {
       	
		/*---------------Fonctionnalité de trace hotline----------------------*/
		if($infos["internal"]){
			log::logger("INTERNAL INTERACTION","hotline");
			$data = array(
				 "duree_presta"=>$infos["duree_presta"]?$infos["duree_presta"]:"00:00"
				,"heure_debut_presta"=>date("h:i")
				,"heure_fin_presta"=>date("h:i")
				,"detail"=>$infos["detail"]
				,"id_user"=>(($infos["id_user"])?$infos["id_user"]:ATF::$usr->getID())
				,"id_hotline"=>$infos["id_hotline"]
				,"visible"=>$infos["visible"]
				,"nature"=>"internal"
			);
			log::logger($data,"hotline");
			return parent::insert($data,$s,$files);
		}
		
		log::logger("INTERACTION NON INTERNAL","hotline");
		log::logger($infos,"hotline");	
		// Début de transaction
		ATF::db($this->db)->begin_transaction();
		
		try {
			// SI pas de data
			if(!$infos){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF(ATF::$usr->trans("aucunes_infos",$this->table),1000);
			}
			// Si pas de texte
			if (!$infos["detail"]){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF(ATF::$usr->trans("joindre_un_texte_explicatif_a_l_interaction",$this->table),1000);
			} 

			// Si les horaire de deplacement ne sont pas présent, on les cale sur les horaires de presta
			if(!$infos["heure_depart_dep"])  $infos["heure_depart_dep"] = $infos["heure_debut_presta"];
			if(!$infos["heure_arrive_dep"])  $infos["heure_arrive_dep"] = $infos["heure_fin_presta"];

			// Check de la durée de prestation
			if((!$infos["duree_presta"] || $infos["duree_presta"]=="00:00" || $infos["duree_presta"] =="0:00")){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF(ATF::$usr->trans("duree_presta_non_renseigne",$this->table),1000);
			} 	

			// On convertit les horaires au format HH:MM en minute pour faire les contrôle
			sscanf($infos["heure_debut_presta"], "%d:%d", $hours, $minutes);
			$debut_presta = $hours * 60 + $minutes;
			sscanf($infos["heure_fin_presta"], "%d:%d", $hours, $minutes);
			$fin_presta = $hours * 60 + $minutes;
			sscanf($infos["heure_depart_dep"], "%d:%d", $hours, $minutes);
			$debut_dep = $hours * 60 + $minutes;
			sscanf($infos["heure_arrive_dep"], "%d:%d", $hours, $minutes);
			$fin_dep = $hours * 60 + $minutes;

			log::logger("debut_presta = ".$debut_presta,"hotline");
			log::logger("fin_presta = ".$fin_presta,"hotline");
			log::logger("debut_dep = ".$debut_dep,"hotline");
			log::logger("fin_dep = ".$fin_dep,"hotline");

			if($debut_dep > $debut_presta){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("L'heure de début de mission est superieure à l'heure de début de prestation !",1000);
			}

			if($fin_presta > $fin_dep){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("L'heure de fin de mission est inferieure à l'heure de fin de prestation !",1000);
			}

			// Calcul durée de presta de base
			$duree_presta = $fin_presta - $debut_presta; // OLD SCHOOL : calcul la durée de presta via les horaires
			// NEW SCHOOL : la durée de presta, c'est tout simplement le temps passé (le chrono)
			sscanf($infos["duree_presta"], "%d:%d:%d", $hours, $minutes, $second);
			$duree_presta = $hours * 60 + $minutes;

			log::logger("duree_presta minutes = ".$duree_presta,"hotline");

			// SI on a mis un temps de pause
			$duree_pause = 0;
			if($infos["duree_pause"]){
				sscanf($infos["duree_pause"], "%d:%d", $hours, $minutes);
				$duree_pause = $hours * 60 + $minutes;
				if($duree_presta < $duree_pause){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF(ATF::$usr->trans("duree_de_pause_superieur_duree_presta",$this->table),1000);
				}
				$duree_presta -= $duree_pause;

			}
			log::logger("duree_pause minutes = ".$duree_pause,"hotline");
			log::logger("duree_presta minutes moins la pause = ".$duree_presta,"hotline");

			// Check de la durée de prestation, si négatif alors on a inverser les horaires
			if($duree_presta < 0){			
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF(ATF::$usr->trans("heure_debut_presta_sup_heure_fin",$this->table),1000);
			} 
			
			// Calcul du nombre de ticket pour l'interaction (via la durée de presta ou on a déjà enlevé la pause)
			$p = array('tps'=>util::convertToHoursMinute($duree_presta));
			$ticket_presta = $this->_credit($p);
			log::logger("ticket_presta = ".$ticket_presta,"hotline");

			$duree_dep = 0;
			if($infos["duree_dep"] && ($infos["duree_dep"] !=="00:00" && $infos["duree_dep"] !=="0:00" && $infos["duree_dep"] !=="00:00:00")){			

				sscanf($infos["duree_dep"], "%d:%d:%d", $hours, $minutes, $second);
				$duree_dep = $hours * 60 + $minutes;
			}
			log::logger("duree_dep minute = ".$duree_dep,"hotline");


			$p = array('tps'=>util::convertToHoursMinute($duree_dep),"field"=>"credit_dep");
			$ticket_dep = $this->_credit($p);
			log::logger("ticket_dep = ".$ticket_dep,"hotline");

		
			/*---------------Gestion de l'ordre de mission----------------------*/
			if($infos["id_ordre_de_mission"]) {
				log::logger("ODM = ".$infos["id_ordre_de_mission"],"hotline");
				ATF::ordre_de_mission()->update(array("id_ordre_de_mission"=>$infos["id_ordre_de_mission"],"etat"=>"termine"));
			}

			$hotline = ATF::hotline()->select($infos["id_hotline"]);
			$id_societe = $hotline["id_societe"];
			log::logger("TH infos = ","hotline");
			log::logger($hotline,"hotline");

			/*---------------Gestion de l'état de la requête----------------------*/
			// 1ère contrainte : Changement de l'état suite à la mise en attente du client ($infos["etat_wait"]=true)
			// 2ème contrainte : Requête en état wait/fixing uniquement (les requêtes terminées ou non prises en charge ne peuvent pas avoir de changement d'état)
			// 3ème contrainte : Les requêtes en état wait passent en état fixing lors d'une interaction
			// 4ème contrainte : Seulement si la facturation est validée ou qu'il n'y a pas de facturation
			if($hotline["etat"]=="fixing" || $hotline["etat"]=="wait"){
				if($infos["etat_wait"]=="oui" || ($hotline["facturation_ticket"]=="oui" && !$hotline["ok_facturation"]) || ($hotline["facturation_ticket"]=="oui" && $hotline["ok_facturation"]=="non")){
					log::logger("TH mise en attente","hotline");
					ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"etat"=>"wait","disabledInternalInteraction"=>true));
				}else{
					log::logger("TH mise en fixing","hotline");
					ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"etat"=>"fixing","disabledInternalInteraction"=>true));
				}
				/*---------------Gestion de la mise en attente de MEP one shot----------------------*/
				if ($infos['mep_mail']=="oui") {
					log::logger("TH mise en attente de MEP","hotline");
					ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"wait_mep"=>"oui","etat"=>"fixing","disabledInternalInteraction"=>true));
				}
			}
			
			
			/*---------------Cas de le requête non encore validée----------------------*/
			// Lorsque la requête n'est pas encore validée il ne faut pas passer celle-ci en fixing !
			if($hotline["facturation_ticket"]=="oui" && !$hotline["ok_facturation"] || $hotline["facturation_ticket"]=="oui" && $hotline["ok_facturation"]=="non"){
				log::logger("On garde le statut en attente du ticket car la factu n'est pas accepté","hotline");
				ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"etat"=>"wait","disabledInternalInteraction"=>true));
			}	
			
			/*---------------Insertion/Maj de l'interaction----------------------*/
			//Patch pour bloquer les interactions en mode non-visible
			if($hotline["visible"]=="non")  $infos["visible"]="non";
			$data = array(
				// "temps_passe"=>$infos["temps_passe"]
				//,"temps"=>$infos["temps"]
				 "heure_debut_presta"=>$infos["heure_debut_presta"]
				,"heure_fin_presta"=>$infos["heure_fin_presta"]
				,"duree_presta"=>$infos["duree_presta"]
				,"duree_pause"=>$infos["duree_pause"]
				,"heure_depart_dep"=>$infos["heure_depart_dep"]
				,"heure_arrive_dep"=>$infos["heure_arrive_dep"]
				,"duree_dep"=>$infos["duree_dep"]
				,"credit_dep"=>$infos["credit_dep"]
				,"credit_presta"=>$infos["credit_presta"]
				,"date"=>$infos["date"]
				,"detail"=>$infos["detail"]
				,"id_user"=>(($infos["id_user"])?$infos["id_user"]:ATF::$usr->getID())
				,"id_contact"=>$infos["id_contact"]
	            ,"id_hotline"=>$infos["id_hotline"]
	            ,"visible"=>$infos["visible"]
	            ,"nature"=>$infos["nature"]
				,"id_ordre_de_mission"=>((isset($infos["id_ordre_de_mission"]))?$infos["id_ordre_de_mission"]:NULL)
			);


			// Pas de file si pas de files !
			if($infos["filestoattach"]) $data["filestoattach"]=$infos["filestoattach"];
			
			//Petit message pour le transfert
			if($infos["transfert"]){
				$data["detail"]="Requête transférée par ".ATF::user()->nom($infos["id_user"])." à ".ATF::user()->nom($infos["transfert"])."<br />".$data["detail"];
			}elseif($infos['transfert_pole']){
				$data["detail"]="Requête transférée par ".ATF::user()->nom($infos["id_user"])." au pôle ".$infos["transfert_pole"]."<br />".$data["detail"];
			}
			

			//Gestion de la mise à jour (update)
			if($infos["update"]){
				$id_hotline_interaction = $data["id_hotline_interaction"] = $this->decryptId($infos["id_hotline_interaction"]);
				
				log::logger("UPDATE INTERACTION","hotline");
				log::logger($data,"hotline");
				parent::update($data,$s,$files);
			}else{		    
				log::logger("INSERT INTERACTION","hotline");
				log::logger($data,"hotline");
				$id_hotline_interaction = parent::insert($data,$s,$files);
			}

			// GESTION DES ALERTES 
			/*log::logger($ticket_presta." > ".$infos["credit_presta"],"hotline");
			log::logger($ticket_dep." > ".$infos["credit_dep"],"hotline");
			if( ($ticket_presta > $infos["credit_presta"]) || ($ticket_dep > $infos["credit_dep"])){
				log::logger("ALERTE POUR SOUS FACTURATION","hotline");

				if(!$infos["champ_alerte"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF("Merci de saisir votre justification !",1020);
				} 
				$alerte = array(
					"alerte"=>$infos["champ_alerte"]
					,"id_user"=>ATF::$usr->getID()
					,"id_hotline"=>$infos["id_hotline"]
					,"id_hotline_interaction"=>$id_hotline_interaction
				);
				if ($infos['id_alerte']) {
					$alerte['id_alerte'] = $infos['id_alerte'];
					log::logger("UPDATE ALERTE","hotline");
					log::logger($alerte,"hotline");
					ATF::alerte()->u($alerte);
				} else {
					log::logger("INSERT ALERTE","hotline");
					log::logger($alerte,"hotline");
					ATF::alerte()->i($alerte);					
				}

			}*/


			//L'interaction fait mention de materiel a facturer
			//On notifie Emma
			if($infos["matos"] == "oui") {
				$alerte_matos = array(
					"id_hotline"=>$infos["id_hotline"],
					"id_hotline_interaction"=>$id_hotline_interaction,
					"alerte"=>$infos["detail"],  
					"id_user"=>ATF::$usr->getID(), 
					"nature"=>"materiel"
				);
				log::logger("INSERT ALERTE POUR MATOS","hotline");
				log::logger($alerte_matos,"hotline");
				ATF::alerte()->insert($alerte_matos);
			}

			
			// Calcul du total temps de la prestation : deplacement + presta + pause (la pause a été enlevé de la presta plus haut)
			$total_presta = $duree_presta + $duree_pause + $duree_dep;
			log::logger("Total presta pour le pointage = ".$total_presta." = ".$duree_presta." + ".$duree_pause." + ".$duree_dep,"hotline");

			/*---------------Gestion de la feuille de pointage----------------------*/
			//Si on ne trouve rien c'est que la ligne de pointage n'est pas créée, exemple :
			//On créer une interaction sur un tickets qui n'est pas a notre charge.
			//On créer une interaction sur un ticket qu'on a pris en charge côté nebula.
			log::logger("INSERT POINTAGE = ".util::convertToHoursMinute($total_presta),"hotline");
			ATF::pointage()->addPointage($infos["id_hotline"],$id_hotline_interaction,$infos["id_user"],$infos["date"],util::convertToHoursMinute($total_presta));
						
			/*---------------Transfert à l'utilisateur----------------------*/

			if ($infos["transfert"]) {
				log::logger("TRANSFERT USER","hotline");
				//Récupération du pôle de l'utilisateur
				$pole=explode(",",ATF::user()->select($infos["transfert"],"pole"));
				$h2update = array(
					"id_hotline"=>$infos["id_hotline"]
					,"id_user"=>$infos["transfert"]
					,"pole_concerne"=>((is_array($pole) && isset($pole[0]) && !empty($pole[0]))?$pole[0]:"dev")
					,"disabledInternalInteraction"=>true
				);
				log::logger($h2update,"hotline");
				ATF::hotline()->update($h2update);
				//Mise à jour de l'état
				log::logger("Mise a jour de l'état en fixing","hotline");
				ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"etat"=>"fixing","disabledInternalInteraction"=>true));
				//Récupération de l'email du nouvel utilisateur en charge
				$email=ATF::user()->select($infos["transfert"],"email");
				
				ATF::hotline_mail()->createMailUserTransfert($hotline["id_hotline"],$id_hotline_interaction,$email);
				ATF::hotline_mail()->sendMail();
				
				//Notice
				ATF::hotline()->createMailNotice("hotline_transfert_user");
			}
			
			/*---------------Transfert de Pôle----------------------*/
			if($infos['transfert_pole']){
				log::logger("TRANSFERT POLE","hotline");
				$h2update = array(
					"id_hotline"=>$infos["id_hotline"],
					"pole_concerne"=>$infos["transfert_pole"],
					"disabledInternalInteraction"=>true,
					"id_user"=>$infos["transfert"]?$infos["transfert"]:NULL
				);
				log::logger($h2update,"hotline");
				ATF::hotline()->update($h2update);
				//Récupération de l'email du nouveau utilisateur en charge
				$email="hotline.".$infos["transfert_pole"]."@absystech.fr";
			
				ATF::hotline_mail()->createMailPoleTransfert($hotline["id_hotline"],$id_hotline_interaction,$email);
				ATF::hotline_mail()->sendMail();
				
				//Notice
				ATF::hotline()->createMailNotice("hotline_transfert_pole");
			}
			
			/*---------------Gestion de l'envoi de mail----------------------*/
			if($mail && $infos["send_mail"]=="oui" && $infos["visible"]=="oui"){
				log::logger("SEND MAIL CLIENT","hotline");
				
				ATF::hotline_mail()->createMailInteraction($hotline["id_hotline"],$id_hotline_interaction,$infos["filestoattach"]["fichier_joint"],$infos["anotherNotify"],$infos['mep_mail']);	
			
				if($infos["filestoattach"]["fichier_joint"]){
					//Ajout du fichier joint
					$path = $this->filepath($id_hotline_interaction,"fichier_joint");	
					$mail=ATF::hotline_mail()->getCurrentMail();
					$mail->addFile($path,"fichier_joint.zip",true);	
				}
									
				ATF::hotline_mail()->sendMail();
				//Notice
				ATF::hotline()->createMailNotice("hotline_interaction_mail_to_contact");

			} else {
				log::logger("PAS D'ENVOI DE MAIL AU CLIENT ".$mail." && ".$infos["send_mail"]." && ".$infos["visible"],"hotline");
			}
			
			//Envoi d'un mail en interne
			//Recherche des intervenant ayant travaillé sur la hotline
			$inters=$this->ss("id_hotline",$infos["id_hotline"],"id_user");	
			$team=array();
			log::logger("RECHERCHE DES INTERVENANT DU TICKET","hotline");
			foreach($inters as $inter){
				if(!empty($inter["id_user"]) && ATF::$usr->getId()!=$inter["id_user"] && !in_array($inter["id_user"])){
					array_push($team,ATF::user()->select($inter["id_user"],"email"));
				}
			}
			log::logger($team,"hotline");

			//Ajout des actifs selectionné
			$lesactifs = is_array($infos["actifNotify"])?$infos["actifNotify"]:explode(",",$infos["actifNotify"]);
			foreach($lesactifs as $actif){
				if (!empty($actif)) {
					array_push($team,ATF::user()->select($actif,"email"));
				}
			}
			log::logger("APRES FILTRAGE DES ACTIFS","hotline");
			log::logger($team,"hotline");
		
			if(count($team)>0){
				$team = array_flip(array_flip($team)); // Suppression des doublons
				
				ATF::hotline_mail()->createMailInteractionInternal(implode(",",$team),$hotline["id_hotline"],$id_hotline_interaction,$infos["filestoattach"]["fichier_joint"]);
				if($infos["filestoattach"]["fichier_joint"]){
					//Ajout du fichier joint
					$path = $this->filepath($id_hotline_interaction,"fichier_joint");	
					$mail=ATF::hotline_mail()->getCurrentMail();
					$mail->addFile($path,"fichier_joint.zip",true);	
				}
				ATF::hotline_mail()->sendMail();
				//Notice
				ATF::hotline()->createMailNotice("hotline_interaction_mail_to_users");
			}
					
			/*---------------Mise à jour de la date de modification hotline----------------------*/
			log::logger("MAJ TH DATE DE MODIFICATION ".date("Y-m-d H:i:s"),"hotline");
			ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"date_modification"=>date("Y-m-d H:i:s"),"disabledInternalInteraction"=>true));
			
			/*--------------- Notice----------------------*/
			ATF::hotline()->createNotice("hotline_interaction_done");
		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF($e->getMessage(),1000);
		}

		
		if(ATF::db($this->db)->commit_transaction()){

			api::sendUDP(array("data"=>array("type"=>"interaction")));
		}


		return $id_hotline_interaction;		
	}

	/**
	* Permet de modifier une interaction hotline depuis telescope
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr> 
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array COntient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/ 
	public function _PUT($get,$post) {
        $input = file_get_contents('php://input');
        if (!empty($input)) parse_str($input,$post);

    	$return = array();

        try {
        	
	        if (!$post) throw new Exception("POST_DATA_MISSING",1000);
	        // Check des champs obligatoire
	        if (!$post['id_hotline_interaction']) throw new Exception("ID_HOTLINE_INTERACTION_MISSING",1103);
	        if (!$post['id_hotline']) throw new Exception("ID_HOTLINE_MISSING",1100);
	        if (!$post['detail'] || $post['detail']=="<p><br></p>") throw new Exception("CONTENT_MISSING",1101);
	        if (!$post['temps_passe'] || $post['temps_passe']=="00:00:00") throw new Exception("TEMPS_PASSE_MISSING",1102);

	        // Mapping pour BDD Optima
	        $tps = substr($post['temps_passe'],0,5);
	        if ($tps == "00:00") {
	        	$tps = "00:01";
	        }
	        $post['temps_passe'] = $post['duree_presta'] = $tps;

	        if (!$post['heure_debut_presta'] || !$post['heure_fin_presta']) {
	        	// On créer un date time
	        	$date = new DateTime();
	        	// On stock la date car c'est la date de fin
	        	$dayEnd = $date->format('d');
	        	$post['heure_fin_presta'] = $date->format('H:i:s');
	        	// On initialise l'interval a soustraire grace au temps passé
	        	$tosub = new DateInterval("PT".str_replace(":", "H", $tps)."M");
	        	$date->sub($tosub);
	        	$post['heure_debut_presta'] = $date->format('H:i:s');
	        	$dayBegin = $date->format('d');

	        	if ($dayEnd != $dayBegin) {
	        		throw new errorATF("Impossible d'enregistrer l'interaction car elle chevauche deux jours. Veuillez ajuster les horaires.",1000);
	        	}
	        }

	        if ($post['visible']=="on") $post['visible'] = "oui";
	        else $post['visible'] = "non";

	        if ($post['send_mail']=="on") $post['send_mail'] = "oui";
	        else $post['send_mail'] = "non";

	        // Calcul du nombre de crédit
	        if (!$post['credit_presta']) {
	        	$tmp = explode(":", $post['temps_passe']);

	        	$creditMin = $tmp[1]/60;

	        	$post['credit_presta'] = round($creditMin + $tmp[0],2);
	        }

			if (!$post['id_user']) {
 	        	$post['id_user'] = ATF::$usr->getId();
 	        }

	        // Modification
	        $post['update'] = true;
	        self::insertTS($post);
	        $p = array("id"=>$post['id_hotline_interaction']);
        	$return['result'] = self::_GET($p);

        	// Traitement de l'id_user
        	// if ($return["result"]["id_user"] && !$return["result"]["id_user_fk"]) {
        	// 	$return["result"]["id_user_fk"] = $return["result"]["id_user"];
        	// 	$return["result"]["id_user"] = ATF::user()->nom($return["result"]["id_user"]);
        	// }
        	// Récupération des notices créés
        	$return['notices'] = ATF::$msg->getNotices();
	        return $return;
        } catch (errorATF $e) {
        	throw $e;
        } catch (Exception $e) {
        	throw $e;
        }
        return false;
	}	

	/**
	* Renvoi le nombre de crédit par rapport aun temps passé
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr> 
	* @param $get array contient le temps passé formatté comme suit : HH:ii:ss ou HH:ii
	* @param $post array vide
	* @return float le nombre de crédit formaté sur 3 chiffres après la virgule
	*/ 
	public function _credit($get,$post) {
		if (!$get['tps'] || $get['tps']=="00:00:00") return 0;

 		if ($get['field']=="credit_dep") {
 			if ($val = ATF::hotline()->estAuForfait($get)) return round($val,2);
 		}
    	$tmp = explode(":", $get['tps']);

    	$creditMin = $tmp[1]/60;

    	return round($creditMin + $tmp[0],2);
	}

	/**
	* Renvoi le nombre de crédit pour le déplacement par rapport aux horaires saisis
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr> 
	* @param $get array contient les temps passé formattés comme suit : HH:ii:ss ou HH:ii
	* @param $post array vide
	* @return float le nombre de crédit formaté sur 3 chiffres après la virgule
	*/ 
	public function _credit_dep($get,$post) {
		if ($val = ATF::hotline()->estAuForfait($get)) {
			return $val;
		} else {
			return $this->_credit($get);
		}
	}



	public function _indicateurs($get, $post){
	
		$workedDay = (ATF::hotline()->getJoursOuvres(date("Y-m-01"), date("Y-m-d")))*ATF::user()->select(ATF::$usr->getID(), "temps_partiel");
		
		
		//Compte le nombre d'heures passees sur un mois
		$mois = ATF::pointage()->totalHeure(date("Y-m"),ATF::$usr->getID());
		$today = ATF::pointage()->totalHeure(date("Y-m-d"),ATF::$usr->getID());
		
		$todayText = $today;
		$moisTxt = $mois;

		if($today == NULL){ $today = 0; $todayText="0h";
		}else{ 
			$d = explode("h", $mois);
			$today = number_format(intval($d[0])+(60/intval($d[1]))-1,0);  
		}
		
		if($mois == NULL){ $mois = 0; $moisTxt="0h";
		}else{
			$m = explode("h", $mois);
			$mois = number_format(intval($m[0])+(60/intval($m[1]))-1,0); 			
		}

		return array("today"=>$today , "mois"=>$mois, "todayText"=>$todayText, "moisTxt"=>$moisTxt,"totalMois"=>$workedDay);

	}


	public function _getMoyennePointage($get, $post){
		$date = date('Y-m', strtotime('-1 month'));

		if ($get['id']) {
			$diviseur = ATF::db()->ffc(
				"SELECT count(DISTINCT DATE_FORMAT(date, '%Y-%m-%d'))
				FROM hotline_interaction
				WHERE DATE_FORMAT(date, '%Y-%m') = '".$date."'
				AND id_user=".ATF::$usr->getID()
			);
		}
		else {
			$diviseur = " count(DISTINCT DATE_FORMAT(date, '%Y-%m-%d'))";
		}
		$r = array();
		$q = "SELECT SUM(TIME_TO_SEC(duree_presta)-TIME_TO_SEC(duree_pause))/";
		$q .= $diviseur;

		$q.=" FROM `hotline_interaction`
			WHERE DATE_FORMAT(date, '%Y-%m') = '".$date."'";
		if ($get['id']) $q .= " AND id_user=".ATF::$usr->getID();

		$date2 = date('Y-m-d', strtotime('-3 days'));
		$q2 = "SELECT
			SUM(TIME_TO_SEC(duree_presta)-TIME_TO_SEC(duree_pause))
			FROM `hotline_interaction`
			WHERE DATE_FORMAT(date, '%Y-%m-%d') = '".$date2."'";
		if ($get['id']) $q2 .= " AND id_user=".ATF::$usr->getID();

		$r['global'] = ATF::db()->ffc($q);
		$r['today'] = ATF::db()->ffc($q2);

		return $r;
	}

}
