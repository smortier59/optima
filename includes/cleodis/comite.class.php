<?
/** Classe comite
* @package Optima
* @subpackage Cléodis
*/
class comite extends classes_optima {
	function __construct($table_or_id) {
		$this->table ="comite";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			'comite.date'
			,'comite.id_refinanceur'
			,'comite.id_affaire'
			,'comite.id_societe'
			,'comite.loyer_actualise'=>array("aggregate"=>array("min","avg","max","sum"),"renderer"=>"money")
			,'comite.taux'=>array("aggregate"=>array("min","avg","max"),"align"=>"right","suffix"=>"%")
			,'pdf'=>array("custom"=>true,"nosort"=>true,"type"=>"file","width"=>50,"align"=>"center")
			,'comite.etat'=>array("width"=>30,"renderer"=>"etat")
			,'comite.validite_accord'=>array("renderer"=>"updateDate","width"=>170)
			,'comite.date_cession'=>array("renderer"=>"updateDate","width"=>170)
			,'comite.duree_refinancement'
			,'decision'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"comiteDecision","width"=>50)
			,"decisionComite"
			,'envoi_mail_demandeRefi'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"envoi_mail_demandeRefi","width"=>50)
		);

		$this->colonnes['primary'] = array(
			"id_societe"=>array("disabled"=>true)
			,"activite"
			,"id_affaire"=>array("disabled"=>true)
			,"id_refinanceur"
			,"id_contact"=>array(
				"obligatoire"=>true,
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
			),
			"date_creation"


		);
