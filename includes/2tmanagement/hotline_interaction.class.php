<?
/**
* Classe Hotline
* @package Optima
* @subpackage 2T Management
*/
require_once dirname(__FILE__)."/../absystech/hotline_interaction.class.php";

class hotline_interaction_2tmanagement extends hotline_interaction {
	/**
	* Contructeur par défaut !
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "hotline_interaction";

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

		ATF::hotline_interaction()->q->where("hotline_interaction.id_user",33,"OR","usr","=") // Guirec
									->where("hotline_interaction.id_user",47,"OR","usr","="); //Laurent

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


}
