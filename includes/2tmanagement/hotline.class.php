<?
/**
* Classe Hotline
* @package Optima
* @subpackage 2tManagement
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
			,"teamviewer"=>array("xtype"=>"switchbutton","default"=>"non")
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
			,"teamviewer"=>$infos["teamviewer"]
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

		$interaction=$this->select($id_hotline_interaction);


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

	/*
	* @codeCoverageIgnore
	*/
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
	* @codeCoverageIgnore
	*/
	public function getBillingTime($id_hotline_interaction){
		return $this->getTime($id_hotline_interaction,"temps");
	}

	/**
	* Donne le temps PASSE sur une interactiion
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_hotline_interaction l'id hotline_interaction correspondant
	* @return int le temps total travaillé en base 10. 1 = 1 heure
	* @codeCoverageIgnore
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



	/**
	 * Permet de définir le mode de facturation.
	 * @author Quentin JANON <qjanon@absystech.fr>
	 *
	*/
	public function setbillingModeNew($infos,$s,$f,$cadre_refresh) {
		$this->infoCollapse($infos);

		// Mode transactionel
		ATF::db($this->db)->begin_transaction();

		$hotline = array(
			"id_hotline"=>$this->decryptId($infos["id_hotline"])
			,"charge"=>$infos["charge"]
		);

		//Est-ce qu'il y a un utilisateur en charge ?
		$id_user = $this->select($this->decryptId($infos["id_hotline"]),"id_user");

		switch($infos["type_requete"]){
			case "charge_absystech":
				$hotline["facturation_ticket"]="non";
				if($id_user) $hotline["etat"]="fixing";
				$hotline["ok_facturation"]=NULL;
				$hotline["id_affaire"]=NULL;
				$chargeText="Charge AbsysTech";
				break;
			case "charge_client":
				$hotline["facturation_ticket"]="oui";
				$hotline["etat"]="wait";
				$hotline["ok_facturation"]=NULL;
				$hotline["id_affaire"]=NULL;
				$chargeText="Charge Client";
				break;
			case "affaire":
				if(!$infos["id_affaire"]) throw new errorATF('null_id_affaire');
				$hotline["facturation_ticket"]="non";
				if($id_user) $hotline["etat"]="fixing";
				$hotline["ok_facturation"]=NULL;
				$hotline["id_affaire"]=$this->decryptId($infos["id_affaire"]);
				$chargeText="Sur une affaire";
				break;
		}

		parent::update($hotline,$s);

		$hotline = $this->select($infos["id_hotline"]);

		/*Envoi du mail*/
		if (($infos["send_mail"]=="true" || $infos["relance"]) && $hotline["visible"]=="oui"){
			if (ATF::hotline_mail()->createMailBilling($hotline["id_hotline"])) {
				ATF::hotline_mail()->sendMail();

				//Notice mail envoyé
				if($infos['relance']){
					$this->createMailNotice("hotline_relance_facturation");
				}else{
					$this->createMailNotice("hotline_mail_facturation");
				}
			}

		}

		//Insère une interaction d'information
		if(!$infos["relance"] && !$infos["disabledInternalInteraction"]){
			//Trace dans les interactions
			$u = $infos['id_user']?$infos['id_user']:ATF::$usr->getId();
			$this->createInternalInteraction($infos["id_hotline"],"Choix de la facturation \"".$chargeText."\" par ".ATF::user()->nom($u));
		}

		//Notice
		$this->createNotice("hotline_billing_set");

		//Commit !
		ATF::db($this->db)->commit_transaction();

		//Raffraichissement
		if(is_array($cadre_refresh) || isset($infos["refresh"])){
			ATF::$cr->block("top");
			$this->redirection("select",$hotline["id_hotline"]);
		}

		return true;
	}

	/**
	 * Renvoi le mode de facturation paramétré
	 * @author Quentin JANON <qjanon@absystech.fr>
	 *
	*/
	public function getBillingMode($id_hotline,$string=false) {


		if (is_array($id_hotline)) $hotline = $id_hotline;
		else $hotline = $this->select($id_hotline);

		$r[] = ATF::$usr->trans($hotline['charge'],"hotline");
		switch ($hotline['charge']) {
			case "maintenance":
				$r[] = ATF::affaire()->nom($hotline['id_affaire']);
			break;
			case "intervention":
				if (!$hotline['facturation_ticket']) {
					$r = array("Nature de la charge à définir");
				} else if ($hotline['id_affaire']) {
					$r[] = " sur l'affaire : ".ATF::affaire()->nom($hotline['id_affaire']);
					$id_societe_affaire = ATF::affaire()->select($hotline['id_affaire'],"id_societe");
					if ($hotline['id_societe'] != $id_societe_affaire) {
						$r[] = " (".ATF::societe()->nom($id_societe_affaire).")";
					}
				} else {
					$r[] = ATF::$usr->trans($hotline['facturation_ticket']."_facture","hotline");
				}
			break;

		}
		return $string?implode(" ",$r):$r;
	}


	public function getModeFacturation($infos){
		if(!$infos["id_hotline"]){
			if($infos["id_hotline_interaction"]) $infos["id_hotline"] = ATF::hotline_interaction()->select(ATF::hotline_interaction()->decryptId($infos["id_hotline_interaction"]), "id_hotline");
		}

		if ($infos["id_hotline"]) {
			$hotline = $this->select($infos["id_hotline"]);
			if($hotline["charge"] == "intervention" && $hotline["facturation_ticket"] == "oui"){
				return true;
			}
		}
		return false;
	}

	public function estAuForfait($infos){
		if(!$infos["id_hotline"]){
			if($infos["id_hotline_interaction"]) $infos["id_hotline"] = ATF::hotline_interaction()->select(ATF::hotline_interaction()->decryptId($infos["id_hotline_interaction"]), "id_hotline");
		}

		if ($infos["id_hotline"]) {
			return ATF::societe()->select($this->select($infos["id_hotline"] , "id_societe")  , "forfait_dep");
		}
		return 0;
	}


	/**
	* Permet l'affichage dynamique des photos
	* @author Caroline MOREL <cmorel@absystech.fr>
	*
	*/
	public function dynamicPicture($id_hotline) {
		if (!$id_hotline) {
			return false;
		}
		$id_hotline_crypted = $id_hotline;
		$id_hotline = $this->decryptId($id_hotline);

		$path = $this->filepath($id_hotline,"fichier_joint");
		$path2extract = dirname($path)."/";

		$mappingFiletype = array(
			//"pdf"=>"pdfB.png",
			"indd"=>"indesign.png",
			"psd"=>"photoshop.png",
			"tiff"=>"photoshop.png",
			"ai"=>"illustrator.png",
			"doc"=>"word.png",
			"docx"=>"word.png",
			"eml"=>"mail.png",
			"odt"=>"word.png",
			"xls"=>"xls2.png",
			"xlsx"=>"xls2.png",
			"ods"=>"xls2.png",
			"ppt"=>"ppt.png",
			"pptx"=>"ppt.png",
			"txt"=>"txt.png"
		);

		//Dézippage
		$zip = new ZipArchive();
		$zip->open($path);

		for($i = 0; $i < $zip->numFiles; $i++){

			$infos_fichier = $zip->statIndex($i);

			//$name = utf8_decode($infos_fichier['name']);
			$name = $infos_fichier['name'];

			$extension=strtolower(substr(strrchr($name,".") ,1));

			$size=util::formatBytes($infos_fichier['size']);
			$array[$i] = array(
				"name"=>utf8_encode($name),
				"size"=>$size,
				"type"=>$extension,
			);

			$zip->extractTo($path2extract,$name);

			if ($extension =="jpg" || $extension =="png" || $extension =="gif") {
				$filename = "image";
				$array[$i]['URL'] = __MANUAL_WEB_PATH__.$this->table."-".$id_hotline_crypted."-".$filename.$i."-200-50.".$extension."?v=".rand(0,10000);
				$array[$i]['URLHD'] = __MANUAL_WEB_PATH__.$this->table."-".$id_hotline_crypted."-".$filename.$i."-800-600.".$extension."?v=".rand(0,10000);

			} elseif ($extension =="pdf") {
				// Nom du document final
				$filename = "dldoc";
				// Filename du fichier a convertir
				$fn = $path2extract.$name;
				// Chemin vers la miniature
				$previewFn = $path2extract.$id_hotline.".previewPDF".$i;

				//execute imageMagick's 'convert', setting the color space to RGB
				//This will create a jpg having the widthg of 200PX
				$cmd = "convert \"{$fn}[0]\" -colorspace RGB -geometry 200 ".$previewFn.".png";
				exec($cmd);


				// Renommer l'image créée par le convert pour lui soustraire son extension
				util::rename($previewFn.".png",$previewFn);

				// On prépare nos URL de vignette et de DL
				$array[$i]['URL'] = __MANUAL_WEB_PATH__.$this->table."-".$id_hotline_crypted."-previewPDF".$i."-200-50.png";
				$array[$i]['URLDL'] = __MANUAL_WEB_PATH__.$this->table."-select-".$filename.$i."-".$id_hotline_crypted."-".$extension.".dl";

			} else {
				$filename = "dldoc";
				$array[$i]['URL'] = ATF::$staticserver.'images/icones/'.$mappingFiletype[$extension];
				$array[$i]['URLDL'] = __MANUAL_WEB_PATH__.$this->table."-select-".$filename.$i."-".$id_hotline_crypted."-".$extension.".dl";
			}

			// Ici on renomme les fichiers extrait avec leur vrai nom par le nom qu'on leur attribut
			rename($path2extract.$name,$path2extract.$id_hotline.".".$filename.$i);

		}

		$zip->close();
		return $array;

	}

	public function dl($infos,&$s,$files,&$cadre_refreshed) {
		if (preg_match("#dldoc#",$infos['field']) && $infos['type']) {
			$target = $this->filepath($infos["id_".$this->table],$infos["field"],$infos["temp"]);
			$size = filesize($target);
			header("Pragma: public");
			header("Expires: 0");
			//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: no-cache");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");


			header("Content-Type: application/".$infos['type']);

			header("Content-Disposition: attachment; filename=\"HotlinePJ.".$infos['type']."\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$size);

			readfile($target);

		} else {
			parent::dl($infos,$s,$files,$cadre_refreshed);
		}
	}

	/**
	* Indique via un BOOL s'il y a des ticket en attente de mise en production ou pas
	* @author Quentin JANON <qjanon@absystech.fr>
	*
	*/
	public function isThereMEPTicket() {
		$c = count($this->getMEPTicket());

		return $c?true:'false';
	}

	/**
	* Renvoi tous les tickets en attente de mise en production
	* @author Quentin JANON <qjanon@absystech.fr>
	*
	*/
	public function getMEPTicket() {
		$this->q->reset()->where("wait_mep","oui");
		return $this->sa();
	}

	/**
	* Valide massivement les MEP des tickets hotlines
	* @author Quentin JANON <qjanon@absystech.fr>
	*
	*/
	public function massValidMEP($infos) {

		//Commit
		ATF::db($this->db)->begin_transaction();

		foreach ($infos['th'] as $k=>$i) {
			$hotline = array(
				"id_hotline"=>$k
				,"wait_mep"=>"non"
			);

			parent::update($hotline,$s);
			//Envoi d'un mail au chef de projet !
			ATF::hotline_mail()->createMailMep($k);
			ATF::hotline_mail()->sendMail();

			//Notice mail envoyé
			$this->createMailNotice("hotline_mail_mise_prod");

			//Trace dans les interactions
			$this->createInternalInteraction($k,"Mis en prod par ".ATF::user()->nom(ATF::$usr->getId()));

		}

		//Commit
		ATF::db($this->db)->commit_transaction();

		$this->redirection("select_all");
		return true;
	}

	/**
	* Accepte automatiquement la facturation
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function acceptationAutomatique($delai = 7) {
		ATF::hotline()->q->reset()->where("etat", "done","AND",false,"!=")
								  ->where("etat", "payee","AND",false,"!=")
								  ->where("etat", "annulee","AND",false,"!=")
								  ->where("facturation_ticket", "oui")
								  ->whereIsNull("ok_facturation");
		$res = ATF::hotline()->select_all();

		foreach ($res as $key => $value) {
			ATF::hotline_interaction()->q->reset()->where("id_hotline",$value["id_hotline"])
												  ->where("detail", 'Choix de la facturation "Charge Client"%',"AND", false, "LIKE")
												  ->setLimit(1)
												  ->addOrder("date", "desc");
			$facturation = ATF::hotline_interaction()->select_row();
			$date_facturation = date("Y-m-d", strtotime($facturation["date"]));

			ATF::hotline_interaction()->q->reset()->whereIsNotNull("id_contact")
												  ->where("id_hotline",$value["id_hotline"])
												  ->where("date", $date_facturation." 00:00:00", "AND", false, ">=")
												  ->setLimit(1)
												  ->addOrder("date", "desc");
			$inter_client = ATF::hotline_interaction()->select_row();

			if($inter_client){ $date = date("Y-m-d", strtotime($inter_client["date"])); }
			else{ $date = $date_facturation; }

			if(date("Y-m-d") > date("Y-m-d",  strtotime(date("Y-m-d", strtotime($date)) . " +".$delai." day"))){
				//On accepte la facturation auto
				$detail = "Acceptation AUTOMATIQUE de la facturation de la requête en fonction du temps passé au tarif de 1 crédit/heure (après délai de ".$delai." jours francs sans réponse)";

				// Modification du ticket
				$h = array(
					"id_hotline"=>$value['id_hotline'],
					"ok_facturation"=>"oui",
					"disabledInternalInteraction"=>true
				);
				ATF::hotline()->update($h);

				// Insertion de l'interaction
				$i = array(
					"id_hotline"=>$value["id_hotline"],
					"detail"=>$detail,
					"id_contact"=>$value["id_contact"]
				);
				$id = ATF::hotline_interaction()->i($i);

				 if (ATF::hotline()->select($value['id_hotline'],"etat")=="wait") {
					$h = array("id_hotline"=>$value['id_hotline'],"etat"=>"fixing");
					ATF::hotline()->u($h);
				}
			}

		}
	}

	/**
	* Permet de récupérer la liste des tickets hotline en cours pour snap stat
	* @package Telescope
	* @author Anthony LAHLAH <qjanon@absystech.fr>
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	public function getTotalHotlineEnCours($get,$post) {
		$date = date('Y-m-d',strtotime('- 1 day'));
		$data  = array();

		$q = "SELECT COUNT(*) as en_cours FROM hotline WHERE etat = 'fixing' OR etat = 'wait' OR etat = 'free'";

		$data['total'] = ATF::db()->ffc($q);

		$qC = "SELECT COUNT(*) as cloture FROM hotline WHERE DATE_FORMAT(date_fin, '%Y-%m-%d') ='".$date."'";
		$data['cloture'] = ATF::db()->ffc($qC);

		return json_encode($data);
	}


	/**
	* Permet de récupérer la liste des tickets hotline pour chaque user en cours pour snap stat
	* @package Telescope
	* @author Anthony LAHLAH <qjanon@absystech.fr>
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	public function getTotalHotlineEnCoursPerso($get,$post) {
		$date = date('Y-m-d',strtotime('- 1 day'));
		$data  = array("total"=>array());

		$q = "SELECT COUNT(*) as en_cours FROM hotline WHERE etat = 'fixing' OR etat = 'wait' OR etat = 'free'";

		$data['total'] = ATF::db()->ffc($q);

		$q_all_id = "SELECT id_user FROM user WHERE etat = 'normal'";
		$all_id = ATF::db()->sql2array($q_all_id);

		foreach ($all_id as $key => $val) {
			$q_id = "SELECT COUNT(*) as en_cours FROM hotline WHERE (etat = 'fixing' OR etat = 'wait' OR etat = 'free') AND id_user = '".$val['id_user']."'";
			$data[$val['id_user']] = ATF::db()->ffc($q_id);
		}


		return json_encode($data);
	}

	public function getSatisfaction($get,$post) {
		$date = date('Y-m',strtotime('- 30 day'));

		$q = "SELECT AVG(indice_satisfaction) as satisfaction FROM hotline WHERE DATE_FORMAT(date, '%Y-%m') >='".$date."' AND indice_satisfaction IS NOT NULL GROUP BY YEAR(date), MONTH(date)";

		return ATF::db()->ffc($q);

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
};

?>