//
//		$this->colonnes['panel']['loyer_lignes'] = array(
//			"loyer"=>array("custom"=>true)
//		);

		// Javascript expérimental
		$javascript = "function (t) {
			ATF.ajax('affaire,getCompteTLoyerActualise.ajax'
				,'id_affaire='+Ext.getCmp('comite[id_affaire]').getValue()+'&taux='+Ext.getCmp('comite[taux]').getValue()+'&date_cession='+Ext.util.Format.date(Ext.getCmp('comite[date_cession]').getValue(), 'Y-m-d')+'&vr='+Ext.getCmp('comite[valeur_residuelle]').getValue()
				,{ onComplete: function (o) {
						Ext.getCmp('comite[loyer_actualise]').setValue(o.result);
					}
				}
			);
		}";

		$this->colonnes['panel']['creditSafeInfos'] = array(
			 "date_compte"
			,"note"
			,"limite"
			,"capital_social"
			,"capitaux_propres"
			,"dettes_financieres"
			,"ca"
			,"resultat_exploitation"
			,"maison_mere1"
			,"maison_mere2"
			,"maison_mere3"
			,"maison_mere4"
		);

		$this->colonnes['panel']['chiffres'] = array(
			"taux"=>array("formatNumeric"=>true,"xtype"=>"textfield","listeners"=>array("change"=>$javascript))
			,"valeur_residuelle"=>array("formatNumeric"=>true,"xtype"=>"textfield","listeners"=>array("change"=>$javascript))
			,"loyer_actualise"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"prix"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"pourcentage_materiel"
			,"pourcentage_logiciel"
			,"coefficient"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"encours"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"frais_de_gestion"=>array("readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);



		$this->colonnes['panel']['dates'] = array(
			"date"
			,"reponse"
			,"validite_accord"
			,"date_cession"=>array("listeners"=>array("select"=>$javascript,"change"=>$javascript))
			,"duree_refinancement"
		);

		$this->colonnes['panel']['notes'] = array(
			"description"
			,"marque_materiel"
			,"observations"
		);

		$this->colonnes['panel']['notifie'] = array(
			"suivi_notifie"=>array("custom"=>true)
		);

		$this->field_nom = "description";

		$this->colonnes['bloquees']['select'] =   array_merge(array('note'));

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['update'] =
		$this->colonnes['bloquees']['clone']  = array_merge(array('etat','commentaire','decision', 'decisionComite', "notifie_utilisateur"));

		$this->fieldstructure();

		//$this->colonnes['bloquees']['insert'] = array('score',"avis_credit");

		$this->noTruncateSA = true;
		$this->files["pdf"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true);
		$this->panels['loyer_lignes'] = array('nbCols'=>1);
		$this->panels['chiffres'] = array("visible"=>true, 'nbCols'=>4);
		$this->panels['dates'] = array("visible"=>true, 'nbCols'=>3);
		$this->panels['notes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['statut'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['creditSafeInfos'] = array("visible"=>true, 'nbCols'=>4);
		$this->no_insert = true;
		$this->selectAllExtjs=true;

		$this->addPrivilege("getInfosFromCREDITSAFE");
		$this->addPrivilege("decision");
		$this->addPrivilege("sendMailDemandeRefi");

	}

	/**
	* Surcharge de l'insert afin de modifier l'etat de l'affaire
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false,$tu=false){

		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}
		$this->infoCollapse($infos);

		$notifie_suivi = $infos["suivi_notifie"];
		$notifie_suivi = array_unique($notifie_suivi);
		unset($infos["suivi_notifie"], $infos["id_devis"]);




//*****************************Transaction********************************
		ATF::db($this->db)->begin_transaction();
		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		if($preview){
			if(!$tu) $this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
		}else{
			if(!$tu) $this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			if($infos["etat"] == "accepte" || $infos["etat"] == "refuse"){
				ATF::affaire()->u(array("id_affaire"=>$infos["id_affaire"], "etat_comite"=>$infos["etat"]));
			}else{
				ATF::affaire()->u(array("id_affaire"=>$infos["id_affaire"], "etat_comite"=>"attente"));
			}


			if($notifie_suivi != array(0=>"")){
				$recipient = "";
				$info_mail["suivi_notifie"] = "";

				foreach ($notifie_suivi as $key => $value) {
					$info_mail["suivi_notifie"] .= ATF::user()->nom($value).",";
					$recipient .= ATF::user()->select($value,"email").",";
				}

				$recipient = substr($recipient, 0, -1);
				$info_mail["suivi_notifie"] = substr($info_mail["suivi_notifie"], 0, -1);

				$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".ATF::user()->select(ATF::$usr->getID(),"email").">";;
				$info_mail["html"] = true;
				$info_mail["template"] = "comite";

				$info_mail["recipient"] = $recipient;
				$info_mail["objet"] = "Une demande de comité vient d'être créée pour l'affaire ".ATF::affaire()->select($infos['id_affaire'], "ref").".";

				$info_mail["reception_comite"] = "Un comité est en attente de votre part, voici quelques informations concernant ce dernier";
				$info_mail["id_user"] = ATF::$usr->getID();
				$info_mail["id_societe"] = $infos["id_societe"];
				$info_mail["id_affaire"] = $infos["id_affaire"];
				$info_mail["optima_url"] = ATF::permalink()->getURL($this->createPermalink($this->cryptId($last_id)));

				$mail = new mail($info_mail);

				if(!$tu) $mail->send();

				$this->u(array("id_comite"=>$last_id , "notifie_utilisateur"=>$info_mail["suivi_notifie"]));
			}


			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){	ATF::affaire()->redirection("select",$infos["id_affaire"]);	}
		return $this->cryptId($last_id);
	}

	/*
	* Surcharge de l'update afin de modifier l'etat de l'affaire
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false,$tu=false){

		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}

		$this->infoCollapse($infos);


		foreach ($infos["suivi_notifie"] as $key => $value) {
			$user_notifie .= ATF::user()->nom($value).",";
		}
		$user_notifie = substr($user_notifie, 0, -1);

		$infos["notifie_utilisateur"] = $user_notifie;


		$infos["filestoattach"]["pdf"]="";
		$notifie_suivi = $infos["suivi_notifie"];

		ATF::devis()->q->reset()->where("devis.id_affaire", $this->select($id, "id_affaire"));
		$devis = ATF::devis()->select_row();

		$notifie_suivi[] = $devis["id_user"];
		$notifie_suivi[] = ATF::$usr->getID();

		$notifie_suivi = array_unique($notifie_suivi);


		unset($infos["suivi_notifie"]);
		/*****************************Transaction********************************/
		ATF::db($this->db)->begin_transaction();
		parent::update($infos,$s,$files);

		if($preview){
			if(!$tu) $this->move_files($infos["id_comite"],$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($infos["id_comite"]);
		}else{
			if(!$tu) $this->move_files($infos["id_comite"],$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			ATF::affaire()->u(array("id_affaire"=>$infos["id_affaire"], "etat_comite"=>$infos["etat"]));


			if($infos["etat"]){
				$suivi = array(
					 "id_user"=>ATF::$usr->get('id_user')
					,"id_societe"=>$infos['id_societe']
					,"id_affaire"=>$infos['id_affaire']
					,"type_suivi"=>'Passage_comite'
					,"texte"=>"La demande de comité vient de changer d'état (nouvel état : ".ATF::$usr->trans($infos["etat"],$this->table).")\n par ".ATF::$usr->getNom().".\n Refinanceur : ".ATF::refinanceur()->select($infos["id_refinanceur"] , "refinanceur")
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_societe'=>array(0=>ATF::$usr->getID())
					,'suivi_notifie'=>$notifie_suivi
					,"permalink"=> ATF::permalink()->getURL($this->createPermalink($infos["id_comite"]))
					,'no_redirect'=>true
				);
				if(!$tu) $id_suivi = ATF::suivi()->insert($suivi);
			}

			ATF::db($this->db)->commit_transaction();
			/****************************************************************************/

			if(is_array($cadre_refreshed)){	ATF::affaire()->redirection("select",$infos["id_affaire"]);	}

			$id_comite=$this->decryptId($infos["id_comite"]);
			return $id_comite;
		}

	}

	/**
	* Impossible de modifier un devis qui n'est pas en attente
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_update($id,$infos=false){
		if($this->select($id,"etat")=="en_attente"){
			return true;
		}else{
			throw new errorATF("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car il n'est plus en '".ATF::$usr->trans("attente")."'",892);
			return false;
		}
	}


	/**
	* Permet de modifier la date sur un select_all
	* @param array $infos id_table, key (nom du champs à modifier),value (nom du champs à modifier)
	* @return boolean
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function updateDate($infos){

		if ($infos['value'] == "undefined") $infos["value"] = "";

		$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
		$infosMaj["id_".$this->table]=$infos["id_".$this->table];
		$infosMaj[$infos["key"]]=$infos["value"];

		if(array_key_exists("validite_accord",$infosMaj)){
			$data=array("id_comite"=>$infosMaj["id_".$this->table], "validite_accord"=>$infos["value"]);
		}elseif(array_key_exists("date_cession",$infosMaj)){
			$data=array("id_comite"=>$infosMaj["id_".$this->table], "date_cession"=>$infos["value"]);
		}
		if($this->u($data)){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
				,ATF::$usr->trans("notice_success_title")
			);
		}
		return true;
	}

	/**
    * Retourne la valeur par défaut spécifique aux données des formulaires
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function default_value($field,&$s,&$request){
		$id_devis = ATF::_r("id_devis");

		$id_affaire=ATF::devis()->select(ATF::devis()->decryptId($id_devis), "id_affaire");
		$affaire = ATF::affaire()->select($id_affaire);
		$societe = $affaire["id_societe"];

		switch ($field) {
			case "id_affaire":	return $id_affaire;
			case "id_societe": return $affaire['id_societe'];
			case "id_user":	return ATF::$usr->get('id_user');
			case "date":
				return date("d-m-Y");
			case "id_contact":
					$devis = ATF::affaire()->getDevis($affaire['id_affaire']);
					return $devis->infos['id_contact'];
			case "prix":
					$devis = ATF::affaire()->getDevis($affaire['id_affaire']);
					return $devis->infos['prix'];
			case "description":	return $affaire['affaire'];
			case "loyer_actualise":		return ATF::affaire()->getCompteTLoyerActualise($affaire);

			case "pourcentage_materiel" : $pourcentage = ATF::affaire()->getPourcentagesMateriel($id_affaire);
										  return $pourcentage["pourcentagesMat"];
			case "suivi_notifie" :		  if(ATF::_r("id_comite")){
												$return = array();
												$data = array(  '16' => 'Jérôme LOISON',
																'17' => 'Christophe LOISON',
																'18' => 'Pierre CAMINEL',
																'93' => 'Térence DELATTRE');

												$notifie = $this->select( $this->decryptId(ATF::_r("id_comite")), "notifie_utilisateur");
												$notifie = explode(",", $notifie);
												foreach ($notifie as $key => $value) {
													if(in_array($value, $data)){ $return[] = array_search($value, $data); }
												}
												return $return;
										   }

		}

		return parent::default_value($field,$s,$request);
	}

	/**
	 * Permet d'interoger Credit Safe et de récupérer les infos de la société
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  array $infos
	 */
	public function getInfosFromCREDITSAFE($infos){

		if(ATF::$codename == "cleodisbe"){
			$num_ident = ATF::societe()->select($infos["societe"], "num_ident");
			$res = ATF::societe()->getInfosFromCREDITSAFE(array("num_ident"=>$num_ident, "returnxml"=>"oui"));
		}else{
			$siret = ATF::societe()->select($infos["societe"], "siret");
			$res = ATF::societe()->getInfosFromCREDITSAFE(array("siret"=>$siret, "returnxml"=>"oui"));
		}

		$xml = simplexml_load_string($res);

		$bi = $xml->xmlresponse->body->company->baseinformation;
		$s = $xml->xmlresponse->body->company->summary;
		$b = $xml->xmlresponse->body->company->balancesynthesis;

		$maison_meres = $s->ultimateparents;

		foreach ($maison_meres->ultimateparent as $key => $value) {	$mm[] = (string)$value->name; }

		if($mm[0]){ $data["maison_mere1"] = $mm[0]; }
		if($mm[1]){ $data["maison_mere2"] = $mm[1]; }
		if($mm[2]){ $data["maison_mere3"] = $mm[2]; }
		if($mm[3]){ $data["maison_mere4"] = $mm[3]; }

		$data["date_creation"] = (string)$bi->formationdate;
		$data["date_compte"] = (string)$bi->lastaccountdate;

		$data["note"] = (string)$s->rating2013;
		$data["limite"] = (string)$s->creditlimit2013;

		$data["activite"] = (string)$bi->activitydescription;



		$data['ca'] = (string)$s->financialsummary->tradingtodate[0]->turnover;


		$data["resultat_exploitation"] = (string)$b->balancesheet->profitloss->operatingprofitloss;
		$data["capital_social"] = (string)$bi->sharecapital;
		$data["capitaux_propres"] = (string)$b->balancesheet->passiveaccount->shareholdersequity;
		$data["dettes_financieres"] = (string)$b->balancesheet->passiveaccount->financialliabilities;

		if(strpos($data["capital_social"], "Euros")){ $data["capital_social"] = str_replace(" Euros", "", $data["capital_social"]); }
		return $data;
	}


	/**
	 * Permet de rendre une décision sur un comité (Accepte/Refusé)
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  [type] $infos [description]
	 */
	public function decision($infos){

		$validite = explode("/", $infos["date"]);
		$validite_accord = $validite[2]."-".$validite[1]."-".$validite[0];

		$id = $this->decryptId($infos["id"]);



		switch($infos["comboDisplay"]){

			case "refus_comite":
				$etat = "refuse";
				ATF::affaire()->u(array("id_affaire"=>$infos["id_affaire"], "etat_comite"=>$etat));
			break;

			case "attente_retour":
			case "accord_reserve_cession";
				$etat = "en_attente";
				ATF::affaire()->u(array("id_affaire"=>$infos["id_affaire"], "etat_comite"=>"attente"));
			break;


			case "accord_portage" :
			case "accord_portage_recherche_cession" :
			case "accord_portage_recherche_cession_groupee" :
				$etat = "accepte";
				ATF::affaire()->u(array("id_affaire"=>$infos["id_affaire"], "etat_comite"=>$etat));

				$id_affaire = ATF::comite()->select($id, "id_affaire");

				ATF::comite()->q->reset()->where("id_affaire", $id_affaire)
								 ->where("etat", "accepte");

				$c = ATF::comite()->sa();

				if($c){
					foreach ($c as $key => $value) {
						ATF::comite()->u(array("id_comite"=>$value["id_comite"] , "etat"=> "accord_non utilise"));
					}
				}
			break;

		}


		$data = array("id_comite"=>$id,
					  "etat"=>$etat,
					  "commentaire"=>$infos["commentaire"],
					  "reponse"=>date("Y-m-d"),
					  "decisionComite"=>$infos["decision"],
					  "validite_accord"=>$validite_accord
					);
		$this->u($data);

		ATF::devis()->q->reset()->where("devis.id_affaire", $this->select($id, "id_affaire"));
		$devis = ATF::devis()->select_row();



		$notifie_suivi = array(ATF::$usr->getID(), $devis["id_user"], ATF::societe()->select($this->select($id , "id_societe"), "id_owner"));

		$notifie_suivi = array_unique($notifie_suivi);

		$suivi_notifie = "";

		foreach ($notifie_suivi as $key => $value) {
			$suivi_notifie .= ATF::user()->nom($value).",";
		}
		$suivi_notifie = substr($suivi_notifie, 0, -1);
		$this->u(array("id_comite"=>$id , "notifie_utilisateur"=>$suivi_notifie));

		$suivi = array(
					 "id_user"=>ATF::$usr->get('id_user')
					,"id_societe"=>$this->select($id, "id_societe")
					,"id_affaire"=>$this->select($id, "id_affaire")
					,"type_suivi"=>'Passage_comite'
					,"texte"=>"La demande de comité vient d'etre ".ATF::$usr->trans($etat,$this->table)."\n par ".ATF::$usr->getNom()."\n\nDécision prise : ".$infos["decision"]."\n\Commentaire : ".$infos["commentaire"]
					,'public'=>'oui'
					,'id_contact'=>NULL
					,'suivi_societe'=>array(0=>ATF::$usr->getID())
					,'suivi_notifie'=>$notifie_suivi
					,"permalink"=> ATF::permalink()->getURL($this->createPermalink($id))
					,'no_redirect'=>true
				);
		$id_suivi = ATF::suivi()->insert($suivi);

	}

	/**
	 * Permet d'envoyer le mail de demande de refi au refinanceur du comité
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  array $infos  array(id) -> ID du comité
	 */
	public function sendMailDemandeRefi($infos){
		$id = $this->decryptId($infos["id"]);

		$comite = $this->select($id);
		$refinanceur = ATF::refinanceur()->select($comite["id_refinanceur"]);
		$societe = ATF::societe()->select($comite["id_societe"]);
		$affaire = ATF::affaire()->select($comite["id_affaire"], "affaire");

		if(ATF::$codename === "cleodisbe"){
			$info_mail["from"] = "Quentin ELLEBOUDT | Cleodis <quentin.elleboudt@cleodis.com>";
		}else{
			$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".ATF::user()->select(ATF::$usr->getID(),"email").">";
		}

		$info_mail["html"] = true;
		$info_mail["template"] = "demande_refi";

		$info_mail["recipient"] = $refinanceur["email"];
		$info_mail["objet"] = "Demande ".$societe["societe"];


		$info_mail["societe"] = $societe["societe"];
		$info_mail["num_tva"] = $societe["num_ident"];
		$info_mail["adresse"] = $societe["adresse"];
		$info_mail["adresse_2"] = $societe["adresse_2"];
		$info_mail["adresse_3"] = $societe["adresse_3"];
		$info_mail["cp"] = $societe["cp"];
		$info_mail["ville"] = $societe["ville"];
		$info_mail["nom_affaire"] = $affaire;
		$info_mail["loyer_actualise"] = $comite["loyer_actualise"];
		$info_mail["duree_refinancement"] = $comite["duree_refinancement"];
		$info_mail["observations"] = $comite["observations"];


		$mail = new mail($info_mail);

		$mail->send();


		$suivi = array(
			 "id_user"=>ATF::$usr->get('id_user')
			,"id_societe"=>$this->select($id, "id_societe")
			,"id_affaire"=>$this->select($id, "id_affaire")
			,"type_suivi"=>'Passage_comite'
			,"texte"=>"Envoi du mail au refinanceur ".$refinanceur["refinanceur"]
			,'public'=>'oui'
			,'id_contact'=>NULL
			,'suivi_societe'=>array(0=>ATF::$usr->getID())
			,'suivi_notifie'=>$notifie_suivi
			,"permalink"=> ATF::permalink()->getURL($this->createPermalink($id))
			,'no_redirect'=>true
		);
		$id_suivi = ATF::suivi()->insert($suivi);
	}


	/**
    * Avoir des infos sur la société
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){

		$return = parent::select_all($order_by,$asc,$page,$count);

		foreach ($return['data'] as $k=>$i) {
			if($i["comite.id_societe_fk"]){ $id_societe = $i["comite.id_societe_fk"]; }else{$id_societe = $i["id_societe"]; }

			if(ATF::societe()->select($id_societe , "code_client") && strpos(ATF::societe()->select($id_societe , "code_client"), "S") === false){
				//Reseau
				$return['data'][$k]["reseau"] = true;
			}else{
				//Autre
				$return['data'][$k]["reseau"] = false;
			}

			$return['data'][$k]["email"] = ATF::refinanceur()->select($i["comite.id_refinanceur_fk"], "email");

		}
		return $return;

	}
	public function _POST($get,$post) {
	 	$input = file_get_contents('php://input');
		if (!empty($input)) parse_str($input,$post);
		// met entre 7 & 8 secondes a s'executer
		// & a rendre une réponse
	 	if($post['id'] && $post['etat']){
			$post["comboDisplay"] = $post['etat']=='refuse'?'refus_comite':$post['etat'];
			$post["date"] =  date("d/m/Y");
			$this->decision($post);
			if($post['etat'] === "accepte"){
				ATF::societe()->_createContratToshiba(false,array('id_affaire'=>$post['id_affaire']));
			}
			return true;
	 	}
	}
};

?>