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
			,'credit_total'=>array("custom"=>true,"align"=>"right","aggregate"=>array("avg","min","max","sum"),"type"=>"decimal","width"=>80)
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
			->addField("IF(hotline.facturation_ticket = 'oui' AND hotline.charge = 'intervention',SUM(hotline_interaction.credit_presta + hotline_interaction.credit_dep),0)","credit_total")
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
				$chargeText="Charge 2T Management";
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
			case "maintenance":
				$hotline["facturation_ticket"]="non";
				if($id_user) $hotline["etat"]="fixing";
				$hotline["ok_facturation"]=NULL;
				$chargeText="Contrat de maintenance";
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

		$societe = ATF::societe()->select($infos['id_societe']);
		$contact = ATF::contact()->select($infos['id_contact']);
		$hotline = ATF::hotline()->select($id_hotline);


		//cadre refresh
		$this->redirection("select",$id_hotline,"hotline-select-".$this->cryptId($id_hotline).".html");


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

		$to="hotline@2tmanagement.support";
		$template="hotline_insert";
		$from="hotline@2tmanagement.support";

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
		$mail=new mail(array('recipient'=>'hotline@2tmanagement.support','objet'=>'[Facturation Hotline - Optima '.ATF::$codename.'] Résultats batch','body'=>ob_get_contents()));
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



	/*
	* Simple function to sort an array by a specific key. Maintains index association.
	* @codeCoverageIgnore
	*/
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

	/*
	* @codeCoverageIgnore
	*/
	public function getTauxHorraire($id_affaire){
		$marge_brute = 0;
		ATF::facture()->q->reset()->where("facture.id_affaire",$id_affaire);
		$res = ATF::facture()->select_all();
		//Si j'ai des factures
		if($res){
			ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire",$id_affaire);
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
			ATF::devis()->q->reset()->where("devis.id_affaire", $id_affaire)
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
	* @codeCoverageIgnore
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
				$chargeText="Charge 2T Management";
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
			case "maintenance":
				$hotline["facturation_ticket"]="non";
				if($id_user) $hotline["etat"]="fixing";
				$hotline["ok_facturation"]=NULL;
				$chargeText="Contrat de maintenance";
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
				$email="hotline@2tmanagement.support";

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

};
?>
