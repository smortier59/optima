<?
/**
* Classe Hotline
* @package Optima
* @subpackage AbsysTech
*/
class hotline extends classes_optima {
	/**
	* Constructeur hotline - Créé le singleton d'accès au module hotline
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;

		//Colonnes SELECT ALL
		$this->colonnes['fields_column'] = array(
			"id_hotline"=>array("custom"=>true,"width"=>50,"fixedWidth"=>true) // Obligé de faire un alias différent de hotline.id_hotline parce que sinon problème des id_hotline trouvé sur "la loupe" des listings
			,'date_last_interaction'=>array("custom"=>true,"renderer"=>"datefield","width"=>100,"fixedWidth"=>true)
			,'hotline.id_societe'
			,'hotline.id_contact'
			,'hotline.id_user'
			,'hotline.hotline'=>array("truncate"=>false)
			,'temps_estime'=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","renderer"=>"temps","width"=>80)
			//,'temps_total'=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","renderer"=>"temps","width"=>80)
			,'temps_facture_calcule'=>array("custom"=>true,"align"=>"right","width"=>80)
			//,'temps'=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","renderer"=>"temps","width"=>80)
			,'duree_work'=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","renderer"=>"temps","width"=>80)
			,'duree_presta'=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","renderer"=>"temps","width"=>80)
			,'duree_dep'=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","renderer"=>"temps","width"=>80)
			,'dead_line'=>array("custom"=>true,"width"=>90,"fixedWidth"=>true)
			,'hotline.priorite'=>array("custom"=>true,"renderer"=>"priorite","rowEditor"=>"prioriteUpdate","width"=>150)
			,"ratio"=>array("custom"=>true,"aggregate"=>array("avg","min","max","sum"),"type"=>"decimal")
		);



		//Colonnes principales
		$this->colonnes['primary'] = array(
			"id_societe"=>array("autocomplete"=>array("function"=>"autocompleteOnlyActive"))
			,"pole_concerne"
			,"id_contact"
			,"id_gep_projet"
			,"hotline"
			,"id_user"
			,"detail"
			,"visible"
			,"estimation"=>array("targetCols"=>2)
			,"date_terminee"
			,"urgence"=>array("targetCols"=>2)
		);

		//Autocomplete
		$this->affaireAutocompleteMapping=array(
			array("name"=>'id', "mapping"=>0),
			array("name"=>'nom', "mapping"=>1),
			array("name"=>'date', "mapping"=>2),
			array("name"=>'etat', "mapping"=>3)
		);

		//IMPORTANT, complète le tableau de colonnes avec les infos MYSQL des colonnes
		$this->fieldstructure();

		$this->panels['primary'] = array("nbCols"=>2,"collapsible"=>false,"columnsWidth"=>array("0.6","0.4"));

		$this->colonnes['bloquees']['select'] = array("send_mail","type_requete","ok_facturation","priorite","avancement","mono_interaction");
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] = array("id_hotline","etat","date_debut","date_fin","date","commentaire","temps","wait_mep","facturation_ticket","ok_facturation","indice_satisfaction","avancement","date_modification","coordonnees_rapides","temps_passe","temps","id_affaire","priorite","charge");
		$this->colonnes['bloquees']['update'] = array("send_mail","type_requete","id_hotline","etat","date_debut","date_fin","date","ok_facturation","commentaire","temps","facturation_ticket","wait_mep","avancement","date_modification","coordonnees_rapides","indice_satisfaction","charge","temps_passe","temps","mono_interaction");
		$this->field_nom = "%hotline.hotline%";
		$this->files["fichier_joint"] = array("multiUpload"=>true);
		$this->onglets = array(
			'hotline_interaction'=>array("opened"=>true)
			,'ordre_de_mission'=>array("opened"=>true)
		);

		// Blocage de l'insertion
		$this->no_insert = false;

		$this->addPrivilege("setBillingMode","update");
		$this->addPrivilege("setbillingModeNew","update");
		$this->addPrivilege("takeRequest","update");
		$this->addPrivilege("resolveRequest","update");
		$this->addPrivilege("cancelRequest","update");
		$this->addPrivilege("boostBilling","update");
		$this->addPrivilege("fixingRequest","update");
		$this->addPrivilege("setWaitMep","update");
		$this->addPrivilege("setMep","update");
		$this->addPrivilege("cancelMep","update");
		$this->addPrivilege("setPriorite","update");
		$this->addPrivilege("setWait","update");
		$this->addPrivilege("selectForDashBoard");
		$this->addPrivilege("getFormBillingMode");
		$this->addPrivilege("listeHotline");
		$this->addPrivilege("getMEPTicket");
		$this->addPrivilege("massValidMEP","update");
		$this->addPrivilege("getModeFacturation");
		$this->addPrivilege("estAuForfait");




		// Mobile
		$this->addPrivilege("rpcGetRecentForMobile","select");
		$this->addPrivilege("rpcGetInteractionsForMobile","select");

		//Redirection par défaut
		$this->defaultRedirect["insert"]="select";
		$this->defaultRedirect["update"]="select";
		$this->defaultRedirect["cloner"]="select";
		$this->formExt=true;

		$this->defaultRedirect["insert"]="select";

		$this->autocomplete["view"] = array(
			"ROUND(hotline.id_hotline)"=>array("alias"=>"id")
			// "hotline.hotline"=>array("alias"=>"nom"),
			// "societe.id_societe"=>array("alias"=>"d1"),
			// "contact.id_contact"=>array("alias"=>"d2"),
			// "hotline.etat"=>array("alias"=>"d3")
		);
		// $this->autocomplete = array(
		// 	"view"=>array("hotline.id_hotline","hotline.hotline","hotline.priorite","hotline.avancement")
		// 	// ,"field"=>array("hotline.id_hotline","hotline.hotline","societe.societe")
		// 	// ,"show"=>array("contact.civilite","contact.prenom","contact.nom")
		// 	// ,"popup"=>array("contact.nom","contact.prenom","contact.societe")
		// );

		$this->liste_user=$this->getUserActif();
		$this->addPrivilege("changeUser");

		$this->selectExtjs=true;
		$this->noCollapse = true;
	}

	/**
	* Surcharge de la méthode nom à cause du problème d'affichage du nom sur fiche select
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	* @return string
	*/
	public function nom($id) {
		if ($this->memory_optimisation_select && isset($this->cache[__FUNCTION__][$id])) {
			return $this->cache[__FUNCTION__][$id];
		} else {
			if (!$id) return;
			$id = $this->decryptId($id); // On sait jamais s'il s'agit d'un md5

			$this->q
				->reset()
				->addCondition($this->table.".id_".$this->table,$id)
				->setDimension("cell")
				->addField($this->table.".id_".$this->table);
			$nom = $this->sa();

			if ($this->memory_optimisation_select) {
				$this->cache[__FUNCTION__][$id] = $nom;
				return $this->cache[__FUNCTION__][$id];
			} else {
				return $nom;
			}
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
	public function saExport(){
		return $this->saCustom();
	}

	/**
	* Surcharge du select-All
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function saCustom(){
		if ($this->q->count!==false && $this->q->count!==1){
			$this->q->setCount();
		}
		$this->q->addJointure("hotline","id_hotline","hotline_interaction","id_hotline")
			->addGroup("hotline.id_hotline")
			->addField("ROUND(hotline.id_hotline)","id_hotline")
			->addField("ROUND(CEIL(SUM(TIME_TO_SEC(hotline_interaction.temps))/3600*4)/4,2)","temps")
			->addField("ROUND(CEIL(SUM(TIME_TO_SEC(hotline_interaction.duree_dep))/3600*4)/4,2)","duree_dep")
			->addField("ROUND(CEIL(SUM(TIME_TO_SEC(hotline_interaction.duree_presta))/3600*4)/4,2)","duree_presta")
			->addField("ROUND(CEIL(SUM(TIME_TO_SEC(hotline_interaction.duree_presta)-TIME_TO_SEC(IF(hotline_interaction.duree_pause IS NULL,0,hotline_interaction.duree_pause)))/3600*4)/4,2)","duree_work")
			->addField("hotline.id_affaire","hotline.id_affaire_fk")
			->addField("hotline.urgence")
			->addField("hotline.etat")
			->addField("hotline.visible")
			->addField("hotline.wait_mep")
			->addField("hotline.avancement")
			->addField("hotline.estimation")
			->addField("hotline.date_terminee")
			->addField("hotline.id_user")
			->addField("hotline.id_affaire")
			->addField("ROUND(TIME_TO_SEC(hotline.estimation)/3600,2)","temps_estime")
			->addField("ROUND(SUM(TIME_TO_SEC(hotline_interaction.temps_passe))/3600,2)","temps_total")
			->addField("hotline.priorite")
			->addField("IF(hotline.facturation_ticket = 'oui' AND hotline.charge = 'intervention' AND SUM(TIME_TO_SEC(hotline_interaction.duree_presta))>0,
							ROUND(
							(CASE
								WHEN (SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) - FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) > 0.75 THEN FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep))+1

								WHEN (SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) - FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) > 0.50 && (SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) - FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) < 0.75
								THEN FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep))+0.75


								WHEN (SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) - FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) > 0.25 && (SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) - FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) < 0.50
								THEN FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) + 0.50

								WHEN (SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) - FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) < 0.25 && (SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) - FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) > 0.00
								THEN FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) +0.25

								WHEN (SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) - FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep)) = 0.00
								THEN FLOOR(SUM(hotline_interaction.credit_presta)+ SUM(hotline_interaction.credit_dep))

							END)
							/((SUM(TIME_TO_SEC(hotline_interaction.duree_dep))/3600)+(SUM(TIME_TO_SEC(hotline_interaction.duree_presta))/3600)),2),
							NULL)", "ratio");

		// Derrnier crédit restant basé sur gestion_ticket
		$hi = new hotline_interaction();
		$id_sub = "hi";
		$hi->q->setAlias("hiSub")->addField("MAX(hiSub.id_hotline_interaction)","id")->orWhere("hiSub.id_hotline","hotline.id_hotline",false,"=",true,true)->setStrict()->setToString();
		$this->q->orWhere("hi.id_hotline_interaction","(".$hi->sa().")","hiSubWhere","=",true,true)
			->addField("hi.date","date_last_interaction")
			->from("hotline","id_hotline","hotline_interaction","id_hotline","hi",NULL,NULL,"hiSubWhere");

		/*$this->q
			->addOrder("date_last_interaction","desc")
			->addOrder("hotline.priorite","desc")
			->addOrder("hotline.date","asc");*/
		$sa=$this->select_all();

		// Trier par défaut dans l'ordre d'urgence (priorité)
		// On ajoute sur la première page le calcul de la deadline en fonction de la priorité, des jours ouvrés, du temps estimé et du travail déjà effectué.

		//--Calcul de la deadline
		//Calcul uniquement pour la première ligne
		if($this->q->getPage()==0){
			//date_cursors permet de gérer la dead_line par utilisateur
			$date_cursor=strtotime("now");
			$date_cursors=array();
			$cursor=&$date_cursor;
			$reste=0;

			foreach($sa["data"] as $number=>$line){
				$sa["data"][$number]["temps_facture_calcule"] = $this->getTimeFactureCalcule($line);

				//Si pas d'estimation nc
				if(!$line["temps_estime"]||$line["temps_estime"]==0.00||$line["hotline.etat"]=="payee"||$line["hotline.etat"]=="annulee"||$line["hotline.etat"]=="done"){
					$sa["data"][$number]["dead_line"]="-";
				}else{
					//Recherche de l'utilisateur et de sa dead_line
					if($sa["data"][$number]["hotline.id_user"]){
						$cursor=&$date_cursors[$sa["data"][$number]["hotline.id_user"]];
						if(!$cursor){
							$cursor=strtotime("now");
						}
					}else{
						$cursor=&$date_cursor;
					}
					//On calcule la différence entre l'estimation et le temps total
					$total=$line["temps_estime"]-$line["temps_total"];
					if($total>=0){
						//On détermine le jour ou on devra finir (dead_line)
						//--D'abords le nombre de jours
						$total=$total+$reste;//On ajoute le reste
						$nb_jours=(int)($total/7);
						$reste=$total-$nb_jours*7;//On calcule le reste
						//On doit finir aujour'hui
						if($nb_jours==0){
							$sa["data"][$number]["dead_line"]=ATF::$usr->date_trans(date("Y-m-d",$date_cursor),false);
							if($cursor==strtotime("now")){
								$sa["data"][$number]["urgent"]="now";
							}
						}else{
							//Il faut trouver un autre jour Mais un jour OUVRE !
							while($nb_jours>0){
								$cursor=strtotime("+1 day",$cursor);
								if(!util::testJour(date("Y-m-d",$cursor))){
									$nb_jours--;
								}
							}
							/*if($reste>0){
								do{
									$date_cursor=strtotime("+1 day",$date_cursor);
								}while(util::testJour(date("Y-m-d",$date_cursor)));
							}*/
							$sa["data"][$number]["dead_line"]=ATF::$usr->date_trans(date("Y-m-d",$cursor),false);
						}
					}else{
						//On dépasse ! Il faut terminer le boulot aujourd'hui
						if($date_cursor==strtotime("now")){
							$sa["data"][$number]["dead_line"]=ATF::$usr->date_trans(date("Y-m-d"),false);
						}else{
							$sa["data"][$number]["dead_line"]=ATF::$usr->date_trans(date("Y-m-d",$cursor),false);
						}
						$sa["data"][$number]["urgent"]="outdated";
					}
				}
			}
		//Pour les autres lignes : nc
		}else{
			foreach($sa["data"] as $number=>$line){
				$sa["data"][$number]["dead_line"]="-";
				$sa["data"][$number]["temps_facture_calcule"] = $this->getTimeFactureCalcule($line);			}
		}
		return $sa;
	}

	public function getTimeFactureCalcule($line){
		if($line["hotline.id_affaire_fk"]){
			ATF::devis()->q->reset()->where("devis.id_affaire", $line["hotline.id_affaire_fk"])
									->where("devis.etat","gagne","OR","condEtatDevis","=")
									->where("devis.etat","attente","OR","condEtatDevis","=")
									->where("devis.etat","bloque","OR","condEtatDevis","=");

			$devis = ATF::devis()->select_row();

			$jours = $heures= $minutes = 0;

			$duree=(($devis["prix"]-$devis["prix_achat"])/69); // Nombre heure

			$jours = intval($duree /7);
			$heures=intval($duree - ($jours*7));
			$minutes=intval( ($duree -(($jours*7) + $heures))*60  );

			return $jours."j ".$heures."h".$minutes;
		}else{
			return "-";
		}
	}



	/**
	* Donne le temps FACTURE sur une requête. C'est le temps correspondant au champ "temps" sur les interactions.
	* Le temps est arrondi au quart d'heure.
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_hotline l'id hotline correspondant
	* @return int le temps total travaillé en base 10. 1 = 1 heure
	*/
	public function getBillingTime($id_hotline){
		//return $this->getTime($id_hotline,"temps");

		return $this->getTotalTime($id_hotline,"presta");
	}


	public function getCreditUtilises($id_hotline){
		if($this->select($id_hotline, "facturation_ticket") == "non"){
			$nb = 0.00;
		}else{
			$credit = $this->getTime($id_hotline,"credit");

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
	* Donne le temps PASSE sur une requête. C'est le temps correspondant au champ "temps_passe" sur les interactions.
	* C'est le temps de travail réél
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_hotline l'id hotline correspondant
	* @return int le temps total travaillé en base 10. 1 = 1 heure
	*/
	public function getTotalTime($id_hotline, $type="temps_passe"){
		if($type == "temps_passe") return $this->getTime($id_hotline,$type);
		else {
			$sec = $this->getTime($id_hotline,$type);
			$heures = intval($sec/3600);
			if($heures < 10) $heures = "0".$heures;
			$minutes = ($sec-($heures*3600))/60;
			if($minutes < 10) $minutes = "0".$minutes;
			return $heures."H".$minutes;
		}
	}

	/**
	* Donne le temps ESTIME sur une requête. C'est le temps correspondant au champ "estimation" sur la hotline.
	* C'est le temps de travail estimé.
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_hotline l'id hotline correspondant
	* @return int le temps estimé en base 10. 1 = 1 heure
	*/
	public function getEstimatedTime($id_hotline){
		return $this->getTime($id_hotline,"estimation");
	}

	/**
	* Calcul le temps de travail facturé ou passé pour un billet hotline donnée
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @author QJ <qjanon@absystech.fr>
	* @param int $id_hotline l'id hotline correspondant
	* @param string $temps "temps_passe" ou "temps"
	* @return int le temps total travaillé en base 10. 1 = 1 heure
	*/
	private function getTime($id_hotline,$temps){
		$this->q->reset();

		if($temps=="temps_passe"){
			//Pour le temps passé on effectue juste une somme du temps passé
			$this->q->addField(array(
				"ROUND(
					SUM(
						TIME_TO_SEC(temps_passe)
						)
					/3600,2)
				")
			);
		}elseif($temps=="presta"){
			//Pour le temps passé on effectue juste une somme du temps passé
			$this->q->addField(array(
				"SUM(
					TIME_TO_SEC(duree_presta)
				)-
				SUM(
					TIME_TO_SEC(duree_pause)
				)")
			);
		}elseif($temps=="dep"){
			//Pour le temps passé on effectue juste une somme du temps passé
			$this->q->addField(array(
				"SUM(
					TIME_TO_SEC(duree_dep)
				)")
			);
		}elseif($temps=="credit"){
			//Pour le temps passé on effectue juste une somme du temps passé
			$this->q->addField(array("SUM(credit_presta)+SUM(credit_dep)"));
		}elseif($temps=="estimation"){
			$this->q->addField(array("
				ROUND(
					TIME_TO_SEC(estimation)
				/3600,2)
				")
			);
		}elseif($temps == "prestaTicket"){
			$this->q->addField(array(
				"SUM(
					TIME_TO_SEC(duree_presta)
				)-
				SUM(
					TIME_TO_SEC(duree_pause)
				)")
			)
				->where("hotline_interaction.nature","interaction");

		}else{
			//Pour le temps facturé on arrondi au quart d'heure supérieur
			$this->q->addField(array(
				"ROUND(
					CEIL(
						SUM(
							TIME_TO_SEC(temps)
							)
						/3600*4)
					/4,2)
				")
			);
		}

		$this->q->addJointure($this->table,"id_hotline","hotline_interaction","id_hotline")
			->addCondition('hotline.id_hotline',$this->decryptId($id_hotline))
			->setDimension("cell");
		return $this->sa();
	}

//	/**
//	* Retourne uniquement les informations nécessaires à la création d'un menu déroulant standard
//    * @author QJ <qjanon@absystech.fr>
//    * @return array societe
//    */
//	public function pole_options() {
//		foreach($this->enum2array("pole_concerne")  as $key => $item) {
//			$return[$item] = ATF::$usr->trans($item,$this->table);
//		}
//		return $return;
//	}

	/**
	* Prend en charge la requête
	* @author Quentin JANON <qjanon@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos
	* @param array $s
	* @param array $files
	* @return boolean true
	*/
	public function takeRequest($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		$this->infoCollapse($infos);

		//Mode transactionel
		ATF::db($this->db)->begin_transaction();
		/* Mise à jour de la requête en cours*/
		$infos_hotline=array();
		$infos_hotline["id_hotline"]=$infos["id_hotline"];
		$infos_hotline["id_user"]=((isset($infos["id_user"]))?$infos["id_user"]:ATF::$usr->getID());
		$infos_hotline["date_debut"]=date("Y-m-d H:i:s",time());
		if($this->select($infos["id_hotline"],"facturation_ticket")!=="oui"){
			$infos_hotline["etat"]="fixing";
		}
		parent::update($infos_hotline);

		//Recherche de la hotline
		$hotline=$this->select($infos["id_hotline"]);

		//Mise à jour de l'avancement
		$this->setPrioriteByUrgence($hotline["id_hotline"],$hotline["urgence"]);

		//Insère une interaction d'information
		$this->createInternalInteraction($infos["id_hotline"],"Requête prise en charge par ".ATF::user()->nom(((isset($infos['id_user']))?$infos['id_user']:ATF::$usr->getID())),"oui");

		//Partie mail
		if ($infos["send_mail"]=="true" && $hotline["visible"]=="oui") {
			try{
				//Mail contact
				ATF::hotline_mail()->createMailTakeCustomer($hotline["id_hotline"]);
				ATF::hotline_mail()->sendMail();
				//Notice mail envoyé
				$this->createMailNotice("hotline_mail_prise_en_charge_contact");
			}catch(errorATF $e){
				//$this->createMailNotice("hotline_no_mail_prise_en_charge_contact");
			}
		}
		/* Mail d'information au pôle concerné */
		ATF::hotline_mail()->createMailTakeAT($hotline["id_hotline"]);
		ATF::hotline_mail()->sendMail();
		//Notice mail envoyé
		$this->createMailNotice("hotline_mail_prise_en_charge_pole");


		//On commit le tout
		ATF::db($this->db)->commit_transaction();

		//Cadre refresh
		$this->redirection("select",$hotline["id_hotline"]);

		//Notice
		$this->createNotice("hotline_prise_en_charge");

		return true;
	}

	/**
	* Génère le formulaire de choix de facturation
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function getFormBillingMode($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);

		//Récupération des infos hotline
		if(!$infos["id_hotline"]) throw new errorATF("null_id_hotline");
		$hotline=$this->select($infos["id_hotline"]);

		//Détermination du type de la requête
		$type_requete="";
		if($hotline["facturation_ticket"]=="oui"){
			$type_requete="charge_client";
		}elseif($hotline["id_affaire"]){
			$type_requete="affaire";
		}else{
			$type_requete="charge_absystech";
		}

		//Création de l'affichage
		ATF::$cr->add("main","hotline-billingMode");
		$var=array("current_class"=>$this
					,"id_hotline"=>$infos["id_hotline"]
					,"charge"=>$hotline["charge"]
					,"type_requete"=>$type_requete
					,"id_affaire"=>$hotline["id_affaire"]
					,"id_societe"=>$hotline["id_societe"]
					,"send_mail"=>$infos["send_mail"]);
		ATF::$cr->add("mainScript","hotline-billingMode.tpl.js",$var);
		ATF::$cr->block("top");
	}

	/**
	* Relance la facturation
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function boostBilling($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		if(!$infos["id_hotline"]) throw new errorATF("null_id_hotline");
		$infos=$this->select($infos["id_hotline"]);
		$infos["relance"]=true;
		$infos["send_mail"]=true;
		$this->setBillingMode($infos,$s,$files,$cadre_refreshed);
		$this->redirection("select",$infos["id_hotline"]);

		//Trace dans les interactions
		$this->createInternalInteraction($infos["id_hotline"],"Relance de la facturation par ".ATF::user()->nom(ATF::$usr->getId()));
	}

	/**
	* Choix du mode de facturation en fonction de deux paramètres : type de requête et charge de la requête
	* Type de requête : R&D, Maintenance, Intervention
	* Charge de la reuqête : Par rapport à une affaire, tickets ou à la charge d'AbsysTech
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos
	* @param array $s
	* @param array $files
	* @return boolean true
	*/
	public function setBillingMode($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);

		// Mode transactionel
		ATF::db($this->db)->begin_transaction();

		/* Mise à jour hotline */
		if($infos["relance"]===true){
			$hotline = array(
				"id_hotline"=>$this->decryptId($infos["id_hotline"])
				,"etat"=>"wait"
			);
		}else{
			$hotline = array(
				"id_hotline"=>$this->decryptId($infos["id_hotline"])
				,"charge"=>$infos["charge"]
			);
		}

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
			ATF::hotline_mail()->createMailBilling($hotline["id_hotline"]);
			ATF::hotline_mail()->sendMail();

			//Notice mail envoyé
			if($infos['relance']){
				$this->createMailNotice("hotline_relance_facturation");
			}else{
				$this->createMailNotice("hotline_mail_facturation");
			}
		}

		//Insère une interaction d'information
		if(!$infos["relance"] && !$infos["disabledInternalInteraction"]){
			//Trace dans les interactions
			$this->createInternalInteraction($infos["id_hotline"],"Choix de la facturation \"".$chargeText."\" par ".ATF::user()->nom(ATF::$usr->getId()));
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
	* Résoudre le ticket hotline - Passe le ticket en etat résolu
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos
	* @param array $s
	* @param array $files
	* @return bool true si le ticket est résolu
	*/
	public function resolveRequest($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		$this->infoCollapse($infos);

		//Vérification de l'état
		if($this->select($infos["id_hotline"],"wait_mep")=="oui") throw new errorATF(ATF::$usr->trans("wait_mep_not_valid",$this->table));

		//Vérification du travail
		if(!$this->getTotalTime($infos["id_hotline"])) throw new errorATF(ATF::$usr->trans("travail_null",$this->table));

		//Vérification des ordres de missions
		ATF::ordre_de_mission()->q->reset()->addField("etat")->addCondition("id_hotline",$this->decryptId($infos['id_hotline']));
		$odms=ATF::ordre_de_mission()->sa();
		foreach($odms as $odm){
			if($odm["etat"]=="en_cours"){
				throw new errorATF(ATF::$usr->trans("odm_en_cours",$this->table));
			}
		}

		//Mise à jour de la requête en cours
		$hotline = array(
			"id_hotline"=>$infos["id_hotline"]
			,"etat"=>"done"
			,"date_fin"=>date("Y-m-d H:i:s")
			,"priorite"=>0
		);

		parent::update($hotline,$s);

		//Insère une interaction d'information
		$inter=array("id_hotline"=>$this->decryptId($infos["id_hotline"])
					,"detail"=>"Requête résolue par ".ATF::user()->nom(ATF::$usr->getId())
					,"id_user"=>ATF::$usr->getId()
					,"visible"=>"non"
					,"internal"=>true
					);
		ATF::hotline_interaction()->insert($inter);

		//Envoi du mail
		$hotline = $this->select($infos["id_hotline"]);
		if ($infos["send_mail"]=="true" && $hotline["visible"]=="oui") {
			try{
				//Mail contact
				ATF::hotline_mail()->createMailResolve($hotline["id_hotline"]);
				ATF::hotline_mail()->sendMail();
				//Notice mail envoyé
				$this->createMailNotice("hotline_mail_resolu_client");
			}catch(errorATF $e){
			}
		}

		//Cadre refresh
		$this->redirection("select",$hotline["id_hotline"]);

		//Notice
		$this->createNotice("hotline_resolu");

		return true;
	}

	/**
	* Création d'une nouvelle requête hotline sur la partie Optima
	* @author QJ <qjanon@absystech.fr>
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed) {


		$this->infoCollapse($infos);


		if(method_exists("estFermee",ATF::societe()) && ATF::societe()->estFermee($infos["id_societe"])){
			throw new errorATF(ATF::$usr->trans("Impossible d'ajouter une requête car la société est inactive"));
		}


		//Vérification des informations
		if(!$infos["id_contact"] && $infos["id_contact"]!==false) throw new errorATF(ATF::$usr->trans("id_contact_null",$this->table));


		if(!$infos["pole_concerne"]) throw new errorATF("Il faut selectionner un pole associé pour cette requete");

		//Construction de la hotline
		$detail=$infos["detail"];
		$infos["detail"]="Requête mise en ligne par ".ATF::user()->nom(ATF::$usr->getID())."\n\n".$infos["detail"];

		$send_mail = false;
		//Gestion de l'envoi de mail
		if($infos["send_mail"]){
			$send_mail = true;
		}
		unset($infos["send_mail"]);

		// Gestion de l'urgence selon la priorité choisi
		if($infos['priorite']){
			if ($infos['priorite']>=15) {
				$infos['urgence'] = "bloquant";
			} elseif ($infos['priorite']>=5) {
				$infos['urgence'] = "genant";
			} else {
				$infos['urgence'] = "detail";
			}
		}

		if(!$infos["urgence"]) { $infos['urgence'] = "detail"; }


		//Date de création de la requête
		$infos["date"]=date('Y-m-d H:i:s');


		// Auto affectation a charge Absystech si client Absystech
		$id_societe = ATF::societe()->select($infos['id_societe'],"id_societe");
		if ($id_societe==1) {
			$infos['type_requete'] = "charge_absystech";
		} else if ($infos['id_gep_projet'] && $id_affaire_projet = ATF::gep_projet()->select($infos['id_gep_projet'],"id_affaire")) {
			$infos["type_requete"] = "affaire";
			$infos["charge"] = "intervention";
			$infos["id_affaire"] = $id_affaire_projet;
		}

		//Insertion de la requête
		$type_requete=$infos["type_requete"];
		unset($infos["type_requete"]);

		ATF::db($this->db)->begin_transaction();

		$id_hotline = parent::insert($infos,$s,$files);

		$hotline = $this->select($id_hotline);
		//Notice
		$this->createNotice("hotline_insert");


		//Gestion de la prise en charge de l'utilisateur
		if($infos["id_user"]){
			//Mise à jour de la hotline
			$infos_hotline['id_hotline']=$id_hotline;
			$infos_hotline["id_user"]=$infos['id_user'];
			$infos_hotline['date_debut']=date("Y-m-d H:i:s",time());
			$infos_hotline['etat']="fixing";
			parent::update($infos_hotline);
		}


		//Sélection du mode de facturation
		$infos["type_requete"]=$type_requete;
		$infos["id_hotline"]=$id_hotline;
		$infos["send_mail"]=$send_mail;
		$infos["disabledInternalInteraction"]=true;
		if ($infos["type_requete"]) {
			$this->setBillingMode($infos,$s,$files);
		}


		//Correction de l'état de la requête
		$hotline = $this->select($id_hotline);
		if(!$hotline["id_user"] && !$hotline["facturation_ticket"]=="oui"){
			$this->update(array("id_hotline"=>$id_hotline,"etat"=>"free","disabledInternalInteraction"=>true));
		}


		//Gestion de l'envoi de mail
		ATF::hotline_mail()->createMailInsert($id_hotline,$infos["filestoattach"]["fichier_joint"],$infos["id_user"]);


		//Fichier joint
		if($infos["filestoattach"]["fichier_joint"]){
			//Ajout du fichier joint
			$path = $this->filepath($id_hotline,"fichier_joint");
			$mail=ATF::hotline_mail()->getCurrentMail();
			$mail->addFile($path,"fichier_joint.zip",true);
		}

		ATF::hotline_mail()->sendMail();


		if((ATF::societe()->decryptId($infos["id_societe"]) != "1") && (ATF::societe()->decryptId($infos["id_societe"]) != "1154") && ($infos["visible"] == "oui") && $send_mail){
			$mail = ATF::hotline_mail()->getCurrentMail();
			if(ATF::contact()->select(ATF::contact()->decryptId($infos["id_contact"]) , "email")){
				ATF::hotline_mail()->createMailForCustomers($id_hotline, "Nouvelle requete", ATF::contact()->select(ATF::contact()->decryptId($infos["id_contact"]) , "email"), "hotline_insert_client");
				ATF::hotline_mail()->sendMail();
			}
		}



		//Notice mail envoyé
		$this->createMailNotice("hotline_mail_insert");

		//Trace dans les interactions
		$this->createInternalInteraction($id_hotline,"Requête créée par ".ATF::user()->nom(ATF::$usr->getId()));


		//Fin de transaction
		ATF::db($this->db)->commit_transaction();


		//cadre refresh
		$this->redirection("select",$id_hotline,"hotline-select-".$this->cryptId($id_hotline).".html");


		api::sendUDP(array("data"=>array("type"=>"interaction")));
		return $id_hotline;
	}

	/**
	* Création d'une nouvelle requête hotline sur la partie Optima
	* @author QJ <qjanon@absystech.fr>
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function NEW_insert($infos,&$s,$files=NULL,&$cadre_refreshed) {


		$this->infoCollapse($infos);

		if(method_exists("estFermee",ATF::societe()) && ATF::societe()->estFermee($infos["id_societe"])){
			throw new errorATF(ATF::$usr->trans("Impossible d'ajouter une requête car la société est inactive"));
		}

		//Vérification des informations
		if(!$infos["id_contact"] && $infos["id_contact"]!==false) throw new errorATF(ATF::$usr->trans("id_contact_null",$this->table));

		if(!$infos["pole_concerne"]) throw new errorATF("Il faut selectionner un pole associé pour cette requete");
		//Construction de la hotline
		$detail=$infos["detail"];
		$infos["detail"]="Requête mise en ligne par ".ATF::user()->nom(ATF::$usr->getID())."\n\n".$infos["detail"];

		$send_mail = false;
		//Gestion de l'envoi de mail
		if($infos["send_mail"]){
			$send_mail = true;
		}
		unset($infos["send_mail"]);

		// Gestion de l'urgence selon la priorité choisi
		if($infos['priorite']){
			if ($infos['priorite']>=15) {
				$infos['urgence'] = "bloquant";
			} elseif ($infos['priorite']>=5) {
				$infos['urgence'] = "genant";
			} else {
				$infos['urgence'] = "detail";
			}
		}

		if(!$infos["urgence"]) { $infos['urgence'] = "detail"; }


		//Date de création de la requête
		$infos["date"]=date('Y-m-d H:i:s');

		// Auto affectation a charge Absystech si client Absystech
		//$id_societe = ATF::societe()->select($infos['id_societe'],"id_societe");
		if (ATF::societe()->decryptId($infos['id_societe'])==1) {
			$infos['type_requete'] = "charge_absystech";
		}

		//Insertion de la requête
		$type_requete=$infos["type_requete"];
		unset($infos["type_requete"]);

		ATF::db($this->db)->begin_transaction();

		//Gestion de la prise en charge de l'utilisateur
		if($infos["id_user"]){
			//Mise à jour de la hotline
			$infos["id_user"]=$infos['id_user'];
			$infos['date_debut']=date("Y-m-d H:i:s",time());
			$infos['etat']="fixing";
		}

		$id_hotline = parent::insert($infos,$s,$files);
		//Notice
		$this->createNotice("hotline_insert");

		//Gestion de l'envoi de mail
		if(!$id_hotline) throw new errorATF(ATF::$usr->trans("null_id_hotline"));

		// Envoi du mail de création de hotline
		$contactn = ATF::contact()->nom($infos['id_contact']);
		$societen = ATF::societe()->nom($infos['id_societe']);


		$obj = "[#".$id_hotline;
		if ($infos['urgence']) $obj .= " - ".strtoupper($infos['urgence']);
		$obj .= "]";
		$obj .= "NOUVELLE REQUÊTE";
		if ($infos["id_user"]) $obj .= "pour ".ATF::user()->nom($infos["id_user"]);
		$obj .= " de ".$contactn;
		$obj .= "(".$societen.")";

		$to="hotline.".$infos['pole_concerne']."@absystech.fr";
		$template="hotline_insert";
		$from="Hotline AbsysTech <optima-hotline-".ATF::$codename."-".ATF::hotline()->cryptId($id_hotline)."-".ATF::contact()->cryptId($infos["id_contact"])."@absystech-speedmail.com>";

		$societe = ATF::societe()->select($infos['id_societe']);
		$contact = ATF::contact()->select($infos['id_contact']);
		$hotline = ATF::hotline()->select($id_hotline);

		$mail_data["optima_url"]= ATF::permalink()->getURL(ATF::hotline()->createPermalink($id_hotline));
		$mail_data["portail_hotline_url"]=$this->createPortailHotlineURL($societe["ref"],$societe["divers_5"],$infos["id_hotline"],$infos["id_contact"],"validation");
		$mail_data["ip"] = $_SERVER["REMOTE_ADDR"];
		$mail_data["contact"] = $contactn;
		$mail_data["societe"] = $societen;
		$mail_data["hotline"] = $hotline;
		$mail_data["recipient"] = $to;
		$mail_data["from"] = $from;
		$mail_data["objet"] = $obj;
		$mail_data["template"] = $template;
		$mail_data["fichier"] = $pj;

		$mail = new mail($mail_data);
		//Fichier joint
		if($infos["filestoattach"]["fichier_joint"]){
			//Ajout du fichier joint
			$path = $this->filepath($id_hotline,"fichier_joint");
			$mail->addFile($path,"fichier_joint.zip",true);
		}

		$mail->send();


		// if((ATF::societe()->decryptId($infos["id_societe"]) != "1") // AT
		// 	&& (ATF::societe()->decryptId($infos["id_societe"]) != "1154") // ATT
		// 	&& ($infos["visible"] == "oui")
		// 	&& $send_mail){
		// 	$mail = ATF::hotline_mail()->getCurrentMail();
		// 	if(ATF::contact()->select($infos["id_contact"], "email")){
		// 		ATF::hotline_mail()->createMailForCustomers(
		// 			$id_hotline,
		// 			"Nouvelle requete",
		// 			ATF::contact()->select($infos["id_contact"], "email"),
		// 			"hotline_insert_client"
		// 		);
		// 		ATF::hotline_mail()->sendMail();
		// 	}
		// }

		//Notice mail envoyé
		$this->createMailNotice("hotline_mail_insert");

		//Trace dans les interactions
		//$this->createInternalInteraction($id_hotline,"Requête créée par ".ATF::user()->nom(ATF::$usr->getId()));


		//Fin de transaction
		ATF::db($this->db)->commit_transaction();

		api::sendUDP(array("data"=>array("type"=>"interaction")));
		return $id_hotline;
	}

	/**
	* Mise à jour de la requête
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed){
		$this->infoCollapse($infos);

		//Vérification des informations
		//if(!$infos["id_contact"]) throw new errorATF(ATF::$usr->trans("id_contact_null",$this->table));

		if(isset($infos["send_mail"])){
			unset($infos["send_mail"]);
		}

		//Désactivation de la trace hotline
		if($infos["disabledInternalInteraction"]){
			$disabledInternalInteraction=true;
			unset($infos["disabledInternalInteraction"]);
		}
		$retour=parent::update($infos,$s,$files,$cadre_refreshed);

		//Trace dans les interactions
		if(!$disabledInternalInteraction){
			$this->createInternalInteraction($infos["id_hotline"],"Requête mise à jour par ".ATF::user()->nom(ATF::$usr->getId()));
		}

		api::sendUDP(array("data"=>array("type"=>"interaction")));

		return $retour;
	}

	/**
	* Annule une requête hotline
	* @author QJ <qjanon@absystech.fr>
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function cancelRequest($infos,&$s,$files=NULL,&$cadre_refreshed) {
		$this->infoCollapse($infos);

		//Mode transactionel
		ATF::db($this->db)->begin_transaction();

		//Mise à jour de la requête
		$hotline="";
		$hotline = $this->select($infos["id_hotline"]);
		$hotline["etat"]="annulee";
		$hotline["priorite"]=0;
		$hotline["wait_mep"]='0';
		parent::update($hotline);

		if ($infos["send_mail"]=="true" && $hotline["visible"]=="oui") {
			try{
				//Mail contact
				ATF::hotline_mail()->createMailCancel($hotline["id_hotline"]);
				ATF::hotline_mail()->sendMail();
				//Notice mail envoyé
				$this->createMailNotice("hotline_mail_cancel_client");
			}catch(errorATF $e){
			}
		}

		//Trace dans les interactions
		$this->createInternalInteraction($infos["id_hotline"],"Requête annulée par ".ATF::user()->nom(ATF::$usr->getId()));

		//Commit !
		ATF::db($this->db)->commit_transaction();

		//Cadre refresh
		$this->redirection("select",$hotline["id_hotline"]);

		//Notice
		$this->createNotice("hotline_annulee");

		return true;
	}

	/**
	* Passage d'une requête wait en fixing
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos
	* @param array $s
	* @param array $files
	* @return boolean true
	*/
	public function fixingRequest($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		$this->infoCollapse($infos);

		//Debut de la transaction
		ATF::db($this->db)->begin_transaction();

		/*Mise à jour de la requête en cours*/
		$hotline["id_hotline"]=$infos["id_hotline"];
		$hotline["etat"]="fixing";
		parent::update($hotline,$s);

		//Trace dans les interactions
		$u = $infos['id_user']?$infos['id_user']:ATF::$usr->getId();
		$this->createInternalInteraction($infos["id_hotline"],"Passage en état 'en cours' par ".ATF::user()->nom($u));

		//commit
		ATF::db()->commit_transaction();

		//Cadre refresh
		$this->redirection("select",$hotline["id_hotline"]);

		//Notice
		$this->createNotice("hotline_force_fixing");

		return true;
	}

	/**
	* Passage d'une requête en etat Wait
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos
	* @param array $s
	* @param array $files
	* @return boolean true
	*/
	public function setWait($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		$this->infoCollapse($infos);

		//Debut de la transaction
		ATF::db($this->db)->begin_transaction();

		/*Mise à jour de la requête en cours*/
		$hotline['id_hotline']=$infos['id_hotline'];
		$hotline['etat']="wait";
		parent::update($hotline,$s);

		//Trace dans les interactions
		$u = $infos['id_user']?$infos['id_user']:ATF::$usr->getId();
		$this->createInternalInteraction($infos["id_hotline"],"Passage en état 'en attente' par ".ATF::user()->nom($u));

		//commit
		ATF::db()->commit_transaction();

		//Cadre refresh
		$this->redirection("select",$hotline["id_hotline"]);

		//Notice
		$this->createNotice("hotline_force_wait");

		return true;
	}


	/**
	* Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @author Jérémie GWIAZDOWSKI <jgwiazdowski@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
	*/
	public function default_value($field){
		switch ($field) {
			case "pole_concerne":
				$pole=ATF::user()->select(ATF::$usr->getID(),"pole");
				if($pos=strpos($pole,",")){
					$pole=substr($pole,0,$pos);
				}
				return $pole;
			break;
			case "estimation":
				return "00:00";
			break;
			default:
				return parent::default_value($field);
		}
	}

	/**
	* Passe un ticket en attente de Mise en production
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos
	* @param array $s
	* @param array $files
	* @return bool true si le ticket est résolu
	*/
	public function setWaitMep($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		//Vérification des infos
		if(!$infos || !$infos["id_hotline"]) throw new errorATF(ATF::$usr->trans("aucunes_infos",$this->table));

		//Mode transactionel
		ATF::db($this->db)->begin_transaction();

		/*Mise à jour de la requête en cours*/
		$hotline = array(
			"id_hotline"=>$infos["id_hotline"]
			,"wait_mep"=>"oui"
		);

		parent::update($hotline,$s);

		//Envoi d'un mail au chef de projet !
		ATF::hotline_mail()->createMailWaitMep($infos["id_hotline"]);
		ATF::hotline_mail()->sendMail();

		//Notice mail envoyé
		$this->createMailNotice("hotline_mail_wait_mise_prod");

		//Trace dans les interactions
		$u = $infos['id_user']?$infos['id_user']:ATF::$usr->getId();
		$this->createInternalInteraction($infos["id_hotline"],"Demande de Mise en prod par ".ATF::user()->nom($u));

		//Commit
		ATF::db($this->db)->commit_transaction();

		//Cadre refresh
		$this->redirection("select",$hotline["id_hotline"]);

		//Notice
		$this->createNotice("hotline_wait_mise_prod");

		return true;
	}

	/**
	* Passe un ticket en mise en prod OK
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos
	* @param array $s
	* @param array $files
	* @return bool true si le ticket est résolu
	*/
	public function setMep($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		//Vérification des infos
		if(!$infos || !$infos["id_hotline"]) throw new errorATF(ATF::$usr->trans("aucunes_infos",$this->table));

		//Mode transactionel
		ATF::db($this->db)->begin_transaction();

		/*	Mise à jour de la requête en cours*/
		$hotline = array(
			"id_hotline"=>$infos["id_hotline"]
			,"wait_mep"=>"non"
		);

		parent::update($hotline,$s);

		//Envoi d'un mail au chef de projet !
		ATF::hotline_mail()->createMailMep($infos["id_hotline"]);
		ATF::hotline_mail()->sendMail();

		//Notice mail envoyé
		$this->createMailNotice("hotline_mail_mise_prod");

		//Trace dans les interactions
		$u = $infos['id_user']?$infos['id_user']:ATF::$usr->getId();
		$this->createInternalInteraction($infos["id_hotline"],"Mis en prod par ".ATF::user()->nom($u));

		//Commit
		ATF::db($this->db)->commit_transaction();

		//Cadre refresh
		$this->redirection("select",$hotline["id_hotline"]);

		//Notice
		$this->createNotice("hotline_mise_prod");

		return true;
	}

	/**
	* Annule une mise en pré-prod
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos
	* @param array $s
	* @param array $files
	* @return bool true si le ticket est résolu
	*/
	public function cancelMep($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		//Vérification des infos
		if(!$infos || !$infos["id_hotline"]) throw new errorATF(ATF::$usr->trans("aucunes_infos",$this->table));

		//Mode transactionel
		ATF::db($this->db)->begin_transaction();

		/*	Mise à jour de la requête en cours*/
		$hotline = array(
			"id_hotline"=>$infos["id_hotline"]
			,"wait_mep"=>"non"
		);

		parent::update($hotline,$s);

		//Trace dans les interactions
		$u = $infos['id_user']?$infos['id_user']:ATF::$usr->getId();
		$this->createInternalInteraction($infos["id_hotline"],"Demande de MEP annulé par ".ATF::user()->nom($u));

		//Commit
		ATF::db($this->db)->commit_transaction();

		//Cadre refresh
		$this->redirection("select",$hotline["id_hotline"]);

		//Notice
		$this->createNotice("hotline_cancel_mise_prod");

		return true;
	}

//	/**
//	* Création de l'URL Optima avec encryption en AES
//	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
//	* @param int $id_hotline L'identifiant hotline
//	* @return string l'url
//	*/
//	public function createOptimaURL($id_hotline){
//		$aes_tmp=new aes();
//		$id = $aes_tmp->crypt($id_hotline);
//		$url = __MANUAL_WEB_PATH__.'?url='.base64_encode('table=hotline&event=select&id_hotline='.$id.'&seed='.$aes_tmp->getIV().$aes_tmp->getKey());
//		$aes_tmp->endCrypt();
//		return $url;
//	}

	/**
	* Création de l'URL pour le portail Hotline
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $login Le Login du portail hotline
	* @param string $passwd Le mot de passe du portail hotline
	* @param int $id_contact Le contact associé à la requête
	* @param int $id_hotline L'identifiant hotline
	* @return string l'url
	*/
	public function createPortailHotlineURL($login,$passwd,$id_hotline,$id_contact,$event="select"){
		$url=__HOTLINE_URL__."login.php?login=".base64_encode($login)."&password=".base64_encode($passwd)."&contact=".base64_encode($id_contact)."&url=";
		$url.=base64_encode(__HOTLINE_URL__."hotline.php?table=hotline&event=".$event."&id_hotline=".$id_hotline);
		$url.= "&schema=".base64_encode(ATF::$codename);
		return $url;
	}

	/**
	* Donne la priorité : rouge, orange ou vert (bloquant, génant ou détail)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $etat l'état de la requête
	* @param int $priorite la priorite de la requête
	* @param string $mep oui|non
	* @return array 1er élément : string progressRed pour rouge, progressOrange pour orange et progressGreen pour vert, 2ème élément :string  le texte, 3ème élément la priorite
	*/
	public function getSimplePriorite($etat,$priorite,$mep){
		//$requete=$this->select($id_hotline);
		if ($mep=="oui") {
			return array(
				"progressBar"=>"progressGreen"
				,"text"=>ATF::$usr->trans("MEP_prevue",$this->table)
				,"priority"=>$priorite
			);
		} else {
			switch($etat){
				case "free":
					$pBar="progressRed";
					break;
				case "done":
				case "payee":
				case "annulee":
					$pBar="progressGrey";
					break;
				default:
					if($priorite<10){
						$pBar="progressGreen";
					}elseif($priorite<15){
						$pBar="progressOrange";
					}else{
						$pBar="progressRed";
					}
			}
			return array(
				"progressBar"=>$pBar
				,"text"=>ATF::$usr->trans($etat,$this->table)
				,"priority"=>$priorite
			);
		}
	}

	/**
	* Met à jour la priorité
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param array $infos "id_hotline" int l'id hotline et "priorite" int la priorité
	*/
	public function setPriorite($infos){
		if($infos["priorite"]>20||$infos["priorite"]<0){
			throw new errorATF(ATF::$usr->trans("invalid_range"));
		}

		$this->update(array("id_hotline"=>$infos["id_hotline"],"priorite"=>$infos["priorite"],"disabledInternalInteraction"=>true));

		if($infos["hotline_select"]){
			//Cadre refresh
			$this->redirection("select",$infos["id_hotline"]);

			//Notice
			$this->createNotice("update_priorite_select");
		}else{
			//Cadre refresh
			/*$var = array(
				"current_class"=>$this
			);
			ATF::$cr->add("main","generic-select_all.tpl.htm",$var);		*/			//Notice
			$this->createNotice("Mise a jour de la priorité réussie");
		}
	}

	/**
	* Met à jour la priorité par rapport à l'urgence
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_hotline L'identifiant de la hotline
	* @param string $urgence l'urgence de la requête
	*/
	public function setPrioriteByUrgence($id_hotline,$urgence){
		switch($urgence){
			case "detail":
				$priorite=5;
				break;
			case "genant":
				$priorite=10;
				break;
			case "bloquant":
				$priorite=15;
				break;
		}
		return $this->update(array("id_hotline"=>$id_hotline,"priorite"=>$priorite,"disabledInternalInteraction"=>true));
	}

	/**
	* Donne l'avancement d'une requête
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $etat l'état de la requête
	* @return string $avancement l'avancement de la requête
	*/
	public function getAvancement($etat,$avancement,$complet=true){
		if(!$complet){
			return $avancement;
		}
		//$requete=$this->select($id_hotline);
		switch($etat){
			case "free":
				return 0;
			case "fixing":
			case "wait":
				return 20+0.6*$avancement;
			case "done":
				return 90;
			case "payee":
				return 100;
			case "annulee":
				return 100;
		}

		return false;
	}

	/**
	* Ajuste l'avancement d'une requête hotline
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_hotline L'identifiant de la hotline
	* @param int $nb l'avancement
	*/

	public function setAvancement($id_hotline,$avancement){
		if($avancement>100||$avancement<0){
			throw new errorATF(ATF::$usr->trans("invalid_range"));
		}
		return $this->update(array("id_hotline"=>$id_hotline,"avancement"=>$avancement,"disabledInternalInteraction"=>true));
	}

	/**
	* Retourne le temps total passé sur les tickets non résolus (en cours)
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param boolean $useForecast Si VRAI alors on pondère le CA par le pourcentage de forecast
	* @return int
	*/
	public function getTempsTotalNonResolu(){
		$this->q->reset()
			->setDimension('cell')
			->addField("SUM(TIME_TO_SEC(temps))")
			->addCondition("etat","done","AND",false,"!=")
			->addCondition("etat","payee","AND",false,"!=")
			->addCondition("etat","annulee","AND",false,"!=")
			->addJointure($this->table,"id_hotline","hotline_interaction","id_hotline");
		return parent::select_all()/3600/7;
	}

	/**
	* Retourne true si la requete est déjà payée et a été facturée
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param id_hotline
	* @return boolean
	*/
	public function alreadyBilled($id){
		return $this->select($id,"facturation_ticket")=="oui" && in_array($this->select($id,"etat"),array("done","annulee","payee"));
	}

	/**
	* Select all des tickets hotlines pour le dashBoard
	* @author QJ <qjanon@absystech.fr>
	* @param array $params
	*/
	public function selectForDashBoard($params) {
		$this->q->reset()
				->addField("hotline.id_hotline","id")
				->addField("hotline.id_societe","id_societe")
				->addField("societe.societe","societe")
				->addField("hotline.hotline","hotline")
				->addField("hotline.etat","etat")
				->addField("hotline.priorite","priorite")
				->addField("hotline.avancement","avancement")
				->addJointure("hotline","id_societe","societe","id_societe")
				->addCondition('hotline.etat','fixing')
				->addCondition('hotline.etat','wait')
				->addCondition('hotline.id_user',ATF::$usr->getID())
				->setLimit($params["limit"])
				->setPage($params["start"]/$params["limit"])
				->setCount()
				->addOrder("hotline.date","desc");

		$result = parent::select_all();
		ATF::$json->add("totalCount",$result["count"]);
		return $result['data'];
	}

	//************************CRONTAB*****************************/

//	/**
//	* Traitement des hotlines en pointage
//	*/
//	public function hotline2pointage(){
//		try{
//			//Debut de la transaction
//			ATF::db($this->db)->begin_transaction();
//			//Recherche des hotlines
//			$this->q->reset();
//			$data=$this->sa();
//			$cpt=0;
//			echo "--Traitement des requêtes hotline--\n";
//			foreach($data as $hotline){
//				echo "-- Requête n°".$hotline["id_hotline"]."\n";
//				//Recherche des interactions
//				ATF::hotline_interaction()->q->reset()->addCondition("id_hotline",$hotline["id_hotline"]);
//				$data2=ATF::hotline_interaction()->sa();
//				foreach($data2 as $interaction){
//					echo "---- Interaction n°".$interaction["id_hotline_interaction"]." - Temps : ".$interaction["temps"]."\n";
//					if($interaction["temps"]){
//						$cpt++;
//						//Insertion de l'interaction
//						ATF::pointage()->insert("hotline",$hotline["id_hotline"],$interaction["id_hotline_interaction"],$hotline["id_user"],$interaction["date"],$interaction["temps"],$hotline["id_gep_projet"]);
//					}
//				}
//			}
//			//Fin de la transaction
//			ATF::db($this->db)->commit_transaction();
//			echo "Nombre de pointages créés : ".$cpt."\n";
//			echo "--Fin Traitement des requêtes hotline--\n";
//		}catch(errorATF $e){
//			echo "Erreur ! : ".$e."\n";
//		}
//	}

	/**
	* Sous-routine de traitement de la facturation
	* Evite de dupliquer les lignes de code pour changer le paramèter de facturation...
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $facturation_ticket oui ou non
	*/
	public function ss_traitement_facturation($facturation_ticket='oui'){
		//Debut de la transaction
		ATF::db($this->db)->begin_transaction();
		/*-------------Requêtes Facturées----------------------*/
		$this->q->reset()
				->addCondition('etat','done',NULL,1)
				->addCondition('facturation_ticket',$facturation_ticket,'AND',1)
				->addCondition('indice_satisfaction',NULL,'AND',1,'IS NOT NULL')
				;

		//Iteration 2
		//Construction du time
		$nb_days=__NB_DAYS_FACTURATION__;
		$date_dead_line=date('Y-m-d',strtotime("-".(($nb_days)?$nb_days:"7")." days"));

		$this->q->addCondition('etat','done',NULL,2)
			   ->addCondition('facturation_ticket',$facturation_ticket,'AND',2)
			   ->addCondition('date_fin',$date_dead_line,'AND',2,'<')
			   ->addSuperCondition('1,2');

		$donnees=$this->sa();

		echo 'nb_requetes-facturation='.$facturation_ticket.':';print_r(count($donnees));echo "\n";
		$compteur=0;
		$count_tickets=0;
		foreach($donnees as $req){
			$compteur++;
			if($facturation_ticket=='oui'){
				echo '['.$compteur.']Traitement de la requete '.$req['id_hotline']." - ".ATF::societe()->nom($req['id_societe'])." - debit de ";

				$nb_tickets=ATF::gestion_ticket()->remove_ticket(array(
					'id_societe'=>$req['id_societe']
					,'id_hotline'=>$req['id_hotline']
				),$s);

				$count_tickets+=$nb_tickets;

				echo $nb_tickets." ticket(s) \n";
			}else{
				echo '['.$compteur.']Traitement de la requete '.$req['id_hotline']." - ".ATF::societe()->nom($req['id_societe'])."\n";
			}

			parent::update(array(
				'id_hotline'=>$req['id_hotline']
				,'etat'=>"payee"
				,'priorite'=>0
			),$s);
		}

		if($facturation_ticket=='oui'){
			//Total des tickets débités :
			echo "Total tickets de la journée : -".$count_tickets."\n";

			//Total de tout les tickets négatifs
			echo "Total de tout les tickets négatifs restants : ".ATF::societe()->getSoldeSNegatives()."\n";

			//Total de tout les tickets
			echo "Total de tout les tickets restants (négatifs + positifs) : ".ATF::societe()->getSoldeS()."\n";

		}

		//Commit !
		ATF::db()->commit_transaction();
	}

	/**
	* Effectue la facturation des tickets
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param array $s la session
	* @param boolean $force_done Force les requêtes non terminées
	*/
	public function traitement_facturation(&$s){
		//Temporisation de sortie
		ob_start();
		echo "[Debut Facturation]\n";

		//Debut de la transaction
		ATF::db($this->db)->begin_transaction();

		/*-------------Requêtes Non Facturées----------------------*/
		$this->ss_traitement_facturation('non');

		/*-------------Requêtes Facturées----------------------*/
		$this->ss_traitement_facturation('oui');

		//commit
		ATF::db()->commit_transaction();

		echo "[Fin facturation]\n";

		//Envoie d'un mail de rapport
		$mail=new mail(array('recipient'=>'smortier@absystech.fr','objet'=>'[Facturation Hotline - Optima '.ATF::$codename.'] Résultats batch','body'=>ob_get_contents()));
		$mail->send();

		//Affichage du résultat
		ob_end_flush();
	}

	/**
	* Met à jour la priorité
	* Règle actuelle : - +1 pour des requêtes 0<r<19 tous les 3 jours
	*                  - Requête en Etat fixing uniquement
	* La méthode incrémente juste. La fréquence (tous les 2 jours) est à régler dans la crontab.
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function upgradePriorite(){
		//Debut de la transaction
		ATF::db($this->db)->begin_transaction();

		$this->q->reset()->addCondition("etat","fixing")
						->addCondition("priorite","0","AND",false,">")
						->addCondition("priorite","19","AND",false,"<");
		$elements=$this->sa();

		//Temporisation de sortie
		echo "[Debut Upgrade Priorite]\n";
		try{
		foreach($elements as $element){
			$nouvelle_priorite=$element["priorite"]+1;
			$this->update(array("id_hotline"=>$element["id_hotline"],"priorite"=>$nouvelle_priorite,"disabledInternalInteraction"=>true));
			echo "Maj requête n° ".$element["id_hotline"]." Ancienne priorite=".$element["priorite"]." Nouvelle priorite=".$nouvelle_priorite."\n";
		}
		}catch(errorATF $e){
			ATF::db()->rollback_transaction();
			echo "Erreur : ".$e->getMessage()."\n";
			return false;
		}
		echo "[Debut Fin Priorite]\n";
		//commit
		ATF::db()->commit_transaction();
	}


	public function _graph_tickets_hotline($get, $post){
		/*
		$at = $this->stats(true);
		ATF::define_db("db","extranet_v3_att");
		ATF::$codename = "att";
		$att = $this->stats(true);
		ATF::define_db("db","extranet_v3_absystech");
		ATF::$codename = "absystech";

		return array("at"=>$at, "att"=>$att, "infos"=>array("graph"=>"charge actuelle"));
		*/
		return 'un test';
	}

	public function _stats($get, $post){
		$at = $this->stats(true);
		ATF::define_db("db","extranet_v3_att");
		ATF::$codename = "att";
		$att = $this->stats(true);
		ATF::define_db("db","extranet_v3_absystech");
		ATF::$codename = "absystech";

		return array("at"=>$at, "att"=>$att, "infos"=>array("graph"=>"charge actuelle"));
	}

	//************************STATS*****************************/
	/**
	* Statistiques sur nombre de ticket en cours non fini par personne
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array session
	* @param bool $widget
	* @param string $type Type de stats
	* return enregistrements
	*/
	public function stats($widget=false,$type=NULL,$tu=NULL){
		switch ($type) {
			case "requetebyUser" :

				$user = ATF::$usr->getID();
				if($tu){
					$user = $tu;
				}
				$this->q->reset()->addField("SUM(hotline.id_hotline)", "total")
								 ->addField("hotline.pole_concerne")
								 ->where("hotline.pole_concerne","telecom")
								 ->where("hotline.pole_concerne","dev")
								 ->where("hotline.pole_concerne","system")
								 ->where("date_debut",date("Y-m-01" , strtotime("-3 month")),"AND",false,">=")
								 ->where("hotline.id_user", $user)
								 ->addGroup("hotline.pole_concerne");
				$result = $this->select_all();

				$total = $result[0]["total"] + $result[1]["total"] + $result[2]["total"];
				$part = $total / 100;
				foreach ($result as $i) {
					$graph['categories']["category"][] = array("label"=>$i["hotline.pole_concerne"]);
					$graph['dataset'][$i["hotline.pole_concerne"]]["total"] = $i["total"] ;
					$pourcent = $i["total"]/$part;
					$graph['dataset'][$i["hotline.pole_concerne"]]["pourcentage"] = number_format($pourcent,2);
				}

				$nom = ATF::user()->select($user,'prenom');


				$graph['params']['caption'] = "Part des requêtes hotline <br /> pour ".$nom;

				return $graph;

			case "top10negatif":
				$result=ATF::societe()->societes_debitrices(15);
				foreach ($result as $i) {
					$graph['categories']["category"][] = array("label"=>"");
//					if (count($graph['categories']["category"])==10) {
//						break; // Pas plus de 10 sur le widget
//					}
				}
				$graph['params']['showLegend'] = "0";
				$graph['params']['bgAlpha'] = "0";
				$this->paramGraphe($dataset_params,$graph);

				$graph['dataset']["solde"]["params"] = array_merge($dataset_params,array(
					"seriesname"=>'solde'
				));
				foreach ($result as $i) {
					$graph['dataset']["solde"]['set'][$i["id_societe"]] = array(
						"value"=>$i['credits']
						,"alpha"=>100
						,"titre"=>$i["societe"]
						,"color"=>$i["credits"]<-100?"FF0033":($i["credits"]<-10?"FF6600":"FFFF00")
						,"link"=>urlencode("societe-select-".classes::cryptId($i["id_societe"]).".html")
					);
//					if (count($graph['dataset']["solde"]['set'])==10) {
//						break; // Pas plus de 10 sur le widget
//					}
				}
				return $graph;

			case "partTicket" :
				$this->q->reset()->addField("COUNT(hotline.id_hotline)", "total")
								 ->addField("hotline.pole_concerne")
								 ->where("hotline.pole_concerne","telecom")
								 ->where("hotline.pole_concerne","dev")
								 ->where("hotline.pole_concerne","system")
								 ->where("hotline.etat", "free")
								 ->where("hotline.etat", "fixing")
								 ->where("hotline.etat", "wait")
								 ->addGroup("hotline.pole_concerne");
				$result = $this->select_all();

				$total = $result[0]["total"] + $result[1]["total"] + $result[2]["total"];
				$part = $total / 100;
				foreach ($result as $i) {
					$graph['categories']["category"][] = array("label"=>$i["hotline.pole_concerne"]);
					$graph['dataset'][$i["hotline.pole_concerne"]]["total"] = $i["total"] ;
					$pourcent = $i["total"]/$part;
					$graph['dataset'][$i["hotline.pole_concerne"]]["pourcentage"] = number_format($pourcent,2);
				}

				$graph['params']['caption'] = "Part des requêtes hotline";
				return $graph;

			case "statCleodis":
				$MonthActu = date("m");
				$YearActu = date("Y");

				if($tu){
					$MonthActu = $tu["month"];
					$YearActu = $tu["year"];
				}
				$date = $this->getSemestre($MonthActu,$YearActu);

				for($i=0; $i<3;$i++){
						ATF::affaire()->q->reset()->where("affaire.id_societe",513)
										  ->where("affaire.affaire", "%Contrat de maintenance ".date("Y", strtotime($date[$i]["debut"]))."%","AND", false, "LIKE");
						$affaire_maintenance = ATF::affaire()->select_row();

						$this->q->reset()->where("id_societe" , 513)
										 ->addField("id_hotline")
										 ->where("facturation_ticket", "oui")
										 ->where("id_affaire",NULL,"AND","maintenance","IS NULL")
										 ->where("etat" , "done" , "OR" )
										 ->where("etat" , "payee" , "OR")
										 ->where("pole_concerne" , "dev")
										 ->where("date_debut",$date[$i]["debut"],"AND",false,">=")
										 ->where("date_debut",$date[$i]["fin"],"AND",false,"<=");
						$lesRequetes[1] = $this->sa();



						$this->q->reset()->where("id_societe" , 513)
										 ->addField("id_hotline")
										 ->where("facturation_ticket", "non")
										 ->where("id_affaire",$affaire_maintenance["affaire.id_affaire"],"AND","maintenance","=")
										 ->where("etat" , "done" , "OR" )
										 ->where("etat" , "payee" , "OR")
										 ->where("pole_concerne" , "dev")
										 ->where("date_debut",$date[$i]["debut"],"AND",false,">=")
										 ->where("date_debut",$date[$i]["fin"],"AND",false,"<=");
						$lesRequetes[2] = $this->sa();
						$temps = 0;


						$this->q->reset()->where("id_societe" , 513)
										 ->addField("id_hotline")
										 ->where("facturation_ticket", "non")
										 ->where("id_affaire",NULL,"AND","maintenance","IS NULL")
										 ->where("etat" , "done" , "OR" )
										 ->where("etat" , "payee" , "OR")
										 ->where("pole_concerne" , "dev")
										 ->where("date_debut",$date[$i]["debut"],"AND",false,">=")
										 ->where("date_debut",$date[$i]["fin"],"AND",false,"<=");
						$lesRequetes[0] = $this->sa();

						$temps = 0;

						$titre = array("Garantie" ,"Facture" , "CM" );
						for($j=0;$j<3;$j++){
							$temps = 0;
							foreach($lesRequetes[$j] as $k=>$v){
								ATF::hotline_interaction()->q->reset()->where("id_hotline" , $v["id_hotline"]);
								$interactions = ATF::hotline_interaction()->select_all();
								foreach ($interactions as $key => $value) {
									if($value["temps_passe"] !== "00:00:00"){
										$time = explode(":" , $value["temps_passe"] );
										$h = $time[0];
										$m = $time[1];


										$temps = $temps + (($h*60) + $m);
									}
								}
							}
							if($temps != 0){
								$result[$titre[$j]][$i] = array("semestre" => $date[$i]["debut"]." au ".$date[$i]["fin"], "duree" => $temps, "dureeH" => number_format($temps/60,2));
							}
						}
				}
				$result["titre"]= "Stats CLEODIS";
				$result["categories"]= $titre;
				$result["semestres"] = $date;
				return $result;
			break;


			case "waitmep" :
				$this->q->reset()
						->addField('hotline.id_user','id_user')
						->addField("sum(case hotline.urgence when 'bloquant' then 1  else 0 end)",'nb_bloquant')
						->addField("sum(case hotline.urgence when 'genant' then 1  else 0 end)",'nb_genant')
						->addField("sum(case hotline.urgence when 'detail' then 1  else 0 end)",'nb_detail')
						->addField("sum(IF(hotline.etat='wait' OR hotline.wait_mep='oui',0,1)*FLOOR(hotline.priorite+1))",'total')
						->setStrict()
						->addJointure("hotline","id_user","user","id_user",NULL,NULL,NULL,NULL,"left")

						->addCondition("hotline.wait_mep",'oui',"AND","nonFinie","=")
						->addCondition("hotline.etat",'payee',"AND","nonFinie","!=")
						->addCondition("hotline.etat",'annulee',"AND","nonFinie","!=")
						->addCondition("hotline.etat",'done',"AND","nonFinie","!=")
						->addGroup("hotline.id_user")
						->addOrder('total','desc');
				$result=parent::select_all();

				if ($widget) {
					foreach ($result as $i) {
						if ($i["id_user"]) {
							$nom = substr(ATF::user()->select($i["id_user"],'prenom'),0,1).substr(ATF::user()->select($i["id_user"],'nom'),0,1);
						}
						$graph['categories']["category"][] = array("label" => $nom);
					}

					$graph['params']['caption'] = "Charge actuelle en attente de MEP";
					$graph['params']['xaxisname'] = "Etat";
					$graph['params']['yaxisname'] = "Nombre de tickets";
				}

				/*parametres graphe*/
				$this->paramGraphe($dataset_params,$graph);

				$liste_etat=array('bloquant'=>"FF0033",'genant'=>"FF6600",'detail'=>"0000FF");

				foreach ($result as $val_) {
					foreach($liste_etat as $etat=>$couleur){
						if (!$graph['dataset'][$etat]) {
							$graph['dataset'][$etat]["params"] = array_merge($dataset_params,array(
								"seriesname"=>ATF::$usr->trans("urgence_".$etat,'hotline')
								,"color"=>$couleur
							));

							foreach ($result as $val_2) {
								$graph['dataset'][$etat]['set'][$val_2["id_user"]] = array("value"=>0,"alpha"=>100,"titre"=>ATF::$usr->trans("urgence_".$etat,'hotline')." : 0");
							}
						}
						$graph['dataset'][$etat]['set'][$val_["id_user"]] = array("value"=>$val_['nb_'.$etat],"alpha"=>100,"titre"=>ATF::$usr->trans("urgence_".$etat,'hotline')." : ".$val_['nb_'.$etat]);

					}
				}
				return $graph;

			default:
				$this->q->reset()
						->addField('hotline.id_user','id_user')
						->addField("sum(case hotline.urgence when 'bloquant' then 1  else 0 end)",'nb_bloquant')
						->addField("sum(case hotline.urgence when 'genant' then 1  else 0 end)",'nb_genant')
						->addField("sum(case hotline.urgence when 'detail' then 1  else 0 end)",'nb_detail')
						->addField("sum(IF(hotline.etat='wait' OR hotline.wait_mep='oui',0,1)*FLOOR(hotline.priorite+1))",'total')
						->setStrict()
						->addJointure("hotline","id_user","user","id_user",NULL,NULL,NULL,NULL,"left")

						->addCondition("hotline.wait_mep",'oui',"AND","nonFinie","!=")
						->addCondition("hotline.etat",'payee',"AND","nonFinie","!=")
						->addCondition("hotline.etat",'annulee',"AND","nonFinie","!=")
						->addCondition("hotline.etat",'done',"AND","nonFinie","!=")

		//				->addConditionNull("hotline.id_user","AND","userNul")
						->addCondition("hotline.etat",'free',"AND","userNul","=")

						->addSuperCondition("nonFinie,userNul")

						->addGroup("hotline.id_user")
						->addOrder('total','desc');
				$result=parent::select_all();

				if ($widget) {
					foreach ($result as $i) {
						if ($i["id_user"]) {
							$nom = substr(ATF::user()->select($i["id_user"],'prenom'),0,1).substr(ATF::user()->select($i["id_user"],'nom'),0,1);
						} else {
							$nom = "?";
						}
						$graph['categories']["category"][$i["id_user"]] = array("label" => $nom);
					}

					$graph['params']['showLegend'] = "0";
					$graph['params']['bgAlpha'] = "0";
				} else {
					foreach ($result as $i) {
						$graph['categories']["category"][$i["id_user"]] = array("label"=>ATF::user()->nom($i["id_user"]));
					}

					$graph['params']['caption'] = "Etat des Tickets Hotline de chaque personne";
					$graph['params']['xaxisname'] = "Etat";
					$graph['params']['yaxisname'] = "Nombre de tickets";
				}

				/*parametres graphe*/
				$this->paramGraphe($dataset_params,$graph);

				$liste_etat=array('bloquant'=>"FF0033",'genant'=>"FF6600",'detail'=>"0000FF");

				foreach ($result as $val_) {
					foreach($liste_etat as $etat=>$couleur){
						if (!$graph['dataset'][$etat]) {
							$graph['dataset'][$etat]["params"] = array_merge($dataset_params,array(
								"seriesname"=>ATF::$usr->trans("urgence_".$etat,'hotline')
								,"color"=>$couleur
							));

							foreach ($result as $val_2) {
								$graph['dataset'][$etat]['set'][$val_2["id_user"]] = array("value"=>0,"alpha"=>100,"titre"=>ATF::$usr->trans("urgence_".$etat,'hotline')." : 0");
							}
						}
						$graph['dataset'][$etat]['set'][$val_["id_user"]] = array("total"=>$val_['total'], "value"=>$val_['nb_'.$etat],"alpha"=>100,"titre"=>ATF::$usr->trans("urgence_".$etat,'hotline')." : ".$val_['nb_'.$etat]);

						/* ajout de l'url */
						$graph['dataset'][$etat]['set'][$val_["id_user"]]["link"]=urlencode("hotline.html,stats=1&label=".$val_["id_user"]);


					}
				}

				return $graph;
		}
	}

	public function getSemestre($MonthActu , $YearActu){
		$Y = $YearActu;
		$Y1 = $Y-1;
		if($MonthActu < 7){
			$date[0]["debut"] = date($Y1."-01-01");
			$date[1]["debut"] = date($Y1."-07-01");
			$date[2]["debut"] = date($Y."-01-01");
			$date[0]["fin"] = date($Y1."-06-30");
			$date[1]["fin"] = date($Y1."-12-31");
			$date[2]["fin"] = date($Y."-06-30");
			$date["semestre"][0] = "S1 ".$Y1;
			$date["semestre"][1] = "S2 ".$Y1;
			$date["semestre"][2] = "S1 ".$Y;
		}else{
			$date[0]["debut"] = date($Y1."-07-01");
			$date[1]["debut"] = date($Y."-01-01" );
			$date[2]["debut"] = date($Y."-07-01");
			$date[0]["fin"] = date($Y1."-12-31");
			$date[1]["fin"] = date($Y."-06-30");
			$date[2]["fin"] = date($Y."-12-31");
			$date["semestre"][0] = "S2 ".$Y1;
			$date["semestre"][1] = "S1 ".$Y;
			$date["semestre"][2] = "S2 ".$Y;
		}
		return $date;
	}

	public function statNbCloture(){
		$this->q->reset()
				->setStrict()
				->addCondition("hotline.id_societe","1","AND",false,"!=")
				->addCondition("hotline.id_societe","1154","AND",false,"!=")
				->addJointure("hotline","id_user","user","id_user")
				->addCondition("user.etat","normal")
				->addOrder("dif",'desc')
				->addCondition("DATE_ADD(hotline.date, INTERVAL 30 DAY)","'".date("Y-m-d 00:00:00")."'",NULL,false,">=",false,false,true)
				->addField('count(*)','dif')
				->addCondition("hotline.etat","done")
				->addCondition("hotline.etat","payee")
				->addField("user.nom","user")
				->addField("user.id_user","id_user")
				->addGroup("hotline.id_user");
		$result=parent::sa();

		$this->q->reset()
				->setStrict()
				->addCondition("hotline.id_societe","1")
				->addCondition("hotline.id_societe","1154")
				->addJointure("hotline","id_user","user","id_user")
				->addCondition("user.etat","normal")
				->addOrder("dif",'desc')
				->addCondition("DATE_ADD(hotline.date, INTERVAL 30 DAY)","'".date("Y-m-d 00:00:00")."'",NULL,false,">=",false,false,true)
				->addField('count(*)','dif')
				->addCondition("hotline.etat","done")
				->addCondition("hotline.etat","payee")
				->addField("user.nom","user")
				->addField("user.id_user","id_user")
				->addGroup("hotline.id_user");
		$resultAT=parent::sa();

		foreach ($result as $i) {
			$nom=ATF::user()->select($i["id_user"]);
			$graph['categories']["category"][$i['user']] = array("label"=>substr($nom['prenom'],0,1).substr($nom['nom'],0,1));
		}
		$graph['params']['showLegend'] = "0";
		$graph['params']['bgAlpha'] = "0";
		$graph['categories']['params']["fontSize"] = "12";

		$this->paramGraphe($dataset_params,$graph);

		foreach ($result as $val_) {
			if (!$graph['dataset']['dif']) {
				$graph['dataset']['dif']["params"] = array_merge($dataset_params,array(
					"seriesname"=>'dif'
					,"color"=>($cloture?"fbb632":"ff3636")
				));

				foreach ($result as $val_2) {
					if($val_['user']==$val_2['user'])$graph['dataset']['dif']['set'][$val_2['user']] = array("value"=>0,"value2"=>0,"alpha"=>100,"titre"=>$val_2['user']." : 0");
				}
			}
			$graph['dataset']['dif']['set'][$val_['user']] = array("value"=>$val_['dif'], "value2"=>0,"alpha"=>($val_['user']==ATF::$usr->get('nom')?100:50),"titre"=>$val_['user']." : ".$val_['dif']);
			foreach ($resultAT as $k=>$_) {
				if($_["user"] == $val_['user']){
					$graph['dataset']['dif']['set'][$val_['user']]["value2"] = $_["dif"];
				}
			}

		}
		return $graph;
	}

	public function getSecond($time){
		$timeArr = array_reverse(explode(":", $time));
		$seconds = 0;
		foreach ($timeArr as $key => $value)
		{
			if ($key > 2) break;
			$seconds += pow(60, $key) * $value;
		}
		return $seconds;

	}

	//hotline_requetebyUserParMois
	public function requetebyUser7joursGlissants($tu=NULL){

		$date = date('Y-m-d',strtotime('- 7 day'));

		$user = ATF::$usr->getID();
		if($tu){ $user = $tu;	}

		ATF::hotline_interaction()->q->reset()
				->setStrict()
				->where("hotline_interaction.id_user", $user)
				->addJointure("hotline","id_hotline","hotline_interaction","id_hotline")
				->where("hotline_interaction.date",$date."%","AND",false,">=");

		$result=ATF::hotline_interaction()->sa();

		$data = array();
		foreach ($result as $key => $value) {
			if(ATF::hotline()->select($value["id_hotline"], "facturation_ticket") == "oui" || ATF::hotline()->select($value["id_hotline"], "id_affaire")){
				$facture = ($value["credit_presta"] + $value["credit_dep"]) * 3600;
			}else{
				$facture = 0;
			}
			$temps_passe = $this->getSecond($value["duree_presta"])+$this->getSecond($value["duree_dep"])-$this->getSecond($value["duree_pause"]);


			$data[date("Y-m-d", strtotime($value["date"]))]["temps_passe"] += $temps_passe;
			$data[date("Y-m-d", strtotime($value["date"]))]["temps"] += $facture;
		}


		foreach ($data as $i=>$j) {
			$graph['categories']["category"][$i] = array("label"=>date("d/m", strtotime($i)));
		}
		$graph['params']['showLegend'] = "0";
		$graph['params']['bgAlpha'] = "0";
		$graph['categories']['params']["fontSize"] = "12";


		/*parametres graphe*/
		$this->paramGraphe($dataset_params,$graph);

		$liste_etat=array('temps_passe'=>"FF0033",'temps'=>"0000FF");

		foreach ($data as $key=>$val_){
			foreach($liste_etat as $etat=>$couleur){
				if (!$graph['dataset'][$etat]) {
					$graph['dataset'][$etat]["params"] = array_merge($dataset_params,array(
						"seriesname"=>ATF::$usr->trans($etat, $this->table)
						,"color"=>$couleur
					));

					foreach ($data as $k=>$val_2) {
						$graph['dataset'][$etat]['set'][$k] = array("value"=>0,"alpha"=>100,"titre"=>$etat." : 0 H");
					}
				}
				$graph['dataset'][$etat]['set'][$key] = array("value"=>number_format($val_[$etat]/3600 ,2),"alpha"=>100,"titre"=>ATF::$usr->trans($etat, $this->table)." : ".number_format($val_[$etat]/3600,2)." H");
			}
		}
		return $graph;
	}

	//Retourne le nbre de jours ouvrés entre 2 dates
	function getJoursOuvres($datedeb,$datefin){
		$nb_jours=0;
		$dated=explode('-',$datedeb);
		$datef=explode('-',$datefin);
		$timestampcurr=mktime(0,0,0,$dated[1],$dated[2],$dated[0]);
		$timestampf=mktime(0,0,0,$datef[1],$datef[2],$datef[0]);
		while($timestampcurr<$timestampf){

				  if((date('w',$timestampcurr)!=0)&&(date('w',$timestampcurr)!=6)){
					$nb_jours++;
				  }
			$timestampcurr=mktime(0,0,0,date('m',$timestampcurr),(date('d',$timestampcurr)+1)   ,date('Y',$timestampcurr));

		}
		return $nb_jours+1;
	}

	public function _requetebyUserParMois($get,$post){
		$moment = $get['moment'] == "now" ? date("Y-m") : -1;
		$at = $this->requetebyUserParMois($moment);
		ATF::define_db("db","extranet_v3_att");
		ATF::$codename = "att";
		$att = $this->requetebyUserParMois($moment);
		ATF::define_db("db","extranet_v3_absystech");
		ATF::$codename = "absystech";

		return array("at"=>$at, "att"=>$att, "infos"=>array("graph"=>"requetebyUserParMois"));

	}

	public function requetebyUserParMois($mois,$tu=false){
		if(ATF::$codename == "att"){ $exclusion = array(34,40); }
		else{ $exclusion = array(30,62,57,54); }

		if($mois < 0){
			$z = true;
			$mois = date('Y-m',strtotime($mois.' month'));
			$mois_conges = date('Y-m',strtotime($mois."-01 -1 month"));
			$conges_mois_prec = true;

			if($tu != false){
				$mois = $tu[0];
				$mois_conges = $tu[1];
			}
			$mois_conges_fin = $mois_conges_fin = date('Y-m-d',strtotime($mois."-01 +1 month"));

		}else{
			$mois_conges = date('Y-m',strtotime($mois."-01 -1 month"));
			$conges_mois_prec = false;
			if($tu != false){
				$mois = $tu[0];
				$mois_conges = $tu[1];
				$mois_conges_fin = date('Y-m-d',strtotime($mois_conges."-01 +1 month"));
			}
			$mois_conges_fin = date("Y-m-d");

		}

		ATF::hotline_interaction()->q->reset()
				->setStrict()
				->addJointure("hotline","id_hotline","hotline_interaction","id_hotline")
				->where("hotline_interaction.date",$mois."%","AND","mois","LIKE")
				->whereIsNotNull("hotline_interaction.id_user");
			foreach ($exclusion as $key => $value) {
				ATF::hotline_interaction()->q->where("hotline_interaction.id_user",$value,"AND","usr","!=");
			}
		$result=ATF::hotline_interaction()->sa();


		$data = array();
		foreach ($result as $key => $value) {
			$temps_passe = $this->getSecond($value["duree_presta"])+$this->getSecond($value["duree_dep"])-$this->getSecond($value["duree_pause"]);

			if($temps_passe != 0){
				$facturation_ticket = ATF::hotline()->select($value["id_hotline"], "facturation_ticket");

				if($facturation_ticket != "oui"){
					if(ATF::hotline()->select($value["id_hotline"], "id_affaire") && ATF::hotline()->select($value["id_hotline"], "charge") === "intervention" ){
						$facturation_ticket="oui";
					}else{
						$facturation_ticket="non";
					}
				}

				if(!$data[$value["id_user"]]["conges"]){
					ATF::conge()->q->reset()->addAllFields("conge")
											->where("conge.date_debut",$mois_conges."-01%","AND","mois",">=")
											->where("conge.date_debut",$mois_conges_fin."%","AND","mois","<")
											->where("conge.id_user",$value["id_user"],"AND")
											->where("conge.etat","nok","AND",false,"!=")
											->where("conge.etat","annule","AND",false,"!=");
					$conges = ATF::conge()->select_all();
					$data[$value["id_user"]]["conges"] = 0;


					if($conges){
						if($conges_mois_prec){
							foreach ($conges as $kconge => $vconge) {
								$jdeb = $mois."-01";
								if(strtotime($jdeb) - strtotime($vconge["conge.date_fin"])  < 0){
									if(strtotime($vconge["conge.date_debut"]) - strtotime($jdeb) > 0) $jdeb = $vconge["conge.date_debut"];
									$nbj = $this->getJoursOuvres($jdeb, $vconge["conge.date_fin"])*7;
									if($vconge["conge.periode"] == "am" || $vconge["conge.periode"] == "pm") $nbj = $nbj/2;
									$data[$value["id_user"]]["conges"] += $nbj;
								}
							}
						}else{
							foreach ($conges as $kconge => $vconge) {
								$jdeb = $mois."-01";
								$jfin = date("Y-m-d");

								if(strtotime($jdeb) - strtotime($vconge["conge.date_fin"])  < 0){
									if(strtotime($vconge["conge.date_debut"]) - strtotime($jdeb) > 0) $jdeb = $vconge["conge.date_debut"];
									if(strtotime($vconge["conge.date_fin"]) - strtotime($jfin) < 0) $jfin = $vconge["conge.date_fin"];

									$nbj = $this->getJoursOuvres($jdeb, $jfin)*7;
									if($vconge["conge.periode"] == "am" || $vconge["conge.periode"] == "pm") $nbj = $nbj/2;
									$data[$value["id_user"]]["conges"] += $nbj;
								}
							}

						}
					}
				}

				if(!$data[$value["id_user"]]["oui"]) $data[$value["id_user"]]["oui"] = 0;
				if(!$data[$value["id_user"]]["non"]) $data[$value["id_user"]]["non"] = 0;

				$data[$value["id_user"]][$facturation_ticket] += $temps_passe;
				$data[$value["id_user"]]["user"] = $value["id_user"];
			}
			$data[$value["id_user"]]["total"] += $temps_passe;
		}
		usort($data, function($a, $b) {	  return $b['oui'] - $a['oui'];	});




		foreach ($data as $i=>$j) {
			$nom=ATF::user()->select($j["user"]);
			$graph['categories']["category"][$j["user"]] = array("label"=>substr($nom['prenom'],0,1).substr($nom['nom'],0,1));
		}
		$graph['params']['showLegend'] = "0";
		$graph['params']['bgAlpha'] = "0";
		$graph['categories']['params']["fontSize"] = "12";
		$graph['params']["titre"] = "Tps passé ".ATF::$usr->date_trans($mois);


		$this->paramGraphe($dataset_params,$graph);

		$liste_etat=array('oui'=>"FF0033",'non'=>"0000FF","conges"=>"000");

		foreach ($data as $key=>$val_){
			foreach($liste_etat as $etat=>$couleur){
				if (!$graph['dataset'][$etat]) {
					$graph['dataset'][$etat]["params"] = array_merge($dataset_params,array(
						"seriesname"=>ATF::$usr->trans($etat, $this->table)
						,"color"=>$couleur
					));


					foreach ($data as $k=>$val_2) {
						$graph['dataset'][$etat]['set'][$val_2["user"]] = array("value"=>0,"alpha"=>100,"titre"=>$etat." : 0 H");
					}
				}
				if($etat == "conges"){
					$graph['dataset'][$etat]['set'][$val_["user"]] = array("value"=>number_format($val_[$etat] ,2),"alpha"=>100,"titre"=>ATF::$usr->trans($etat, $this->table)." : ".number_format($val_[$etat],2)." H");
				}else{
					$graph['dataset'][$etat]['set'][$val_["user"]] = array("value"=>number_format($val_[$etat]/3600 ,2),"alpha"=>100,"titre"=>ATF::$usr->trans($etat, $this->table)." : ".number_format($val_[$etat]/3600,2)." H");
				}

			}
		}

		return $graph;
	}


	/*
	* Permet l'affichage sur le graphe marge réelle sur tickets
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function graph_tarif_horaire($mois_deb,$mois_fin){

		if($mois_deb){ $date_deb = $mois_deb; }
		else { $date_deb = date("Y-01-01"); }

		if($mois_fin){ $date_fin = $mois_fin; }
		else { $date_fin = date("Y-m-d"); }

		$result=  ATF::hotline_interaction()->getInteractionIntoDate($date_deb, $date_fin);

		foreach ($result as $key => $value) {
			//$data[$value["id_hotline"]][$value["id_hotline_interaction"]] = $value;
			$users[$value["id_user"]][$value["id_hotline"]][$value["id_hotline_interaction"]] = $value;
		}

		$workedHour = $d = array();
		$workedHour[0] = $this->getJoursOuvres($date_deb, $date_fin);

		foreach ($users as $id_user => $data) {
			$return = ATF::hotline_interaction()->getDataMargeHotline($users[$id_user] ,$id_user, $workedHour);

			$d[$id_user]["marge_nette_affaire"] =
			$d[$id_user]["marge_nette_ticket"] =
			$d[$id_user]["marge_nette_total"] =
			$d[$id_user]["marge_nette_non_pointe"] = 0;

			foreach ($return as $k => $v) {
				$hotline = $this->select($v["id_hotline"]);
				if($hotline["id_affaire"]){
					$d[$id_user]["marge_nette_affaire"] += $v["marge_nette"];
				}else{
					$d[$id_user]["marge_nette_ticket"] += $v["marge_nette"];
				}
				$d[$id_user]["marge_nette_total"] += $v["marge_nette"];
			}
		}

		foreach ($users as $id_user => $data) {
			$temps_partiel = round(ATF::user()->select($id_user ,"temps_partiel"),2);
			$npt = round(($workedHour[0]*7*$temps_partiel)-$workedHour[$id_user],2);

			$d[$id_user]["marge_nette_non_pointe"] = 0 - round($npt*__COUT_HORAIRE_TECH__ ,2);
			$d[$id_user]["marge_nette_total"] += round($d[$id_user]["marge_nette_non_pointe"],2);
		}



		$data = $this->array_sort($d, 'marge_nette_total', SORT_DESC);

		foreach ($data as $i=>$j) {
			$nom=ATF::user()->select($i);
			$graph['categories']["category"][$i] = array("label"=>substr($nom['prenom'],0,1).substr($nom['nom'],0,1));
		}

		$graph['params']['showLegend'] = "0";
		$graph['params']['bgAlpha'] = "0";
		$graph['categories']['params']["fontSize"] = "12";
		$graph['params']["titre"] = "Marge nette pour ".date("Y");


		$this->paramGraphe($dataset_params,$graph);

		$liste_etat=array('ticket'=>"FF0033",'affaire'=>"0000FF","total"=>"0000FF","non_pointe"=>"0000FF");


		foreach ($data as $key=>$val_){
			foreach($liste_etat as $etat=>$couleur){
				if (!$graph['dataset']['set'][$etat]) {
					foreach ($data as $k=>$val_2) {
						$graph['dataset']['set'][$etat][$k] = array("value"=>0,"alpha"=>100,
															 "titre"=>"Marge nette : 0 €");
					}
				}
				$graph['dataset']['set'][$etat][$key] = array("value"=>number_format($val_["marge_nette_".$etat] ,2, ".",""),"alpha"=>100,
													   "titre"=>"Marge nette : ".number_format($val_["marge_nette_".$etat],2, "."," ")." €");
			}
		}
		unset($graph["dataset"]["params"]);
		return $graph;

	}




	//Simple function to sort an array by a specific key. Maintains index association.
	function array_sort($array, $on, $order=SORT_ASC){
		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
			switch ($order) {
				case SORT_ASC:  asort($sortable_array);
				break;
				case SORT_DESC: arsort($sortable_array);
				break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}

	public function getTauxHorraire($id_affaire){
		$marge_brute = 0;
		ATF::facture()->q->reset()->where("id_affaire",$id_affaire);
		$res = ATF::facture()->select_all();
		//Si j'ai des factures
		if($res){
			ATF::bon_de_commande()->q->reset()->where("id_affaire",$id_affaire);
			$bdcs = ATF::bon_de_commande()->select_all();
			$pbdc = 0;
			foreach ($bdcs as $k => $v) {
				$pbdc+=$v["prix"];
			}

			foreach ($res as $key => $value) {
				$marge_brute += $value["facture.prix"];
			}
			$marge_brute = round(($marge_brute - $pbdc),2);
		}else{
			//Si j'ai pas de factures
			ATF::devis()->q->reset()->where("id_affaire", $id_affaire)
									->addOrder("revision","desc");
			$devis = ATF::devis()->select_row();

			$marge_brute = round(($devis["prix"] - $devis["prix_achat"]),2);
		}

		ATF::hotline_interaction()->q->reset()
				->setStrict()
				->whereIsNotNull("hotline_interaction.id_user")
				->from("hotline_interaction",'id_hotline',"hotline","id_hotline")
				->where("hotline.id_affaire",$id_affaire);

		$res=ATF::hotline_interaction()->select_all();

		$tps = $temps_passe = 0;

		foreach ($res as $k => $v) {
			$temps_passe = round($this->getSecond($v["duree_presta"])/3600,2)
						  +round($this->getSecond($v["duree_dep"])/3600,2)
						  -round($this->getSecond($v["duree_pause"])/3600,2);
			if($temps_passe != 0){
				$tps += $temps_passe;
			}
		}

		return  round($marge_brute/$tps,2);

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

		if (ATF::_r('label')) {
			$this->setConditionFiltre($donnees,$this->table.".id_user",'LIKE',ATF::user()->nom(ATF::_r('label')),0);

			$liste_etat=array(1=>'payee',2=>'annulee',3=>'done');
			foreach($liste_etat as $cle=>$etat){
				$this->setConditionFiltre($donnees,"hotline.etat",'!=',$etat,$cle);
			}
		} else {
			$this->setConditionFiltre($donnees,$this->table.".etat",'=','free',0);
		}

		$insertion=array("filtre_optima"=>$donnees['name']
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

	//************************MAILS*****************************/

	/**
	* Créé une notice classique
	* La méthode s'occupe de tout ! il suffit de mettre l'expression non traduite
	* @param strin $msg l'expression non traduite
	*/
	public function createNotice($msg){
		$notice=ATF::$usr->trans($msg,$this->table);
		ATF::$msg->addNotice($notice);
	}

	/**
	* Crée une notice pour les mails
	* La méthode s'occupe de tout ! il suffit de mettre l'expression non traduite
	* @param strin $msg l'expression non traduite
	*/
	public function createMailNotice($msg){
		$notice='<img src="'.ATF::$staticserver.'images/icones/email.png" height="16" width="16" alt="" />';
		$notice.="&nbsp;";
		$notice.=ATF::$usr->trans($msg,$this->table);
		ATF::$msg->addNotice($notice);
	}

	//************************DIVERS*****************************/

	/**
	* Créé une interaction pour des mouvements de hotline donné. Cela peut servir à tracer les modifications sur la hotline dans le fil des interactions.
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param int $id_hotline l'identifiant de la hotline
	* @param string $msg Le message de l'interaction
	*/
	private function createInternalInteraction($id_hotline,$msg,$visible="non"){
		//Insère une interaction d'information
		//internal permet de bypassé la restriction de temps sur hotline_interaction
		$inter=array("id_hotline"=>$this->decryptId($id_hotline)
					,"detail"=>$msg
					,"id_user"=>ATF::$usr->getId()
					,"visible"=>$visible
					,"internal"=>true
					);
		return ATF::hotline_interaction()->insert($inter);
	}

	/** Liste les hotlines selon la priorité passée en paramètre (et éventuellement un user)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function listeHotline($infos){
		$this->q->reset()->addField("count(*)","nbr_hotline")
						->addField("priorite")
						->addCondition("priorite",$infos["priorite"]-2)
						->addCondition("priorite",$infos["priorite"]-1)
						->addCondition("priorite",$infos["priorite"])
						->addCondition("priorite",$infos["priorite"]+1)
						->addCondition("priorite",$infos["priorite"]+2)
						->addCondition("hotline.etat",'payee',"AND","nonFinie","!=")
						->addCondition("hotline.etat",'annulee',"AND","nonFinie","!=")
						->addCondition("hotline.etat",'done',"AND","nonFinie","!=")
						->addGroup('priorite')
						->addOrder('priorite','asc');

		if($infos['user']){
			$this->q->addCondition("id_user",ATF::user()->decryptId($infos['user']));
		}

		//on liste les hotlines en fonction des priorités à + ou - 2 de celle passée en paramètre
		$liste_date=array(
							$infos['priorite']-2=>0
							,$infos['priorite']-1=>0
							,$infos['priorite']=>0
							,$infos['priorite']+1=>0
							,$infos['priorite']+2=>0
		);

		foreach(parent::select_all() as $key=>$item){
			$liste_date[$item["priorite"]]=$item['nbr_hotline'];
		}

		$liste[]=$liste_date;

		return $liste;
	}

	/**
	* Méthode ajax pour appeler les hotlines
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function rpcGetRecentForMobile($infos){
		ATF::$cr->block("top");
		if ($infos["limit"]) {
			return $this->getRecentForMobile($infos["countUnseenOnly"],$infos["limit"]);
		}
		return $this->getRecentForMobile($infos["countUnseenOnly"]);
	}

	/**
	* Retourne les hotlines ayant eu lieues depuis la dernière activité
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function getRecentForMobile($countUnseenOnly=false,$limit=42){
		$this->q->reset()
			->setLimit($limit)
			->unsetCount()
			->addField('hotline.hotline')
			->addField('hotline.pole_concerne')
			->addField('hotline.id_societe')
			->addField('hotline_interaction.id_user','intervenant_user')
			->addField('hotline_interaction.id_contact','intervenant_contact')
		;

		// Ne voir que les ticket des poles qui concernent l'utilisateur
		foreach (explode(",",ATF::$usr->get('pole')) as $pole) {
			$this->q->where("pole_concerne",$pole,"OR","pole");
		}

		if ($countUnseenOnly) {
			$this->q->setCountOnly()
				->andWhere("hotline_interaction.date",ATF::$usr->last_activity,"date",">");
			return $this->saCustom();
		} else {

			$return = $this->saCustom();
			foreach ($return as $k=>$i) {
				if ($return[$k]["intervenant_user"]) {
					$return[$k]["intervenant"] = ATF::user()->nom($return[$k]["intervenant_user"]);
				} else {
					$return[$k]["intervenant"] = ATF::contact()->nom($return[$k]["intervenant_contact"]);
				}
				$return[$k]["contact"] = ATF::contact()->nom($return[$k]["id_contact"]);
				$return[$k]["humanDate"] = ATF::$usr->date_trans(substr($return[$k]["date_last_interaction"],0,10),true,false,true);
				$return[$k]["heure"] = date("H\hi",strtotime($return[$k]["date_last_interaction"]));
				$return[$k]["indexSectionDate"] = date("y-m-d",strtotime($return[$k]["date_last_interaction"]));
				if (!ATF::$usr->last_activity || $return[$k]["date_last_interaction"] > ATF::$usr->last_activity) {
					$return[$k]["indexSectionDate"] = "";
					$return[$k]["humanDate"] = "=> ".ATF::$usr->trans("unseen");
				}
			}

			return util::cleanForMobile($return);
		}
	}

	/**
	* Méthode ajax pour récupérer des interactions
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function rpcGetInteractionsForMobile($infos){
		ATF::$cr->block("top");
		if ($infos["id"]) {
			$this->q->reset();
			return $this->getInteractionsForMobile($infos["id"]);
		}
	}

	/**
	* Retourne les hotlines ayant eu lieues depuis la dernière activité
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function getInteractionsForMobile($id_hotline){
		ATF::hotline_interaction()->q->reset()
//			->addField('id_user')
//			->addField('id_contact')
//			->addField('date')
//			->addField('detail')
			->where("id_hotline",$id_hotline);
		$return=ATF::hotline_interaction()->select_all();
		foreach ($return as $k=>$i) {
			$return[$k]["user"] = ATF::user()->nom($return[$k]["id_user"]);
			$return[$k]["contact"] = ATF::contact()->nom($return[$k]["id_contact"]);
			$return[$k]["humanDate"] = ATF::$usr->date_trans(substr($return[$k]["date"],0,10),true,false,true);
			$return[$k]["indexSectionDate"] = date("y-m-d",strtotime($return[$k]["date"]));
			$return[$k] = array_map("strip_tags",$return[$k]);
			$return[$k] = array_map("html_entity_decode",$return[$k]);
		}
		return util::cleanForMobile($return);
	}

	/** Récupère les éléments nécessaires à l'affichage de graphe de temps (de prise en charge et de cloture)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function statsTps($widget=false,$par_user=false,$cloture=false,$count=false){
		$this->q->reset()
				->setStrict()
				->addCondition("hotline.id_societe","1","AND",false,"!=")
				->addCondition("hotline.id_societe","1154","AND",false,"!=")
				->addJointure("hotline","id_user","user","id_user")
				->addCondition("user.etat","normal");

		if($widget){
			if($count){
				$this->q->addOrder("dif",'desc')
						->addCondition("DATE_ADD(hotline.date, INTERVAL 30 DAY)","'".date("Y-m-d 00:00:00")."'",NULL,false,">=",false,false,true);
			}else{
				$this->q->addOrder("dif",'desc')
						//->addCondition("DATE_FORMAT(hotline.date,'%Y')",date("Y"));
						->addCondition("DATE_ADD(hotline.date, INTERVAL 365 DAY)","'".date("Y-m-d 00:00:00")."'",NULL,false,">=",false,false,true);
			}
		}else{
			$this->q->addCondition("DATE_FORMAT(hotline.date,'%Y')","2010","OR",false,">=")
					->addGroup("DATE_FORMAT(hotline.date,'%Y %M')")
					->addOrder("hotline.date",'asc')
					->addField("DATE_FORMAT(hotline.date,'%Y %M')","hotdate");
		}

		if($cloture){
			if($count){
				$this->q->addField('count(*)','dif')
						->addCondition("hotline.etat","done")
						->addCondition("hotline.etat","payee");
			}else{
				$this->q->addField('ROUND(AVG(TIMESTAMPDIFF(HOUR,hotline.date_debut,hotline.date_fin))/24,2)','dif')
						->addOrder("dif",'asc')
						->addConditionNotNull("hotline.date_debut")
						->addConditionNotNull("hotline.date_fin");
			}
		}else{
			$this->q->addField('ROUND(AVG(TIMESTAMPDIFF(HOUR,hotline.date,hotline.date_debut))/24,2)','dif')
					->addOrder("dif",'asc')
					->addConditionNotNull("hotline.date")
					->addConditionNotNull("hotline.date_debut");
		}

		if($par_user){
			$this->q
					->addField("user.nom","user")
					->addField("user.id_user","id_user")
					->addGroup("hotline.id_user");
			if(!$widget){
				foreach($this->liste_user as $id_user=>$check){
					if($check){
						$this->q->addCondition("hotline.id_user",$id_user,"OR","hot_id_user");
					}
				}
			}
		}

		$result=parent::sa();



		if($widget){
			foreach ($result as $i) {
				$nom=ATF::user()->select($i["id_user"]);
				$graph['categories']["category"][$i['user']] = array("label"=>substr($nom['prenom'],0,1).substr($nom['nom'],0,1));
			}
			$graph['params']['showLegend'] = "0";
			$graph['params']['bgAlpha'] = "0";
			$graph['categories']['params']["fontSize"] = "12";
		}else{
			foreach ($result as $i) {
				$graph['categories']["category"][$i['hotdate']] = array("label"=>$i['hotdate']);
			}
		}

		/*parametres graphe*/
		$this->paramGraphe($dataset_params,$graph);

		if($widget){
			foreach ($result as $val_) {
				if (!$graph['dataset']['dif']) {
					$graph['dataset']['dif']["params"] = array_merge($dataset_params,array(
						"seriesname"=>'dif'
						,"color"=>($cloture?"fbb632":"ff3636")
					));

					foreach ($result as $val_2) {
						if($val_['user']==$val_2['user'])$graph['dataset']['dif']['set'][$val_2['user']] = array("value"=>0,"alpha"=>100,"titre"=>$val_2['user']." : 0");
					}
				}
				$graph['dataset']['dif']['set'][$val_['user']] = array("value"=>$val_['dif'],"alpha"=>($val_['user']==ATF::$usr->get('nom')?100:50),"titre"=>$val_['user']." : ".$val_['dif']);
			}
		}elseif($par_user){
			foreach ($result as $val_) {
				if (!$graph['dataset'][$val_['user']]) {
					$graph['dataset'][$val_['user']]["params"] = array_merge($dataset_params,array(
						"seriesname"=>$val_['user']
						,"color"=>dechex(rand(0,16777216))
					));

					foreach ($result as $val_2) {
						$graph['dataset'][$val_['user']]['set'][$val_2['hotdate']] = array("value"=>0,"alpha"=>100,"titre"=>$val_['user']." : 0");
					}
				}
				$graph['dataset'][$val_['user']]['set'][$val_['hotdate']] = array("value"=>$val_['dif'],"alpha"=>100,"titre"=>$val_['user']." : ".$val_['dif']);
			}
		}else{
			foreach ($result as $val_) {
				if (!$graph['dataset']['tps_moyen']) {
					$graph['dataset']['tps_moyen']["params"] = array_merge($dataset_params,array(
						"seriesname"=>ATF::$usr->trans('tps_moyen','stats')
						,"color"=>"006600"
					));

					foreach ($result as $val_2) {
						$graph['dataset']['tps_moyen']['set'][$val_2['hotdate']] = array("value"=>0,"alpha"=>100,"titre"=>$val_2['hotdate']." : 0");
					}
				}
				$graph['dataset']['tps_moyen']['set'][$val_['hotdate']] = array("value"=>$val_['dif'],"alpha"=>100,"titre"=>$val_['hotdate']." : ".$val_['dif']);
			}
		}

		return $graph;
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
			->addJointure("user","id_user","hotline","id_user",NULL,NULL,NULL,NULL,"inner")
			->addCondition('user.etat','normal')
			->addConditionNotNull('id_profil')
			->addGroup('id');

		foreach(ATF::user()->sa() AS $tab){
			$r[$tab['id']]  = 1;
		}
		return $r;
	}

	/**
	 * Permet de checker la boite mail zimbra, recuperer les mails et inserer les interactions dans la BDD
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 *
	*/
	public function checkMailBox($mail, $host, $port, $password){

		ATF::imap()->init($host, $port, $mail, $password);
		//ATF::imap()->init($host, $port, $mail, $password, "INBOX/tmp");
		if (ATF::imap()->error) {
			throw new errorATF(ATF::imap()->error);
		}
		$mails = ATF::imap()->imap_fetch_overview('1:*');

		if (is_array($mails)) {
			foreach ($mails as $val) {

				$pattern = "/optima-hotline-([a-z]*)-([0-9a-f]{32})-([0-9a-f]{32})@absystech-speedmail.com/";

				if (strpos(str_replace(" ","",$val->to),"optima-hotline-".ATF::$codename."-")!==false){
					preg_match($pattern , $val->to, $ids);

					$codename = $ids[1];
					$idhotline = $ids[2];
					$idcontact = $ids[3];

					$id_hotline = ATF::hotline()->decryptId($idhotline);


					$from = explode("<",$val->from);
					if($from[1]){ $from = substr($from[1], 0, -1);	}
					else{ $from = $from[0]; }


					ATF::user()->q->reset()->where("email",$from);
					$user = ATF::user()->select_row();

					if(is_array($user)){
						$id_user = $user["id_user"];
					}else{
						$id_user = NULL;
					}


					$id_contact = ATF::contact()->decryptId($idcontact);

					$this->q->reset()->where("id_hotline",$id_hotline);
					$res = $this->select_row();

					//Si le ticket hotline existe
					if(is_array($res)){
						$date = date("Y-m-d H:i", strtotime($val->date));
						$body =  ATF::imap()->returnBody($val->uid);

						//$position = strpos($body, utf8_encode("<---- Pour répondre par email, écrire au-dessus de cette ligne ---->"));
						//$message = substr($body, 0, $position);

						$message = substr($body, 0, 512);

						//$message = False;
						if($message){
							if($id_user = $user["id_user"]){
								$usr=ATF::$usr;
								ATF::$usr=new usr($user["id_user"]);
								$id_contact = NULL;
							}


							if(($res["etat"] == "payee") || ($res["etat"] == "annulee")){

								//Requête cloturée ou annulée donc pas d'interaction !!
								$info_mail["objet"] = "Requête ".$id_hotline." déja cloturée ";
								$info_mail["from"] = "optima-hotline@absystech.net";
								$info_mail["html"] = false;
								$info_mail["template"] = 'hotline_deja_cloture';
								$info_mail["text"] = $id_hotline;
								$info_mail["recipient"] = $from;


								$this->facture_mail = new mail($info_mail);
								$this->facture_mail->send();

								ATF::imap()->imap_mail_move( $val->uid, "Cloture" );


							}else{


								$interaction = array("id_hotline" =>  $id_hotline,
													 "date" => $date,
													 "duree_presta" => "00:00",
													 'no_test_credit'=>true,
													 'heure_debut_presta'=>date("H:i"),
													 'heure_fin_presta'=>date("H:i"),
													 "detail" => $message,
													 "id_user" => $id_user,
													 "id_contact" => $id_contact,
													 "id_ordre_de_mission" => NULL,
													 "visible" => "oui");

								$id = ATF::hotline_interaction()->insert($interaction);

								$mail = ATF::imap()->returnmail($val->uid);
								file_put_contents(ATF::hotline_interaction()->filepath($id,".eml",true), $mail);

								$zip = new ZipArchive();
								$zipFileName = ATF::hotline_interaction()->filepath($id,"fichier_joint");
								if (!file_exists($zipFileName)) {
									util::file_put_contents($zipFileName,""); // Nécessaire pour créer le fichier avant de l'open
								}

								$zip->open($zipFileName);
								$zip->addFile(ATF::hotline_interaction()->filepath($id,".eml",true),"mail.eml");
								$zip->close();
							}

							ATF::imap()->imap_delete($val->uid);

							if($id_user = $user["id_user"]){
								$usr=ATF::$usr;
								ATF::$usr=new usr();
							}
						}
					}

				}
			}
		}
		ATF::imap()->imap_expunge();
		return true;
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
	* Permet de récupérer la liste des tickets hotline pour telescope
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	public function _GET($get,$post) {

		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "id_hotline";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit'] && !$get['no-limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;
		if ($get['no-limit']) $get['page'] = false;

		$colsData = array(
			"hotline.id_hotline"=>array(),
			"hotline.date"=>array(),
			"hotline.date_modification"=>array(),
			"hotline.id_societe"=>array("visible"=>false),
			"hotline.id_contact"=>array(),
			"hotline.id_gep_projet"=>array(),
			"hotline.id_user"=>array(),
			"hotline.hotline"=>array(),
			"hotline.pole_concerne"=>array(),
			"hotline.visible"=>array(),
			"hotline.urgence"=>array(),
			"hotline.detail"=>array(),
			"hotline.etat"=>array(),
			"hotline.id_affaire"=>array(),
			"hotline.ok_facturation"=>array(),
			"hotline.charge"=>array(),
			"hotline.facturation_ticket"=>array(),
			"hotline.wait_mep"=>array(),
			"societe.latitude"=>array(),
			"societe.longitude"=>array(),
			"societe.solde"=>array()
		);


		$this->q->reset();

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			$this->q->setSearch($get["search"]);
		}

		if ($get['id']) {
			$this->q->where("id_hotline",$get['id'])->setLimit(1);
		} elseif ($get['id_societe']) {
			$this->q->where("hotline.id_societe",$get['id_societe']);
			if (!$get['no-limit']) $this->q->setLimit($get['limit']);
		} else {
			// Filtre EXCLUSIF ET NON EXCLUSIF
			// Filtre non traité
			if ($get['filters']['free'] == "on") {
				$this->q->where("hotline.etat","free");
			} else {
				// Filtre ticket actif
				if ($get['filters']['fixing'] == "on") {
					$this->q->where("hotline.etat","fixing")->where("hotline.etat","wait");
				}
				// Filtre MES tickets
				if ($get['filters']['mine'] == "on") {
					$this->q->where("hotline.id_user",ATF::$usr->getId());
				}

				// Filtre Facturé
				if ($get['filters']['facture'] == "on") {
					$this->q->where("hotline.facturation_ticket","oui","OR","facturation");
				}
				// Filtre NON Facturé
				if ($get['filters']['nfacture'] == "on") {
					$this->q->where("hotline.facturation_ticket","non","OR","facturation");
				}
				$this->q->whereIsNull("hotline.facturation_ticket","OR","facturation");

				// Filtre Sur affaire
				if ($get['filters']['afffacture'] == "on") {
					$this->q->whereIsNotNull("hotline.id_affaire","OR","facturation");
				} else {
					$this->q->whereIsNull("hotline.id_affaire");
				}
			}
			// AUtre filtre - fitlres indépendant
			if ($get['filters']['dev'] == "on") {
				$this->q->where("hotline.pole_concerne","dev","OR","pole");
			}
			if ($get['filters']['system'] == "on") {
				$this->q->where("hotline.pole_concerne","system","OR","pole");
			}
			if ($get['filters']['telecom'] == "on") {
				$this->q->where("hotline.pole_concerne","telecom","OR","pole");
			}


			// TRI
			switch ($get['tri']) {
				case 'id_societe':
				case 'id_user':
				case 'id_contact':
				case 'date':
					$get['tri'] = "hotline.".$get['tri'];
				break;
			}

			if (!$get['no-limit']) $this->q->setLimit($get['limit']);

		}

		$this->q->addField($colsData);

		$this->q->from("hotline","id_contact","contact","id_contact");
		$this->q->from("hotline","id_societe","societe","id_societe");
		$this->q->from("hotline","id_user","user","id_user");
		$this->q->from("hotline","id_gep_projet","gep_projet","id_gep_projet");
		$this->q->from("hotline","id_affaire","affaire","id_affaire");

		// $this->q->setToString();
		// log::logger($this->select_all($get['tri'],$get['trid'],$get['page'],true),"qjanon");
		// $this->q->unsetToString();

		$data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);

		foreach ($data["data"] as $k=>$lines) {
			foreach ($lines as $k_=>$val) {
				if (strpos($k_,".")) {
					$tmp = explode(".",$k_);
					$data['data'][$k][$tmp[1]] = $val;
					unset($data['data'][$k][$k_]);
				}
			}
		}

		if ($get['id']) {
			$data['data'][0]['facturation-indicateur'] = $this->getBillingMode($get['id'],true);

			$return = $data['data'][0];

			// Check PJ
			$return["pj"] = file_exists($this->filepath($get['id'],"fichier_joint"));
			$return["idc"] = $this->cryptId($get['id']);

		} else {
			// Envoi des headers
			header("ts-total-row: ".$data['count']);
			if ($get['limit']) header("ts-max-page: ".ceil($data['count']/$get['limit']));
			if ($get['page']) header("ts-active-page: ".$get['page']);
			if ($get['no-limit']) header("ts-no-limit: 1");

	  $return = $data['data'];
		}

		return $return;
	}

	/**
	* Permet d'insérer un ticket hotline depuis telescope
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param $get array Argument obligatoire mais inutilisé ici.
	* @param $post array COntient les données envoyé en POST par le formulaire.
	* @return boolean|integer Renvoi l'id de l'enregitrement inséré ou false si une erreur est survenu.
	*/
	public function _POST($get,$post,$files) {
		$return = array();

		try {
			if (!$post) throw new Exception("POST_DATA_MISSING",1000);
			// Check des champs obligatoire
			if (!$post['id_societe']) throw new Exception("ID_SOCIETE_MISSING",1020);
			if (!$post['id_contact']) throw new Exception("ID_CONTACT_MISSING",1021);
			if (!$post['hotline']) throw new Exception("TITLE_MISSING",1022);
			if (!$post['detail']) throw new Exception("CONTENT_MISSING",1023);
			if (!$post['pole']) throw new Exception("POLE_MISSING",1024);

			// Mapping pour BDD Optima
			$post['pole_concerne'] = $post['pole']; unset($post['pole']);
			$post['id_gep_projet'] = $post['id_projet']; unset($post['id_projet']);
			$post['visible'] = $post['visible']=='on'?"oui":"non";

			$post["filestoattach"]["fichier_joint"] = true; // Paramètre Optima pour préciser de prendre en compte les fichier joint lors de l'insertion

			// Insertion
			$return['id'] = self::insert($post);

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

	private function forward($infos) {

		ATF::db($this->db)->begin_transaction();

		try {
			if ($infos['type'] == 'pole') {
				$id_hotline_interaction = ATF::hotline_interaction()->insert(array(
					"detail"=>"Requête transférée par ".ATF::user()->nom($infos["id_user"])." au pôle ".ATF::$usr->trans($infos['val'],"hotline_pole_concerne"),
					"internal"=>true,
					"visible"=>"oui",
					"duree_presta"=>"00:05",
					"id_user"=>$infos["id_user"],
					"id_hotline"=>$infos['id_hotline']
				));
				$return['interaction'] = ATF::hotline_interaction()->select($id_hotline_interaction);


				ATF::hotline()->update(array(
					"id_hotline"=>$infos["id_hotline"],
					"pole_concerne"=>$infos["val"],
					"disabledInternalInteraction"=>true
				));

				//Récupération de l'email du nouveau utilisateur en charge
				$email="hotline.".$infos["val"]."@absystech.fr";

				ATF::hotline_mail()->createMailPoleTransfert($infos["id_hotline"],$id_hotline_interaction,$email);
				ATF::hotline_mail()->sendMail();

				//Notice
				$this->createMailNotice("hotline_transfert_pole");

			} else if ($infos['type'] == 'user') {
				$id_hotline_interaction = ATF::hotline_interaction()->insert(array(
					"detail"=>"Requête transférée par ".ATF::user()->nom($infos["id_user"])." à ".ATF::user()->nom($infos['val']),
					"internal"=>true,
					"visible"=>"oui",
					"duree_presta"=>"00:05",
					"id_user"=>$infos["id_user"],
					"id_hotline"=>$infos['id_hotline']
				));
				$return['interaction'] = ATF::hotline_interaction()->select($id_hotline_interaction);
				$return['new_user'] = ATF::user()->nom($infos['val']);

				//Récupération du pôle de l'utilisateur
				$pole=explode(",",ATF::user()->select($infos["val"],"pole"));
				$return['new_pole'] = ((is_array($pole) && isset($pole[0]) && !empty($pole[0]))?$pole[0]:"dev");


				ATF::hotline()->update(
					array(
						"id_hotline"=>$infos["id_hotline"]
						,"id_user"=>$infos["val"]
						,"pole_concerne"=>$return['new_pole']
						,"disabledInternalInteraction"=>true)
				);

				//Mise à jour de l'état
				ATF::hotline()->update(array("id_hotline"=>$infos["id_hotline"],"etat"=>"fixing","disabledInternalInteraction"=>true));
				//Récupération de l'email du nouvel utilisateur en charge
				$email=ATF::user()->select($infos["val"],"email");

				ATF::hotline_mail()->createMailUserTransfert($infos["id_hotline"],$id_hotline_interaction,$email);
				ATF::hotline_mail()->sendMail();

				//Notice
				ATF::hotline()->createMailNotice("hotline_transfert_user");
			}
		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
			throw $e;
		}

		$return['notices'] = ATF::$msg->getNotices();
		$return['result'] = true;
		ATF::db($this->db)->commit_transaction();
		return $return;
	}

	/**
	* Permet de modifier un ticket hotline depuis telescope
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

			// SI on fait une demande de mise en prod, une mise en attente ou tout autre action spécifique
			if ($post['specialAction']) {
				switch ($post['specialAction']) {
					case "forward":
						if (!$post['id_hotline']) throw new Exception("ID_HOTLINE_MISSING",1019);
						$action = $post['specialAction'];
						$return = self::$post['specialAction']($post);
						$lastInteractionRequired = true;
					break;
					case "setPriorite":
						if (!$post['id_hotline']) throw new Exception("ID_HOTLINE_MISSING",1019);
						$action = $post['specialAction'];
						self::$action($post);
						$return['result'] = true;
					break;
					case "takeRequest":
					case "cancelRequest":
					case "resolveRequest":
						if (!$post['id_hotline']) throw new Exception("ID_HOTLINE_MISSING",1019);
						$action = $post['specialAction'];
						self::$action($post);
						$return['result'] = true;
						$lastInteractionRequired = true;
						if ($post['specialAction']=="takeRequest") $return['user-in-charge'] = ATF::user()->nom(ATF::$usr->getId());

					break;
					case "setWait":
						if (!$post['id_hotline']) throw new Exception("ID_HOTLINE_MISSING",1019);
						if ($post['etat']=="wait") {
							$action = $post['specialAction'];
							self::$action($post);
						} else {
							$this->fixingRequest($post);
						}
						$return['result'] = true;
						$lastInteractionRequired = true;
					break;
					case "setBillingMode":
						if (!$post['id_hotline']) throw new Exception("ID_HOTLINE_MISSING",1019);
						self::setBillingModeNew($post);
						$return['result'] = $this->getBillingMode($post['id_hotline'],true);
						$lastInteractionRequired = true;
					break;
					case "sendMailTeamviewer":
						if (!$post['id_contact']) throw new Exception("ID_CONTACT_MISSING",1024);
						ATF::contact()->sendMailTeamViewer($post);
					break;
					case "sendMEP":
						if (!$post['id_hotline']) throw new Exception("ID_HOTLINE_MISSING",1019);
						if (!$post['action']) throw new Exception("ACTION_MISSING",1030);
						$action = $post["action"];
						$this->$action($post);
						$lastInteractionRequired = true;
					break;
				}
			// Si on fait un update pur et simple du ticket
			} else {
				// Check des champs obligatoire
				if (!$post['id_hotline']) throw new Exception("ID_HOTLINE_MISSING",1019);
				if (!$post['id_societe']) throw new Exception("ID_SOCIETE_MISSING",1020);
				if (!$post['id_contact']) throw new Exception("ID_CONTACT_MISSING",1021);
				if (!$post['hotline']) throw new Exception("TITLE_MISSING",1022);
				if (!$post['detail']) throw new Exception("CONTENT_MISSING",1023);

				// Mapping pour BDD Optima
				$post['pole_concerne'] = $post['pole']; unset($post['pole']);
				$post['id_gep_projet'] = $post['id_projet']; unset($post['id_projet']);
				$post['visible'] = $post['visible']=='on'?"oui":"non";

				// Insertion
				$return['aff'] = self::update($post);
				$return['result'] = true;
			}

			// last itneraction
			if ($lastInteractionRequired) {
				$p = array("limit"=>1,"id_hotline"=>$post['id_hotline']);
						$return['interaction'] = ATF::hotline_interaction()->_GET($p)[0];
			}

			// Récupération des notices créés
			$return['notices'] = ATF::$msg->getAllNotices();
			return $return;
		} catch (error $e) {
			throw $e;
		} catch (Exception $e) {
			throw $e;
		}
		return false;
	}

	/**
	* Permet de supprimer un ticket hotline
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param $get array contient l'id a l'index 'id'
	* @param $post array vide
	* @return array result en booleen et notice sous forme d'un tableau
	*/
	public function _DELETE($get,$post) {
		if (!$get['id']) throw new Exception("MISSING_ID",1000);
		$return['result'] = $this->delete($get);
		// Récupération des notices créés
		$return['notices'] = ATF::$msg->getNotices();
		return $return;
	}

	/**
	* Récupère la liste des numéros de téléphones utiles
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param $get array contient l'id du ticket a l'index 'id'
	* @return array les numéro de téléphone sous deux index : societe et contact
	*/
	public function _tel($get) {
		if (!$get['id']) throw new Exception("MISSING_ID",1000);

		$h = $this->select($get['id']);
		// Récupération des numéros du contacts du ticket
		$c = ATF::contact()->select($h['id_contact']);
		$return['contact']['id'] = $h['id_contact'];
		if ($c['tel'] || $c['gsm']) $return['contact']["name"] = $c["prenom"]." ".$c["nom"];
		if ($c['tel']) $return['contact']["tel"] = $c['tel'];
		if ($c['gsm']) $return['contact']["gsm"] = $c['gsm'];

		// Récupération des numéros de la société
		$s = ATF::societe()->select($h['id_societe']);
		$return['societe']['id'] = $h['id_societe'];
		if ($s['tel'] || $s['gsm']) $return['societe']["name"] = $s["societe"];
		if ($s['tel']) $return['societe']["tel"] = $s['tel'];
		if ($s['gsm']) $return['societe']["gsm"] = $s['gsm'];

		return $return;
	}

	/**
	* Récupère la liste des affaires utiles
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param $get array contient l'id du ticket a l'index 'id'
	* @return array les affaires éligibles
	*/
	public function _affaire($get) {
		if (!$get['id']) throw new Exception("MISSING_ID",1000);

		$h = $this->select($get['id']);
		ATF::affaire()->q->reset()->where("id_societe",$h['id_societe'])->where("etat","terminee","AND","cle1","!=")->where("etat","perdue","AND","cle1","!=");

		return ATF::affaire()->sa();
	}

	/**
	* Renvoi toutes les infos utiles pour le ticket hotline : temps facturé / passé, temps de deplacement, etc...
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param $get array contient l'id du ticket a l'index 'id'
	* @return array les différends indicateurs utiles : prestaTicket, credit, deplacement, solde, etc...
	*/
	public function _getInfos($get,$post) {
		if (!$get['id']) throw new Exception("MISSING_ID",1000);

		$id_societe = $this->select($get['id'],"id_societe");

		$r['prestaTicket'] = $this->getTotalTime($get['id'],"prestaTicket");
		$r['credit'] = $this->getCreditUtilises($get['id']);
		$r['deplacement'] = $this->getTotalTime($get['id'],"dep");
		$r['solde'] = ATF::societe()->getSolde($id_societe);

		return $r;
	}

	/**
	* Renvoi un flag pour identifier si le ticket est facturable au client ou non
	* @package Telescope
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param $get array contient l'id du ticket ou l'id d'une interaction
	* @return boolean TRUE si facturable, sinon FALSE
	*/
	public function _isFacturable($get,$post){
		if(!$get["id_hotline"] && $get["id_hotline_interaction"]) {
			$get["id_hotline"] = ATF::hotline_interaction()->select($get["id_hotline_interaction"], "id_hotline");
		}

		if ($get["id_hotline"]) {
			$hotline = $this->select($get["id_hotline"]);
			if($hotline["charge"] == "intervention" && $hotline["facturation_ticket"] == "oui"){
				return true;
			}
		}
		return false;
	}



	public function _partTicket($get,$post){
		log::logger($get , "mfleurquin");
		log::logger($post , "mfleurquin");

		$at = $this->stats(true,"partTicket");
		ATF::define_db("db","extranet_v3_att");
		ATF::$codename = "att";
		$att = $this->stats(true,"partTicket");
		ATF::define_db("db","extranet_v3_absystech");
		ATF::$codename = "absystech";

		$res = array();
		$res["dev"]["total"] =     $at["dataset"]["dev"]["total"] + $att["dataset"]["dev"]["total"];
		$res["telecom"]["total"] = $at["dataset"]["telecom"]["total"] + $att["dataset"]["telecom"]["total"];
		$res["system"]["total"] =  $at["dataset"]["system"]["total"] + $att["dataset"]["system"]["total"];

		$total = $res["dev"]["total"] + $res["telecom"]["total"] + $res["system"]["total"];

		foreach ($res as $key => $value) {
			$res[$key]["pourcentage"] = round(($value["total"] / $total)*100,1);
		}
		return $res;
	}

	public function _getStatSnapData($get,$post){
		$to_return = array();

		$code = $get['code'];
		$date = date('Y-m-d',strtotime('- 30 day'));
		$q = "SELECT * FROM stat_snap WHERE code = '".$code."' AND DATE_FORMAT(date, '%Y-%m-%d') >='".$date."' ORDER BY date DESC";

		$to_return['data_snap'] = ATF::db()->sql2array($q);

		if ($code == 'getTotalHotlineEnCours'){
			$to_return['ca'] = ATF::commande_absystech()->_getCaThisMonth();
			$to_return['satisfaction'] =  $this->getSatisfaction();
			$to_return['conseil'] = ATF::news()->getConseils();
		}

		return $to_return;
	}


};
?>
