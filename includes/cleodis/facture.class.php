<?
/** Classe facture
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../facture.class.php";
class facture_cleodis extends facture {
	function __construct($table_or_id=NULL) {
		$this->table = "facture";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			'facture.ref'
			,'facture.type_facture'=>array("renderer"=>"typeFacture","width"=>80,"align"=>"center")
			,'facture.id_societe'
			,'societe.code_client'=>array("custom"=>true)
			,'facture.id_affaire'
			,'facture.date'
			,'facture.date_paiement'=>array("renderer"=>"updateDate","width"=>170)
			,'facture.date_periode_debut'
			,'facture.date_periode_fin'
			,'facture.prix'=>array("aggregate"=>array("min","avg","max","sum"),"renderer"=>"money")
			,'facture.prix_sans_tva'=>array("aggregate"=>array("min","avg","max","sum"),"renderer"=>"money")
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","width"=>50,"align"=>"center")
            ,'facture.rejet'=>array("renderer"=>"updateEnumFactureRejetCledodis","width"=>200)
            ,'relance'=>array("custom"=>true,"nosort"=>true,"renderer"=>"relanceFacture","width"=>70)
            ,'facture.date_rejet'=>array("renderer"=>"updateDate","width"=>170)
            ,'facture.date_regularisation'=>array("renderer"=>"updateDate","width"=>170)
            ,'facture.nature'
			,'facture.date_envoi'=>array("renderer"=>"updateDate","width"=>170)
			,'facture.envoye'
		);

		// Panel principal
		$this->colonnes['primary'] = array(
			"id_societe"=>array("disabled"=>true)
			,"id_affaire"=>array("disabled"=>true)
			,"id_commande"=>array("disabled"=>true)
			,"type_facture"
			,"type_libre"=>array("disabled"=>true)
			,'nature'=>array("disabled"=>true)
			,"redevance"=>array("disabled"=>true)
			,"mode_paiement"
			,"id_fournisseur_prepaiement"=>array("autocomplete"=>array(
   				"function"=>"autocompleteFournisseurs"
     		))
			,"date"
			,"date_previsionnelle"
			,"date_envoi"
			,"designation"=>array("xtype"=>"textarea")
			,"commentaire"=>array("xtype"=>"textarea")

		);

		$this->colonnes['panel']['refi'] = array(
			"id_demande_refi"=>array("disabled"=>true)
			,"id_refinanceur"=>array("disabled"=>true)
			,"prix_refi"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","disabled"=>true)
		);

		$this->colonnes['panel']['dates_facture'] = array(
			"date_periode_debut"=>array("readonly"=>true),
			"date_periode_fin"=>array("readonly"=>true),
			"prix"  => array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);

		$this->colonnes['panel']['dates_facture_libre'] = array(
			"date_periode_debut_libre"=>array("custom"=>true,"xtype"=>"datefield"),
			"date_periode_fin_libre"=>array("custom"=>true,"xtype"=>"datefield"),
			"prix_libre"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);

		$this->colonnes['panel']["hors_tva"] = array(
			"prix_sans_tva"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);

		$this->colonnes['panel']['midas'] = array(
			"periode_midas"=>array("custom"=>true,"xtype"=>"textfield"),
			"prix_midas"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);

		$this->colonnes['panel']['loyer_lignes'] = array(
			"loyer"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes_repris'] = array(
			"produits_repris"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes_non_visible'] = array(
			"produits_non_visible"=>array("custom"=>true)
		);

		$this->colonnes['panel']['courriel'] = array(
			"email"=>array("custom"=>true,'null'=>true)
			,"emailCopie"=>array("custom"=>true,'null'=>true)
			,"emailTexte"=>array("custom"=>true,'null'=>true,"xtype"=>"htmleditor")
		);

		// Propriété des panels
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>3);
		$this->panels['refi'] = array("visible"=>true,'nbCols'=>3,"hidden"=>true);
		$this->panels['dates_facture'] = array("visible"=>true,'nbCols'=>3);
		$this->panels['hors_tva'] = array("visible"=> false, 'nbCols' => 3);
		$this->panels['midas'] = array("visible"=>true,'nbCols'=>3,"hidden"=>true);
		$this->panels['dates_facture_libre'] = array("visible"=>true,'nbCols'=>3,"hidden"=>true);
		$this->panels['lignes_repris'] = array('nbCols'=>1);
		$this->panels['lignes'] = array('nbCols'=>1);
		$this->panels['lignes_non_visible'] = array('nbCols'=>1);
		$this->panels['loyer_lignes'] = array('nbCols'=>1);
		$this->panels['courriel'] = array('nbCols'=>2,"checkboxToggle"=>true);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] = array('ref','tva','etat','date_paiement','date_relance','id_user','envoye_mail','rejet');
		$this->fieldstructure();

		$this->onglets = array('facture_ligne','slimpay_transaction');

		$this->no_insert = true;
		$this->no_update = true;
		$this->addPrivilege("majMail","update");
		$this->addPrivilege("export_special");
		$this->addPrivilege("export_special2");
		$this->addPrivilege("export_autoportes");
		$this->addPrivilege("updateEnumRejet");
		$this->addPrivilege("getAllForRelance");
		$this->addPrivilege("libreToNormale");
		$this->addPrivilege("export_cegid");
		$this->addPrivilege("export_cleofi");

		$this->addPrivilege("import_facture_libre");
		$this->addPrivilege("import_facture_controle_statut");
		$this->addPrivilege("download_facture_controle_statut");

		$this->addPrivilege("aPrelever");
		$this->addPrivilege("aPreleverEchec");

		$this->addPrivilege("massPrelevementSlimpay");




		$this->field_nom="ref";
		$this->foreign_key["id_fournisseur_prepaiement"] = "societe";
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true);
		$this->selectAllExtjs=true;


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

		if($id_facture = ATF::_r('id_facture')){
			$facture=$this->select($id_facture);
		}elseif ($id_commande = ATF::_r('id_commande')){
			$facture=ATF::commande()->select($id_commande);
		}

		switch ($field) {
			case "email":
				if($facture){
					$societe=ATF::societe()->select($facture["id_societe"]);
					if($societe["id_contact_facturation"]){
						$email=ATF::contact()->select($societe["id_contact_facturation"],"email");
					}
				}
				if($email){
					return $email;
				}
				break;
			case "emailCopie":
				return ATF::$usr->get("email");
			case "emailTexte":
				if ($facture["id_societe"]) {
					return $this->majMail($facture["id_societe"]);
				}
				break;
			case "id_societe":
				if ($facture) {
					return $facture['id_societe'];
				}
				break;
			case "id_user":
				if ($facture) {
					$id_user = $facture['id_user'];
				}else{
					$id_user = ATF::$usr->get('id_user');
				}
				return $id_user;
			case "id_affaire":
				if ($facture) {
					return $facture['id_affaire'];
				}
				break;
			case "id_commande":
				if ($facture) {
					return $facture['id_commande'];
				}
				break;
			case "date":
				return date("Y-m-d");
			case "date_previsionnelle":
				$affaire = ATF::affaire()->select($facture["id_affaire"]);
				$jourPrelevement = "01";
                if ($affaire["date_previsionnelle"]) {
					if (intval($affaire["date_previsionnelle"]) < 10) {
						$jourPrelevement = "0".$affaire["date_previsionnelle"];
					} else {
						$jourPrelevement = $affaire["date_previsionnelle"];
					}
                }

				if ($facture) {
					if (ATF::$codename == "go_abonnement") {
						$periode = ATF::facturation()->next_echeance($facture['id_affaire'],true);
					} else {
						$periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true);
					}
					if($date_previsionnelle=ATF::affaire()->select($facture['id_affaire'],"date_previsionnelle")){
						$day=$date_previsionnelle;
					}else{
						$day=0;
					}
					return date('Y-m-',strtotime($periode["date_periode_debut"]."+".$day." day")).$jourPrelevement;
				}else{
					return date("Y-m-").$jourPrelevement;
				}
			case "date_periode_debut":
				if ($facture) {
					if (ATF::$codename == "go_abonnement") {
						$periode = ATF::facturation()->next_echeance($facture['id_affaire'],true);
					} else {
						$periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true);
					}

					return $periode["date_periode_debut"];
				}
				break;
			case "date_periode_fin":
				if ($facture) {
					if (ATF::$codename == "go_abonnement") {
						$periode = ATF::facturation()->next_echeance($facture['id_affaire'],true);
					} else {
						$periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true);
					}
					return $periode["date_periode_fin"];
				}
				break;
			case "prix":
				if ($facture) {

					if (ATF::$codename === "go_abonnement") {
						$type_affaire = ATF::affaire()->select($facture['id_affaire'], "id_type_affaire");

						if ($type_affaire && ATF::type_affaire()->select($type_affaire, "assurance_sans_tva") === "oui") {
							if($periode = ATF::facturation()->next_echeance($facture['id_affaire'],true)){
								$prix=($periode["montant"]+$periode["frais_de_gestion"]);
							}elseif(ATF::affaire()->select($facture['id_affaire'],"nature"=="vente")){
								$prix=$facture["prix"];
							}
						} else {
							if($periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true)){
								$prix=($periode["montant"]+$periode["frais_de_gestion"]+$periode["assurance"]);
							}elseif(ATF::affaire()->select($facture['id_affaire'],"nature"=="vente")){
								$prix=$facture["prix"];
							}
						}
					} else {
						if($periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true)){
							$prix=($periode["montant"]+$periode["frais_de_gestion"]+$periode["assurance"]);
						}elseif(ATF::affaire()->select($facture['id_affaire'],"nature"=="vente")){
							$prix=$facture["prix"];
						}
					}


				}
				if($prix){
					return $prix;
				}

				break;
			case "prix_sans_tva":
					$prix_sans_tva = 0;
					if ($facture) {
						if (ATF::$codename === "go_abonnement") {
							$type_affaire = ATF::affaire()->select($facture['id_affaire'], "id_type_affaire");
							if ($type_affaire && ATF::type_affaire()->select($type_affaire, "assurance_sans_tva") === "oui") {
								if (ATF::$codename == "go_abonnement") {
									if($periode = ATF::facturation()->next_echeance($facture['id_affaire'],true)){
										$prix_sans_tva=$periode["assurance"];
									}elseif(ATF::affaire()->select($facture['id_affaire'],"nature"=="vente")){
										$prix_sans_tva=$facture["prix_sans_tva"];
									}
								} else {
									if($periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true)){
										$prix_sans_tva=$periode["assurance"];
									}elseif(ATF::affaire()->select($facture['id_affaire'],"nature"=="vente")){
										if ($facture["prix_sans_tva"]) {
											$prix_sans_tva=$facture["prix_sans_tva"];
										}
									}
								}
							} else {
								if($periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true)){
									$prix=($periode["montant"]+$periode["frais_de_gestion"]+$periode["assurance"]);
								}elseif(ATF::affaire()->select($facture['id_affaire'],"nature"=="vente")){
									if ($facture["prix_sans_tva"]) {
										$prix_sans_tva=$facture["prix_sans_tva"];
									}
								}
							}
						}
					}
					return $prix_sans_tva;

					break;
			case "prix_refi":
				if ($facture["id_affaire"]) {
					$refi=ATF::affaire()->refiValid($facture["id_affaire"]);
					return $refi["loyer_actualise"];
				}
				break;
			case "mode_paiement":
				if ($facture["id_affaire"]) {
					if(ATF::affaire()->select($facture["id_affaire"],"nature")=="vente"){
						return "cheque";
					} else {
						return ATF::commande()->select($facture["id_commande"], "type");
					}
				}
				break;
		}

		return parent::default_value($field,$s,$request);
	}




	function getRef($id_affaire,$type="facture"){
		$affaire=ATF::affaire()->select($id_affaire);

		$this->q->reset()
				->addCondition("id_affaire",$id_affaire)
				->addCondition("type_facture",$type)
				->addOrder("ref_reel","DESC")
				->setDimension("row");

		if($affaire["nature"]=='avenant'){
			$this->q->addField('ROUND( SUBSTRING(  `ref` , 13 ) )',"ref_reel");
		}else{
			$this->q->addField('ROUND( SUBSTRING(  `ref` , 9 ) )',"ref_reel");
		}

		$facture=$this->sa();

		if(!$facture){
			$suffix=0;
		}else{
			$suffix=$facture["ref_reel"];
		}

		if($type=="ap"){
			$sufType="-AP";
		}elseif($type=="refi"){
			$sufType="-RE";
		}elseif($type=="libre"){
			$sufType="-LI";
		}elseif($type=="facture"){
			$sufType="";
		}

		//Si jamais pour une raison x ou y la ref existe déjà il faut incrémenter jusqu'à trouver une ref dispo (problème lorsque cléodis se trompe de type et que l'on doit modifier le type sans changer la ref...)
		$find=false;
		$i=1;
		while($find==false){
			$this->q->reset()->addCondition("ref",$affaire["ref"]."-".($suffix+$i).$sufType);
			if(!$this->sa()){
				$ref=$affaire["ref"]."-".($suffix+$i).$sufType;
				$find=true;
			}else{
				$i++;
			}
		}

		return $ref;

	}

	/**
	* Surcharge de delete afin de modifier la facturation et l'état de l'affaire
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $infos le ou les identificateurs de l'élément que l'on désire inséré
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		if (is_numeric($infos) || is_string($infos)) {
			$id=$this->decryptId($infos);
			$facture=$this->select($id);

			ATF::db($this->db)->begin_transaction();
	//*****************************Transaction********************************

			ATF::facturation()->q->reset()->addCondition("id_facture",$facture["id_facture"])
										  ->setDimension("row");

			if($facturation=ATF::facturation()->sa()){
				$facturation["id_facture"]=NULL;
				$facturation["envoye"]="non";
				ATF::facturation()->u($facturation);
			}

			//Facture
			parent::delete($id,$s);

			//Dans le cas d'une vente les parcs doivent passer en inactif
			if(ATF::affaire()->select($facture["id_affaire"],"nature")=="vente"){
				ATF::commande()->q->reset()->addCondition("id_affaire",$facture["id_affaire"])->setDimension("row");
				$commande=ATF::commande()->sa();
				ATF::commande()->u(array("id_commande"=>$commande["id_commande"],"etat"=>"non_loyer"));
				$commande = new commande_cleodis($commande['id_commande']);
				$affaire = $commande->getAffaire();
				$affaire_parente = $affaire->getParentAvenant();
				ATF::parc()->updateExistenz($commande,$affaire,$affaire_parente);
			}

			/*S'il n'y a pas encore de facture fournisseur alors on peut supprimer le parc*/



			ATF::db($this->db)->commit_transaction();
	//*****************************************************************************
			ATF::affaire()->redirection("select",$facture["id_affaire"]);

			return true;

		} elseif (is_array($infos) && $infos) {

			foreach($infos["id"] as $key=>$item){
				$this->delete($item,$s,$files);
			}
		}
	}


	function is_past($jour) {
		$now = time();
		$date = strtotime($jour);

		if ($date < $now)
			return true;
		return false;
	}



	/**
	* Retourne les lignes de l'objet facture, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function getLignes() {
		$this->notSingleton();
		ATF::facture_ligne()->q->reset()->where("id_facture",$this->get('id_facture'));
		return ATF::facture_ligne()->sa();
	}


	/**
	 * Permet de créer des factures au prorata par rapport à la date d'installation et date
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  [type] $infos [description]
	 */
	public function createFactureProrata($infos){
		if(strtotime($infos["date_installation_reel"]) < strtotime($infos["date_debut_contrat"])){

			$affaire = ATF::affaire()->select($infos["id_affaire"]);
			$commande = ATF::commande()->select($infos["id_commande"]);

			ATF::loyer()->q->reset()->where("id_affaire", $infos["id_affaire"])
									->addOrder("id_loyer", "ASC");
			$loyers = ATF::loyer()->select_all();

			$dateTimeDebContrat = new DateTime(date("Y-m-d", strtotime($infos["date_debut_contrat"])));
			$dateFinPeriode = date_sub($dateTimeDebContrat, date_interval_create_from_date_string('1 days'));
			$dateInstall = new DateTime($infos["date_installation_reel"]);


			$nbJProRata = $dateInstall->diff($dateTimeDebContrat)->format("%a");



			if($loyers[0]["frequence_loyer"] == "mois"){
				$nbDInPeriode = 30;
			}elseif($loyers[0]["frequence_loyer"] == "trimestre"){
				$nbDInPeriode = 90;
			}elseif($loyers[0]["frequence_loyer"] == "semestre"){
				$nbDInPeriode = 180;
			}else{
				$nbDInPeriode = 365;
			}
			//Calcul du bon prix par rapport à la frequence
			//Calcul des bonnes periodes (date_debut date_fin) par rapport aux periodes
			//Ajout des assurance .... sur le prix prix_libre
			$loyerAuJour = ($loyers[0]["loyer"] + $loyers[0]["assurance"] + $loyers[0]["frais_de_gestion"] )/$nbDInPeriode;
			$total = $loyerAuJour * ($nbJProRata +1);


			$prix_sans_tva = 0;

			if ($affaire["id_type_affaire"] && ATF::type_affaire()->select($affaire["id_type_affaire"], "assurance_sans_tva") === "oui") {
				$loyerAuJour = ($loyers[0]["loyer"] + $loyers[0]["frais_de_gestion"] )/$nbDInPeriode;
				$prix = $loyerAuJour * ($nbJProRata +1);
				$prix_sans_tva = ($loyers[0]["assurance"]/$nbDInPeriode) * ($nbJProRata +1) ;
			} else {
				$prix = $total;
			}


			$dateFinPeriode = $dateFinPeriode->format('t-m-Y');

			if($nbJProRata > 0 && $prix != 0){
				$facture["facture"] = array(
		            "id_societe" => $affaire["id_societe"],
		            "type_facture" => "libre",
		            "mode_paiement" => $commande["type"],
		            "id_affaire" => $affaire["id_affaire"],
		            "type_libre" => "normale",
		            "date" => date("d-m-Y"),
		            "id_commande" => $commande["id_commande"],
		            "date_previsionnelle" => date("d-m-Y"),
		            "date_periode_debut" => $infos["date_installation_reel"],
		            "date_periode_fin" => $dateFinPeriode,
		            "prix" => round($prix, 2),
					"prix_sans_tva" => round($prix_sans_tva, 2),
		            "date_periode_debut_libre" => $infos["date_installation_reel"],
		            "date_periode_fin_libre" => $dateFinPeriode,
		            "prix_libre" => round($prix, 2),
		            "nature" => "prorata"
		        );




				ATF::commande_ligne()->q->reset()->where("commande_ligne.id_commande", $commande["id_commande"]);
				$lignes = ATF::commande_ligne()->select_all();

				foreach ($lignes as $key => $value) {
					$ligne = array();
					$ligne["facture_ligne__dot__produit"] = $value["produit"];
		            $ligne["facture_ligne__dot__quantite"] = $value["quantite"];
		            $ligne["facture_ligne__dot__ref"] = $value["ref"];
		            $ligne["facture_ligne__dot__id_fournisseur_fk"] = $value["id_fournisseur"];
		            $ligne["facture_ligne__dot__prix_achat"] = $value["prix_achat"];
		            $ligne["facture_ligne__dot__id_produit"] = $value["produit"];
		            $ligne["facture_ligne__dot__id_produit_fk"] = $value["id_produit"];
		            $ligne["facture_ligne__dot__serial"] = $value["serial"];
		            $ligne["facture_ligne__dot__afficher"] = $value["visible_pdf"];
		            $ligne["facture_ligne__dot__visible"] = $value["visible"];
		            $ligne["facture_ligne__dot__id_facture_ligne"] = $value["id_commande_ligne"];

		            $facture["values_facture"]["produits"][] = $ligne;
				}

				$facture["values_facture"]["produits"] = json_encode($facture["values_facture"]["produits"]);

		       	$this->insert($facture);
			}


		}
	}

	/**
	 * Permet de créer la premiere facture du contrat
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param
	 */
	public function createPremiereFacture($infos){


		$affaire = ATF::affaire()->select($infos["id_affaire"]);
		$commande = ATF::commande()->select($infos["id_commande"]);

		ATF::loyer()->q->reset()->where("id_affaire", $infos["id_affaire"])
								->addOrder("id_loyer", "ASC");
		$loyers = ATF::loyer()->select_all();


		$jourPrelevement = date("d", strtotime($infos["date_debut_contrat"]));
        if ($affaire["date_previsionnelle"]) {
            if (intval($affaire["date_previsionnelle"]) < 10) {
				$jourPrelevement = "0".$affaire["date_previsionnelle"];
			} else {
				$jourPrelevement = $affaire["date_previsionnelle"];
			}
        }


		if(strtotime(date("Y-m-d")) < strtotime($affaire["date_debut_contrat"])){
			$date_previsionnelle = date("Y-m-", strtotime($infos["date_debut_contrat"])).$jourPrelevement;
		}
		else{
			$date_previsionnelle = date("Y-m-").$jourPrelevement;
		}

		$dateTimeDebContrat = new DateTime(date("Y-m-d", strtotime($infos["date_debut_contrat"])));


		$prix_sans_tva = 0;

		if ($affaire["id_type_affaire"] && ATF::type_affaire()->select($affaire["id_type_affaire"], "assurance_sans_tva") === "oui") {
			$prix = $loyers[0]["loyer"] + $loyers[0]["frais_de_gestion"];
			$prix_sans_tva = $loyers[0]["assurance"];
		} else {
			$prix = $loyers[0]["loyer"] + $loyers[0]["assurance"] + $loyers[0]["frais_de_gestion"];
		}



		if($loyers[0]["frequence_loyer"] == "mois"){
			$dateFinPeriode = date_add($dateTimeDebContrat, date_interval_create_from_date_string('1 months'));
		}elseif($loyers[0]["frequence_loyer"] == "trimestre"){
			$dateFinPeriode = date_add($dateTimeDebContrat, date_interval_create_from_date_string('3 months'));
		}elseif($loyers[0]["frequence_loyer"] == "semestre"){
			$dateFinPeriode = date_add($dateTimeDebContrat, date_interval_create_from_date_string('6 months'));
		}else{
			$dateFinPeriode = date_add($dateTimeDebContrat, date_interval_create_from_date_string('1 year'));
		}

		$dateFinPeriode = date_sub($dateTimeDebContrat, date_interval_create_from_date_string('1 days'));
		$dateFinPeriode = $dateFinPeriode->format('d-m-Y');

		if($prix != 0){
			$mode_paiement = $commande["type"];
			if($affaire["site_associe"] === 'toshiba') $mode_paiement = "cb";
			if($affaire["site_associe"] === 'btwin') $mode_paiement = "pre-paiement";

			$facture["facture"] = array(
	            "id_societe" => $affaire["id_societe"],
	            "type_facture" => "libre",
	            "type_libre" => "normale",
	            "mode_paiement" => $mode_paiement,
	            "id_affaire" => $affaire["id_affaire"],
	            "date" => date("Y-m-d"),
	            "id_commande" => $commande["id_commande"],
	            "date_previsionnelle" => $date_previsionnelle,
	            "date_periode_debut" => date("Y-m-d", strtotime($infos["date_debut_contrat"])),
	            "date_periode_fin" => $dateFinPeriode,
	            "date_periode_debut_libre" => date("Y-m-d", strtotime($infos["date_debut_contrat"])),
				"date_periode_fin_libre" => $dateFinPeriode,
	            "prix_libre" => round($prix, 2),
	            "prix" => round($prix, 2),
				"prix_sans_tva"=> round($prix_sans_tva, 2),
	            "nature" => "engagement"
	        );
			if($affaire["site_associe"] === 'btwin') $facture["facture"]["id_fournisseur_prepaiement"] = "29109"; //Decathlon BTWIN

	        ATF::commande_ligne()->q->reset()->where("commande_ligne.id_commande", $commande["id_commande"]);
			$lignes = ATF::commande_ligne()->select_all();

			foreach ($lignes as $key => $value) {
				$ligne = array();
				$ligne["facture_ligne__dot__produit"] = $value["produit"];
	            $ligne["facture_ligne__dot__quantite"] = $value["quantite"];
	            $ligne["facture_ligne__dot__ref"] = $value["ref"];
	            $ligne["facture_ligne__dot__id_fournisseur_fk"] = $value["id_fournisseur"];
	            $ligne["facture_ligne__dot__prix_achat"] = $value["prix_achat"];
	            $ligne["facture_ligne__dot__id_produit"] = $value["produit"];
	            $ligne["facture_ligne__dot__id_produit_fk"] = $value["id_produit"];
	            $ligne["facture_ligne__dot__serial"] = $value["serial"];
	            $ligne["facture_ligne__dot__afficher"] = $value["visible_pdf"];
	            $ligne["facture_ligne__dot__visible"] = $value["visible"];
	            $ligne["facture_ligne__dot__id_facture_ligne"] = $value["id_commande_ligne"];

	            $facture["values_facture"]["produits"][] = $ligne;
			}

			$facture["values_facture"]["produits"] = json_encode($facture["values_facture"]["produits"]);


	        $id_facture = $this->insert($facture);



	        $this->libreToNormale(array("id_facture"=> $id_facture));

	        //Sur l'echeancier, le montant ne doit pas être Loyer + aussrance + frais de dossier
	        ATF::facturation()->q->reset()->where("id_facture", $id_facture);
	        $facturation = ATF::facturation()->select_row();
	        ATF::facturation()->u(array("id_facturation"=> $facturation["id_facturation"],
	        							"montant"=> $loyers[0]["loyer"],
	        							"assurance"=>  $loyers[0]["assurance"],
	        							"frais_de_gestion" => $loyers[0]["frais_de_gestion"]
	    						));


	    }


	}


	/**
	* Surcharge de l'insert afin d'insérer les lignes de devis de créer l'affaire si elle n'existe pas
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}
		$infos_ligne_repris = json_decode($infos["values_".$this->table]["produits_repris"],true);
		$infos_ligne_non_visible = json_decode($infos["values_".$this->table]["produits_non_visible"],true);
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		unset($infos["values_".$this->table]["produits"]);

		$envoyerEmail = $infos["panel_courriel-checkbox"];
		$this->infoCollapse($infos);

		$commande=ATF::commande()->select($infos["id_commande"]);
		$infos["id_affaire"]=$commande["id_affaire"];
		$infos["tva"]=$commande["tva"];
		$infos["ref"]=$this->getRef($commande["id_affaire"],$infos["type_facture"]);

		$infos["etat"]="impayee";
		$infos["id_user"] = ATF::$usr->getID();
		$infos["date_relance"]=date("Y-m-d",strtotime("+1 month"));

		if(($infos["type_facture"] === "libre") && (!$infos["type_libre"])){
			throw new errorATF("Il faut un type de facture libre",351);
		}

		if($infos["type_facture"]=="refi"){
			if(!$infos["id_demande_refi"]){
				throw new errorATF("Il n'y a pas de demande de refinancement valide pour cette affaire !",347);
			}
			$demande_refi=ATF::demande_refi()->select($infos["id_demande_refi"]);
			$infos["prix"]=$demande_refi["loyer_actualise"];
			$infos["id_refinanceur"]=$demande_refi["id_refinanceur"];
			unset($infos["date_periode_debut"],$infos["date_periode_fin"]);
		}elseif($infos["type_facture"]=="libre"){

			$infos["prix"]=$infos["prix_libre"];
			$infos["date_periode_debut"]=$infos["date_periode_debut_libre"];
			$infos["date_periode_fin"]=$infos["date_periode_fin_libre"];


			if($infos["type_libre"] == "contentieux") $infos["tva"] = 1;

		}elseif($infos["type_facture"]=="midas"){
			unset($infos_ligne_repris , $infos_ligne_non_visible , $infos_ligne);
			$infos["prix"]=$infos["prix_midas"];
			$infos["commentaire"] = $infos["periode_midas"];
		}elseif($infos["type_facture"]=="facture"){
			if (ATF::$codename == "go_abonnement") {
				$facturation= ATF::facturation()->next_echeance($commande['id_affaire']);
			} else {
				$facturation= ATF::facturation()->periode_facturation($commande['id_affaire']);
			}


			if($facturation){
				if(($infos["date_periode_debut"]) && ($infos["date_periode_fin"])){
					$date = explode("-", $infos["date_periode_debut"]);
					$date_periode_debut = $date[2]."-".$date[1]."-".$date[0];
					$infos["date_periode_debut"] = $date_periode_debut;
					$date = explode("-", $infos["date_periode_fin"]);
					$date_periode_fin = $date[2]."-".$date[1]."-".$date[0];
					$infos["date_periode_fin"] = $date_periode_fin;

					if(($infos["date_periode_debut"] >= $facturation["date_periode_debut"]) && ($infos["date_periode_fin"] <= $facturation["date_periode_fin"])){
						//On est dans une periode de l'echeancier
						if($facturation["id_facture"]) throw new errorATF("Il existe déjà une facturation pour cette période.",349);
					}else{
						if (ATF::$codename == "go_abonnement") {
							$facturation = ATF::facturation()->next_echeance($commande['id_affaire'],true);
						} else {
							$facturation = ATF::facturation()->periode_facturation($commande['id_affaire'],true);
						}

					}
				}else{
					$infos["date_periode_debut"] = $facturation["date_periode_debut"];
					$infos["date_periode_fin"] = $facturation["date_periode_fin"];

					$infos["date_periode_debut"] = $facturation["date_periode_debut"];
					$infos["date_periode_fin"] = $facturation["date_periode_fin"];
					if($facturation["id_facture"]){
					  throw new errorATF("Il existe déjà une facturation pour cette période.",349);
					}
				}
			}
		}


		if($infos["type_facture"]!="refi"){
			unset($infos["id_refinanceur"],$infos["id_demande_refi"]);
		}
		unset($infos["periode_midas"],$infos["prix_midas"]);
		unset($infos["date_periode_debut_libre"],$infos["date_periode_fin_libre"],$infos["prix_libre"]);

		//Gestion mail
		if($envoyerEmail){
			$email["email"]=$infos["email"];
			$email["emailCopie"]=$infos["emailCopie"];
			$email["texte"]=$infos["emailTexte"];
			$infos["envoye_mail"]=$infos["email"];
			//Si on est dans une facturation, il faut passer la facturation a 'envoye'
			if($infos["type_facture"]=="facture" && $facturation){
				$facturation["envoye"]='oui';
			}
		}else{
			$email=false;
		}

		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"]);
		$infos["id_user"] = ATF::$usr->getID();
		$societe=ATF::societe()->select($infos["id_societe"]);

		if(!$infos["prix"]){
			throw new errorATF("Il faut un prix pour la facture",351);
		}


		if($infos["tva"] == "1.196"){
			$infos["tva"]= "1.2";
		}

		if (ATF::$codename == "bdomplus" || ATF::$codename == "boulanger" || ATF::$codename == "go_abonnement") {
			$infos["ref_externe"] = $this->getRefExterne();
		}


		ATF::db($this->db)->begin_transaction();

	//*****************************Transaction********************************

		////////////////Affaire
		ATF::affaire()->u(array("id_affaire"=>$infos["id_affaire"],"etat"=>"facture"));


		if(!$commande["mise_en_place"]){
			ATF::commande()->u(array("id_commande"=> $infos["id_commande"] , "mise_en_place" => date("Y-m-d")));
		}

		////////////////Facture
		unset($infos["marge"],$infos["marge_absolue"],$infos["prix_achat"]);

		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		////////////////Facturation
		if($infos["type_facture"]=="facture" && $facturation){
			$facturation["id_facture"]=$last_id;
			ATF::facturation()->u($facturation);
		}

		////////////////Facture Ligne
		//Lignes reprise
		if($infos_ligne_repris){
			foreach($infos_ligne_repris as $key=>$item){
				$infos_ligne[]=$infos_ligne_repris[$key];
			}
		}

		//Lignes non visibles
		if($infos_ligne_non_visible){
			foreach($infos_ligne_non_visible as $key=>$item){
				$infos_ligne_non_visible[$key]["facture_ligne__dot__visible"]="non";
				$infos_ligne[]=$infos_ligne_non_visible[$key];
			}
		}

		//Lignes
		if($infos_ligne){
			$infos_ligne=ATF::devis()->extJSUnescapeDot($infos_ligne,"facture_ligne");
			foreach($infos_ligne as $key=>$item){
				if($item["id_facture_ligne"]){
					$commande_ligne=ATF::commande_ligne()->select($item["id_facture_ligne"]);
					$item["id_affaire_provenance"]=$commande_ligne["id_affaire_provenance"];
					unset($item["id_facture_ligne"]);
				}
				$item["id_facture"]=$last_id;
				ATF::facture_ligne()->i($item,$s);
			}
		}


		//Dans le cas d'une vente les parcs doivent passer en actif
		if(ATF::affaire()->select($infos["id_affaire"],"nature")=="vente"){
			ATF::commande()->u(array("id_commande"=>$commande["id_commande"],"etat"=>"vente"));
			$commande = new commande_cleodis($commande['id_commande']);
			$affaire = $commande->getAffaire();
			$affaire_parente = $affaire->getParentAvenant();
			ATF::parc()->updateExistenz($commande,$affaire,$affaire_parente);
		}

	//*****************************************************************************
		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			/* MAIL */
			if($email){
				$path=array("facture"=>"fichier_joint");
				ATF::affaire()->mailContact($email,$last_id,"facture",$path);
			}
			//Seulement si le profil le permet
			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){
			ATF::affaire()->redirection("select",$infos["id_affaire"]);
		}
		return $last_id;
	}

	/**
    * Exposition de la fonction updateDate via l'API de'Optima
    * @author Quentin JANON <qjanon@absystech.fr>
    */
	public function _updateDate ($infos) {

		ATF::$usr->set('id_user',16);
		ATF::$usr->set('id_agence',1);

		if(ATF::facture()->select($infos["id_facture"], "date_rejet")){
            $infos["key"] = "date_regularisation";
            $this->updateDate($infos);

            if(!ATF::facture()->select($infos["id_facture"], "date_paiement")){
                $this->updateDate(array("id_facture" => $infos["id_facture"],"key"=> "date_paiement", "value" => $infos["value"]));
            }

        }else{
            $this->updateDate($infos);
        }
	}

	public function _createFactureLibre($get, $post){


		try{
			if(!$post) throw new errorATF("DATA_MANQUANTE", 400);

			if(!$post['type_libre']) throw new errorATF("TYPE_LIBRE EST OBLIGATOIRE", 400);
			if(!$post['date']) throw new errorATF("LA DATE EST OBLIGATOIRE", 400);
			if(!$post['prix_libre']) throw new errorATF("LE PRIX_LIBRE EST OBLIGATOIRE", 400);
			if(!$post['id_affaire']) throw new errorATF("ID_FACTURE EST OBLIGATOIRE", 400);
			if(!$post['type_facture']) throw new errorATF("LE TYPE_FACTURE EST OBLIGATOIRE", 400);
			if($post['type_facture'] !== "libre") throw new errorATF("LE TYPE DE FACTURE DOIT ETRE LIBRE", 400);
			unset($post['schema']);
			$facture = $post;


			$affaire = ATF::affaire()->select($post["id_affaire"]);
			if(!$affaire['id_societe']) {
				throw new errorATF("ID_SOCIETE INTROUVABLE", 404);
			} else {
				$facture['id_societe'] = $affaire['id_societe'];
			}


			ATF::commande()->q->reset()->where("commande.id_affaire", $post["id_affaire"]);
			$commande = ATF::commande()->select_row();
			if (!$commande['commande.id_commande']) {
				throw new errorATF("ID_COMMANDE INTROUVABLE", 404);
			} else {
				$facture['id_commande'] = $commande['commande.id_commande'];
			}

			ATF::commande_ligne()->q->reset()->where("commande_ligne.id_commande", $facture['id_commande']);
			$lignes = ATF::commande_ligne()->select_all();
			foreach ($lignes as $key => $value) {
				$ligne = array();
				$ligne["facture_ligne__dot__produit"] = $value["produit"];
				$ligne["facture_ligne__dot__quantite"] = $value["quantite"];
				$ligne["facture_ligne__dot__ref"] = $value["ref"];
				$ligne["facture_ligne__dot__id_fournisseur_fk"] = $value["id_fournisseur"];
				$ligne["facture_ligne__dot__prix_achat"] = $value["prix_achat"];
				$ligne["facture_ligne__dot__id_produit"] = $value["produit"];
				$ligne["facture_ligne__dot__id_produit_fk"] = $value["id_produit"];
				$ligne["facture_ligne__dot__serial"] = $value["serial"];
				$ligne["facture_ligne__dot__afficher"] = $value["visible_pdf"];
				$ligne["facture_ligne__dot__visible"] = $value["visible"];
				$ligne["facture_ligne__dot__id_facture_ligne"] = $value["id_commande_ligne"];

				$produits[] = $ligne;
			}
			$produits = json_encode($produits);

			$data = [
				"facture" => $facture,
				"values_facture" => [
					"produits" => $produits
				]
				];

			$return = $this->insert($data,$s,NULL,$var=NULL,NULL,true);

		} catch(errorATF $e){
			$msg = $e->getMessage();
			throw new errorATF('Error:'.$msg, 500);
			return false;
		}
	}



	public function updateDate($infos){

		if ($infos['value'] == "undefined") $infos["value"] = "";
		$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
		$infosMaj["id_".$this->table]=$infos["id_".$this->table];
		$infosMaj[$infos["key"]]=$infos["value"];

		$infos["id_facture"] = ATF::facture()->decryptId($infos["id_facture"]);
		$facture = ATF::facture()->select($infos["id_facture"]);

		if ($infos["key"] == "date_envoi") {

			if ($facture["envoye"] === "oui") {
				throw new errorATF("Facture déja envoyée", 400);
			} else {
				if ($infos["value"] && date("Ymd", strtotime($infos["value"])) < date("Ymd")) throw new errorATF("Impossible de prévoir un envoi anterieur à la date du jour !!", 400);

				ATF::facture()->u($infosMaj);
			}

		} else {
			if(($infos["key"] == "date_rejet") || ($infos["key"] == "date_regularisation")) {

				if($infos["key"] == "date_regularisation") {
					$this->updateEnumRejet($infos);

					if ($infos["value"] != "" && $infos["value"] != NULL) {
						if ($facture["date_paiement"] == NULL) {
							$infosMaj["date_paiement"] = $infos["value"];
						}
					} else {
						if ($facture["date_paiement"]) {
							$infosMaj["date_paiement"] = NULL;
						}
					}
				}

				if ($infos["key"] == "date_rejet") {
					if ($infos["value"] == "" || $infos["value"] == NULL) {
						if ( $facture["date_regularisation"] ) {
							$infosMaj["date_regularisation"] = NULL;
						}
					}
				}

				$infosMaj["id_facture"] = $infos["id_facture"];

				if($this->u($infosMaj)) {
					ATF::$msg->addNotice(
						loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
						,ATF::$usr->trans("notice_success_title")
					);
				}

				$factureAfter = ATF::facture()->select($infosMaj["id_facture"]);
				if ($factureAfter["date_paiement"] && !$factureAfter["date_rejet"]) {
					ATF::facture()->u(array("id_facture"=> $infos["id_facture"], "etat" => "payee"));
				} else {
					if ($factureAfter["date_regularisation"]) {
						ATF::facture()->u(array("id_facture"=> $infos["id_facture"], "etat" => "payee"));
					} else {
						ATF::facture()->u(array("id_facture"=> $infos["id_facture"], "etat" => "impayee"));
					}
				}

				$commande = $this->select($infos["id_facture"] , "facture.id_commande");
				ATF::commande()->checkEtatContentieux($commande);

				// log::logger("--> Appel Mauvais payeur" , "mauvais_payeur");
				ATF::societe()->checkMauvaisPayeur($this->select($this->decryptId($infos["id_facture"]) , "id_societe"));

				// log::logger("################################# FIN", "updateDate");
				return true;
			} else {
				switch($infos["key"]) {
					case "date_paiement" :
						// SUPPRESSION DE LA DATE DE PAIEMENT
						if ($infos["value"] == "" || $infos["value"] == NULL) {
							if ($facture["date_rejet"] == NULL && $facture["rejet"] != "non_rejet") {
								$infosMaj["date_rejet"] = date("Y-m-d", strtotime("-1 days"));
							}
							if($facture["date_regularisation"]) $infosMaj["date_regularisation"] = NULL;
						} else {
							if ($facture["date_rejet"] !== NULL && $facture["date_regularisation"] === NULL){
								$infosMaj["date_regularisation"] = $infos["value"];
							}
						}
					break;

				}

				if($infosMaj[$infos["key"]]) {
					$infosMaj["etat"]="payee";
				} else {
					$infosMaj["etat"]="impayee";
				}


				if($this->u($infosMaj)) {
					ATF::$msg->addNotice(
						loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
						,ATF::$usr->trans("notice_success_title")
					);
				}

				$factureAfter = ATF::facture()->select($infosMaj["id_facture"]);
				if ($factureAfter["date_paiement"] && !$factureAfter["date_rejet"]) {
					ATF::facture()->u(array("id_facture"=> $infos["id_facture"], "etat" => "payee"));
				} else {
					if ($factureAfter["date_regularisation"]) {
						ATF::facture()->u(array("id_facture"=> $infos["id_facture"], "etat" => "payee"));
					} else {
						ATF::facture()->u(array("id_facture"=> $infos["id_facture"], "etat" => "impayee"));
					}
				}
			}

			$commande = $this->select($infos["id_facture"] , "facture.id_commande");
			ATF::commande()->checkEtatContentieux($commande);

			// log::logger("--> Appel Mauvais payeur" , "mauvais_payeur");
			ATF::societe()->checkMauvaisPayeur($this->select($this->decryptId($infos["id_facture"]) , "id_societe"));

			// log::logger("################################# FIN", "updateDate");

		}

		return true;
	}

	/**
    * Fonction qui permet de mettre à jour la date
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    * @param array $infos date garantie
    * @param type pour savoir si l'on cherche une affaire qui annule  et remplace ($type=='new') ou une affaire qui EST annulée et remplacée ($type=='old')
    * @return boolean à true si la transaction c'est bien passé
    */
	public function updateEnumRejet($infos) {

		if($infos["key"] == "rejet") {
			$infosMaj["id_".$this->table]=$infos["id_".$this->table];
			$facture = $this->select($this->decryptId($infos["id_".$this->table]));

			if ($infos["value"] != "non_rejet") {
				$this->updateDate(array(
					"id_facture" => $this->decryptId($infos["id_".$this->table]),
					"key" => "date_rejet",
					"value" => $infos["date_rejet"] ? date("Y-m-d", strtotime($infos["date_rejet"])) : date("Y-m-d", strtotime("-1 days"))
				));
			} else {
				if ($facture["date_rejet"]) {
					$this->updateDate(array(
						"id_facture" => $this->decryptId($infos["id_".$this->table]),
						"key" => "date_rejet",
						"value" => ""
					));
				}
			}
		}


		if($this->select($infos["id_".$this->table],"etat")=="impayee" || $infos["key"] == "rejet") {

			if ($infos['value'] == "undefined") $infos["value"] = "";
			$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
			$infosMaj["id_".$this->table]=$infos["id_".$this->table];
			$infosMaj[$infos["key"]]=$infos["value"];

			if($this->u($infosMaj)){
				ATF::$msg->addNotice(
					loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
					,ATF::$usr->trans("notice_success_title")
				);
			}

		} else {
			throw new errorATF("Impossible de modifier ce ".ATF::$usr->trans($this->table)." car elle est en '".ATF::$usr->trans("payee")."'",877);
		}
		$commande = $this->select($infos["id_facture"] , "facture.id_commande");
		ATF::commande()->checkEtatContentieux($commande);

		// log::logger("--> Appel Mauvais payeur" , "mauvais_payeur");
		ATF::societe()->checkMauvaisPayeur($this->select($this->decryptId($infos["id_facture"]) , "id_societe"));
		return true;
	}


	public function contientFactureRejetee($id_commande, $FactureEnCours){
		$idFactEnCours = $this->decryptId($FactureEnCours);
		$this->q->reset()->where("facture.id_commande", $id_commande)->addField("facture.rejet")->addField("facture.date_regularisation");
		$res = $this->select_all();
		foreach($res as $k=>$v){
			if($idFactEnCours !== $v["facture.id_facture"] ){
				if(($v["facture.rejet"] != "non_rejet") && ($v["facture.rejet"] != "non_preleve_mandat") && (!$v["facture.date_regularisation"])){
					return 1;
				}
			}
		}
		return 0;
	}

	/**
    * Retourne la valeur du texte d'email, appelé en Ajax
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @return string texte du mail
    */
	public function majMail($id_societe){
		return nl2br("Bonjour,\n\nCi-joint la facture pour la société ".ATF::societe()->nom($id_societe).".\nFacture éditée le ".date("d/m/Y").".\n");
	}

	/**
	* Impossible de supprimer une facture payee
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_delete($id){
		if($this->select($id,"etat")=="impayee"){
			return true;
		}else{
			throw new errorATF("Impossible de supprimer ce ".ATF::$usr->trans($this->table)." car elle est en '".ATF::$usr->trans("payee")."'",879);
		}
	}

	/**
	* Impossible de modifier une facture payee
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_update($id,$infos=false){
		throw new errorATF("Impossible de modifier une ".ATF::$usr->trans($this->table),878);
	}

	 public function export_special2($infos){
	 	$infos["rejet"] = "ok";
	 	$this->export_special($infos);
	 }

     /** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	 public function export_special($infos){
	 	 if($infos["rejet"]) $rejet = "oui";
         $this->q->reset();

         $this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

         $this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
         $infos = $this->sa();

		 if($rejet) $infos["rejet"] = "ok";

		 $this->export_xls_special($infos);
     }

     /** Surcharge pour avoir un export identique Ã  celui de Nebula
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $infos : contient tous les enregistrements
     */
     public function export_xls_special(&$infos){
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

		$workbook = new PHPExcel;

		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$workbook->setActiveSheetIndex(0);
		$sheet = $workbook->getActiveSheet();
		$sheet->setTitle('Autoporté');

		//mise en place des titres
		$this->ajoutTitre($sheet);

		//ajout des donnÃ©es
		if($infos){
			$this->ajoutDonnees($sheet,$infos);
			foreach($infos as $facture){
				ATF::facture()->u(array("id_facture" => $facture['facture.id_facture_fk'] , "exporte" => "oui"));
			}
		}

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_comptable.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		ob_end_clean();
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
		// Pour remettre la prÃ©cision correcte ! sinon ini_set('precision',14)... sinon ca provoque un pb avec php > var_dump((1.196-1)*100); => float(19.59999999999999432) ( https://bugs.php.net/bug.php?id=55368 )
     }

     /** Mise en place des titres
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $sheets : contient les 5 onglets
     */
     public function ajoutTitre(&$sheet){
        $row_data = array(
        	"A"=>'Type'
        	,"B"=>'Date'
			,"C"=>'Journal'
			,"D"=>'Général'
			,"E"=>'Auxiliaire'
			,"F"=>'Sens'
			,"G"=>'Montant'
			,"H"=>'Libellé'
			,"I"=>'Référence'
			,"J"=>'Section A1'
			,"K"=>'Date début'
			,"L"=>'Date de fin'
			,"M"=>'Date de prélèvement'
			,"N"=>'Refinancement'
		);

		 $i=0;
		 foreach($row_data as $col=>$titre){
			$sheet->setCellValueByColumnAndRow($i , 1, $titre);
			$i++;
        }
     }

 	/**
    * Permet d'avoir le code client sur le select_all
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q
    		 ->addJointure("facture","id_societe","societe","id_societe")
    		 ->addJointure("facture","id_commande","commande","id_commande")
			 ->addField("societe.code_client");

		$return = parent::select_all($order_by,$asc,$page,$count);
        foreach ($return['data'] as $k=>$i) {
            $return['data'][$k]['numRelance'] = ATF::relance()->getNumeroDeRelance($i['facture.id_facture']);
            if ($idr1 = ATF::relance()->getIdRelance($i['facture.id_facture'],"premiere")) {
                $return['data'][$k]['id_relance_premiere'] = ATF::relance()->cryptId($idr1);
                $r = ATF::relance()->getIdFactures($idr1,$i['facture.id_facture']);
                $return['data'][$k]['id_autreFacture'] = $r['id_autreFacture'];
                $return['data'][$k]['ref_autreFacture'] = $r['ref_autreFacture'];

            }
            if ($idr2 = ATF::relance()->getIdRelance($i['facture.id_facture'],"seconde")) $return['data'][$k]['id_relance_seconde'] = ATF::relance()->cryptId($idr2);
            if ($idr3 = ATF::relance()->getIdRelance($i['facture.id_facture'],"mise_en_demeure")) $return['data'][$k]['id_relance_mise_en_demeure'] = ATF::relance()->cryptId($idr3);
            $return['data'][$k]['allowRelance'] = $i['facture.etat']=="payee"?false:true;
        }

        return $return;
	}

     /** Mise en place du contenu
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $sheets : contient les 5 onglets
     * @param array $infos : contient tous les enregistrements
     */
     public function ajoutDonnees(&$sheet,$infos){

		$row_auto=1;
		$increment=0;
		foreach ($infos as $key => $item) {
			$increment++;
			if($item){
				//initialisation des données
				$devis=ATF::devis()->select_special("id_affaire",$item['facture.id_affaire_fk']);
				$infos_commande=ATF::commande()->select($this->select($item['facture.id_facture_fk'], "id_commande"));
				$societe = ATF::societe()->select($item['facture.id_societe_fk']);
				if($id_refinanceur = ATF::demande_refi()->id_refinanceur($item['facture.id_affaire_fk'])){
					$refinanceur=ATF::refinanceur()->select($id_refinanceur);
				}else{
					$refinanceur=NULL;
				}

	 			$date=date("Y-m-d",strtotime($item['facture.date']));
				$affaire=ATF::affaire()->select($item['facture.id_affaire_fk']);

				if($increment>999){
					$reference="F".date("ym",strtotime($item['facture.date'])).$increment;
				}elseif($increment>99){
					$reference="F".date("ym",strtotime($item['facture.date']))."0".$increment;
				}elseif($increment>9){
					$reference="F".date("ym",strtotime($item['facture.date']))."00".$increment;
				}else{
					$reference="F".date("ym",strtotime($item['facture.date']))."000".$increment;
				}

				// Récupération de : Date de debut, de fin et de prélèvement
				$dateDebut = ($item['facture.date_periode_debut']) ? " ".date("d/m/y",strtotime($item['facture.date_periode_debut'])) : " ";
				$dateFin = ($item['facture.date_periode_fin']) ? " ".date("d/m/y",strtotime($item['facture.date_periode_fin'])) : " ";

				if($affaire["date_previsionnelle"] < 0){
					$datePrelevement = date("Y-m-d",strtotime($item['facture.date_periode_debut']." ".$affaire['date_previsionnelle']." DAY"));
				}else{
					$datePrelevement = date("Y-m-d",strtotime($item['facture.date_periode_debut']." + ".$affaire['date_previsionnelle']." DAY"));
				}


				$refinancement = "";

				ATF::demande_refi()->q->reset()->where("id_affaire",$item['facture.id_affaire_fk'],"AND")
											   ->where("etat","valide");
				$ResRefinancement = ATF::demande_refi()->select_row();

				if($ResRefinancement){
					$refinancement = ATF::refinanceur()->select($ResRefinancement["id_refinanceur"] , "refinanceur");
				}

				$choix = "defaut";

				if($item['facture.type_facture'] == "libre" && in_array($item["facture.type_libre"], ["cout_copie", "transfert"])){
					if($item["facture.type_libre"] == "cout_copie") $choix = "libre_cout_copie";
					if($item["facture.type_libre"] == "transfert") $choix = "libre_transfert";
				}elseif($item['facture.type_facture']=='refi'){
					if($refinancement == "FRANFINANCE") $choix = "refi_refinanceur_SGEF";
					elseif($refinancement == "CLEOFI") $choix = "refi_refinanceur_CLEOFI";
					else  $choix = "refi_autre";
				}elseif ($devis[0]["tva"] == 1) {
					$choix = "facture_sans_tva";
				}else{
					if($affaire['nature']=="vente"){
						$choix = "affaire_vente";
						// avoir sur affaire de vente
						if( $item["facture.prix"] < 0 ) {
							$choix = "avoir_affaire_vente";
						}
					}else{
						// avoir
						if( $item["facture.prix"] < 0 ) {
							if ($item["facture.date_periode_debut"] && $item["facture.date_periode_fin"]) {
								// On recherce si il y a une facture sur la meme periode, avoir qui annule cette facture
								ATF::facture()->q->reset()->where("facture.date_periode_debut", $item["facture.date_periode_debut"], "AND")
														  ->where("facture.date_periode_fin", $item["facture.date_periode_fin"], "AND")
														  ->where("facture.ref", $item["facture.id_facture"], "AND", null, '!=');
								$facture_avoir = ATF::facture()->select_row();
								if ($facture_avoir && ATF::facture()->select($facture_avoir["facture.id_facture"], "nature") === 'prolongation') {
									$choix = "avoir_sur_prolongation";
								} elseif ( in_array($infos_commande['etat'], ['mis_loyer', 'mis_loyer_contentieux']) ) {
									// Avoir sur un contrat en cours
									$choix = "avoir_affaire_en_cours";
								}
							}
						}
						//Prolongation
						elseif($item['facture.date_periode_debut'] && $infos_commande['date_debut'] && $infos_commande['date_evolution']
															   && ($item['facture.date_periode_debut']>$infos_commande['date_evolution'])){
							$choix = "prolongation";
						}
						// Pro rata
						elseif( ($item['facture.date_periode_debut'] && $infos_commande['date_debut'] && ($item['facture.date_periode_debut']<$infos_commande['date_debut']))
							  || ($item["facture.nature"] === "prorata")){
							$choix = "pro_rata";
						}
						else{
							if($item['facture.date_periode_debut']) {
								//Si le contrat est en cours pendant la période de la facture, pas d'analytique
								if(strtotime($infos_commande["date_debut"]) <= strtotime($item['facture.date_periode_debut']) && strtotime($infos_commande["date_evolution"]) >=  strtotime($item['facture.date_periode_fin'])){
								   	$en_cours = true;
								}else{ $en_cours = false; }

								if( ATF::$codename == "cleodisbe") {
									$choix = "affaire_en_cours_cleodisbe";
								} else {
									//Affaire non refi ou refinancée par CLEODIS
									if(!$ResRefinancement || ($ResRefinancement && $refinancement == "CLEODIS") && $en_cours){
										$choix = "affaire_non_refi_ou_refi_cleodis_ac_date_deb_facture";
									//Affaire en cours et refinancée par BMF
									}elseif($ResRefinancement && $refinancement == "BMF"){
										$choix = "affaire_en_cours_refi_bmf";
									}elseif(($refinanceur['refinanceur']=='CLEOFI' || $refinanceur['refinanceur']=='FRANFINANCE') && $en_cours){
										$choix = "affaire_en_cours_refi_cleofi_sgef";
									}
								}
							}
						}
					}
				}

				$h = $item['facture.id_facture']."-".$societe['code_client'];

				//$h = 'F'.$affaire['ref'].'-'.$societe['code_client'].'/'.$societe['societe'];
				$ligne[1] = array("D"=> "411000" , "H"=> $h);
				$ligne[2] = array("D"=> "706400" , "H"=> $h);
				$ligne[3] = array("D"=> "706400" , "H"=> $h);
				$ligne[4] = array("D"=> "445710" , "H"=> $h);

				if ($item["facture.prix"] < 0 ) {
					$ligne[4]["F"] = "D";
				}
				$libelle = $societe['code_client'];

				switch ($choix) {
					case 'libre_cout_copie':
						$ligne[1]["D"] ="411000";
						$ligne[2]["D"] ="706220";
						$ligne[3]["D"] ="706220";
						$ligne[4]["D"] ="445714";
					break;

					case 'libre_transfert':
						$ligne[1]["D"] = "411000";
						$ligne[2]["D"] = "706400";
						$ligne[3]["D"] = "706400";
					break;

					case 'refi_refinanceur_SGEF':
						$libelle = $refinanceur["code_refi"];
						$ligne[1]["D"] =  "411300";
						$ligne[2]["D"] =  "707110";
						$ligne[3]["D"] =  "707110";
						$ligne[4]["H"] = $h;
					break;

					case 'refi_refinanceur_CLEOFI':
						$libelle = $refinanceur["code_refi"];
						$ligne[1]["D"] =  "411200";
						$ligne[2]["D"] =  "758100";
						$ligne[3]["D"] =  "758100";
						$ligne[4]["H"] = $h;
					break;

					case 'refi_autre':
						$libelle = $refinanceur["code_refi"];
						$ligne[1]["D"] =  "411300";
						$ligne[2]["D"] =  "707110";
						$ligne[3]["D"] =  "707110";
					break;

					case 'affaire_vente':
						$ligne[2]["D"] = "707110";
						$ligne[3]["D"] = "707110";
					break;

					case 'avoir_affaire_vente':
						$ligne[2]["D"] =  "707110";
						$ligne[3]["D"] =  "707110";
						$ligne[4]["F"] ="D";
					break;

					case 'avoir_affaire_en_cours':
						$ligne[2]["D"] = "706200";
						$ligne[3]["D"] = "706200";
						$ligne[4]["D"] = "445712";
						$ligne[4]["F"] ="D";
					break;

					case 'prolongation':
						$ligne[2]["D"] = "706230";
						$ligne[3]["D"] = "706230";
						$ligne[4]["D"] = "445713";
						if (ATF::$codename == "cleodisbe") {
							$ligne[4]["D"] = "445710";
						}
					break;

					case 'avoir_sur_prolongation':
						$ligne[1]["D"] ="411000";
						$ligne[2]["D"] ="706230";
						$ligne[3]["D"] ="706230";
						$ligne[4]["D"] ="445713";
						$ligne[4]["F"] ="D";
					break;

					case 'pro_rata':
						$ligne[2]["D"] = "706300";
						$ligne[3]["D"] = "706300";
						$ligne[4]["D"] = "445715";
					break;

					case 'affaire_non_refi_ou_refi_cleodis_ac_date_deb_facture':
						$ligne[2]["D"] = "706200";
						$ligne[3]["D"] = "706200";
						$ligne[4]["D"] = "445712";
					break;

					case 'affaire_en_cours_refi_cleofi_sgef':
						if($refinanceur['refinanceur']=='CLEOFI'){
							$ligne[2]["D"] = "706200";
							$ligne[3]["D"] = "706200";
							$ligne[4]["D"] = "445712";

						}else{
							$ligne[2]["D"] = "467800";
							unset($ligne[3]);
							unset($ligne[4]);
							$ligne[2]["G"] = round(abs($item['facture.prix']*$item['facture.tva']),2);
						}


					break;

					case 'affaire_en_cours_refi_bmf':
						$h = "B".substr($societe["code_client"],1);
						$ligne[1]["H"] = $h;
						$ligne[2] = array("D"=> "706230" , "H"=> $h);
						$ligne[3] = array("D"=> "706230" , "H"=> $h);
						$ligne[4] = array("D"=> "445713" , "H"=> $h);
					break;

					case 'affaire_en_cours_cleodisbe':
						$ligne[2]["D"] = "706200";
						$ligne[3]["D"] = "706200";
						$ligne[4]["D"] = "445710";
					break;

					case 'facture_sans_tva':
						$ligne[2]["D"] = "706900";
						$ligne[3]["D"] = "706900";
						unset($ligne[4]);
					break;
				}

				//insertion des donnÃ©es
				for ($i = 1; $i <= 6; $i++) {
					$row_data=array();

					if($i == 1){
						$row_data["A"] = 'G';
						$row_data["B"] = $date;
						$row_data["C"] = 'VEN';
						$row_data["D"] = $ligne[1]["D"];
						$row_data["E"] = $libelle;
						if($item['facture.prix']<0){
							$row_data["F"] = 'C';
						}else{
							$row_data["F"] = 'D';
						}
						$row_data["G"] = round(abs($item['facture.prix']*$item['facture.tva']),2);
						$row_data["H"] = $ligne[$i]["H"];
						$row_data["I"] = $reference;
						$row_data["J"] = "";
						$row_data["K"] = $dateDebut;
						$row_data["L"] = $dateFin;
						$row_data["M"] = $datePrelevement;
						$row_data["N"] = $refinancement;
					}elseif($i==2){
						$row_data["A"] = 'G';
						$row_data["B"] = $date;
						$row_data["C"] = 'VEN';
						$row_data["D"] = $ligne[$i]["D"];
						$row_data["E"] = "";
						if($item['facture.prix']<0){
							$row_data["F"] = 'D';
						}else{
							$row_data["F"] = 'C';
						}
						$row_data["G"] = $ligne[$i]["G"] ? $ligne[$i]["G"] : abs($item['facture.prix']);
						$row_data["H"] = $ligne[$i]["H"];
						$row_data["I"] = $reference;
						$row_data["J"] = "";
						$row_data["K"] = $dateDebut;
						$row_data["L"] = $dateFin;
						$row_data["M"] = $datePrelevement;
						$row_data["N"] = $refinancement;
					}elseif($i==3 && $ligne[3]){
						$row_data["A"] = 'A1';
						$row_data["B"] = $date;
						$row_data["C"] = 'VEN';
						$row_data["D"] = $ligne[$i]["D"];
						$row_data["E"] = "";
						if($item['facture.prix']<0){
							$row_data["F"] = 'D';
						}else{
							$row_data["F"] = 'C';
						}
						$row_data["G"] = abs($item['facture.prix']);
						$row_data["H"] = $ligne[$i]["H"];
						$row_data["I"] = $reference;
						if($affaire["nature"] =="avenant"){
							//Faire en sorte que l1296 = 2008 et non pas 208
							$row_data["J"] = " 20".substr($affaire["ref"],0,7).$societe["code_client"]."AV ";
						}else{
							$row_data["J"] = " 20".substr($affaire["ref"],0,7).$societe["code_client"]."00 ";
						}
						$row_data["K"] = $dateDebut;
						$row_data["L"] = $dateFin;
						$row_data["M"] = $datePrelevement;
						$row_data["N"] = $refinancement;

					}elseif($i==4 && $ligne[4]){
						$row_data["A"] = 'G';
						$row_data["B"] = $date;
						$row_data["C"] = 'VEN';
						$row_data["D"] = $ligne[$i]["D"];
						$row_data["E"] = "";
						$row_data["F"] = $ligne[$i]["F"] ? $ligne[$i]["F"] : 'C';
						$row_data["G"] = round(abs(($item['facture.prix']*$item['facture.tva'])-$item['facture.prix']),2);
						$row_data["H"] = $ligne[$i]["H"];
						$row_data["I"] = $reference;
						$row_data["J"] = "";
						$row_data["K"] = $dateDebut;
						$row_data["L"] = $dateFin;
						$row_data["M"] = $datePrelevement;
						$row_data["N"] = $refinancement;
					}

					if($item['facture.mode_paiement'] == "pre-paiement"){
						if($i==5){
							$row_data["A"]='G';
							$row_data["B"]=" ".$date;
							$row_data["C"]='OD1';
							$row_data["D"]="401000";
							$row_data["E"] = "";
							if($item["facture.id_fournisseur_prepaiement_fk"]) $row_data["E"] = ATF::societe()->select($item["facture.id_fournisseur_prepaiement_fk"], "code_fournisseur");
							$row_data["F"]='D';
							$row_data["G"]=round(abs($item['facture.prix']*$item['facture.tva']),2);
							$row_data["H"]=$libelle;
							$row_data["I"]="OD000001";
							$row_data["J"]="";
							$row_data["I"]="OD000001";
							$row_data["K"]=$dateDebut;
							$row_data["L"]=$dateFin;
							$row_data["M"]=$datePrelevement;
							$row_data["N"]=$refinancement;
						}elseif($i==6){
							$row_data["A"]='G';
							$row_data["B"]=" ".$date;
							$row_data["C"]='OD1';
							$row_data["D"]="411000";
							$row_data["E"]="B".substr($societe["code_client"],1);
							$row_data["F"]='C';
							$row_data["G"]=round(abs($item['facture.prix']*$item['facture.tva']),2);
							$row_data["H"]=$libelle;
							$row_data["I"]="OD000001";
							$row_data["J"]="";
							$row_data["K"]=$dateDebut;
							$row_data["L"]=$dateFin;
							$row_data["M"]=$datePrelevement;
							$row_data["N"]=$refinancement;
						}
					}

					if($row_data){
						$indexCol = 0;
						if($infos["rejet"]){
							if($row_data["G"] != 0){
								$row_auto++;
								foreach($row_data as $col=>$valeur){
									if (($col === "B" || $col === "M") && $valeur ) {
										$dateTime = new DateTime($valeur);
										$sheet->setCellValueByColumnAndRow($indexCol , $row_auto, PHPExcel_Shared_Date::PHPToExcel( $dateTime ));
										$sheet->getStyleByColumnAndRow($indexCol , $row_auto)->getNumberFormat()->setFormatCode('ddmmyyyy');
									} else {
										$sheet->setCellValueByColumnAndRow($indexCol , $row_auto, $valeur);
									}
									$sheet->getColumnDimension($col)->setAutoSize(true);
									$indexCol++;
								}
							}
						}else{
							$row_auto++;
							foreach($row_data as $col=>$valeur){
								if (($col === "B" || $col === "M") && $valeur ) {
									$dateTime = new DateTime($valeur);
									$sheet->setCellValueByColumnAndRow($indexCol , $row_auto, PHPExcel_Shared_Date::PHPToExcel( $dateTime ));
									$sheet->getStyleByColumnAndRow($indexCol , $row_auto)->getNumberFormat()->setFormatCode('ddmmyyyy');
								} else {
									$sheet->setCellValueByColumnAndRow($indexCol , $row_auto, $valeur);
								}
								$sheet->getColumnDimension($col)->setAutoSize(true);
								$indexCol++;
							}
						}
					}
				}
			}
		}
	}


	/**
	 * Export comptable provenant d'optima Exporter > export CLEOFI
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 */
	 public function export_cleofi(&$infos){
	 	$infos["display"] = true;

     	$this->q->reset();
	 	$this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

     	$this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
    	$data = $this->sa();

     	require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle('Export CLEOFI');
		$sheets=array("auto"=>$worksheet_auto);
		$writer = new PHPExcel_Writer_Excel5($workbook);

		$this->remplissageExportCleofi($sheets,$data);


		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_CLEOFI.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();

	 }

	 public function remplissageExportCleofi(&$sheets, $data){
	 	//mise en place des titres
		$row_data = array(
        	"A"=>'Type'
        	,"B"=>'Date'
			,"C"=>'Journal'
			,"D"=>'Général'
			,"E"=>'Auxiliaire'
			,"F"=>'Sens'
			,"G"=>'Montant'
			,"H"=>'Libellé'
			,"I"=>'Référence'
			,"J"=>'Section A1'
		);

        foreach($sheets as $nom=>$onglet){
            foreach($row_data as $col=>$titre){
				  $sheets[$nom]->write($col.'1',$titre);
				  $sheets[$nom]->sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $row_auto=1;
		$increment=0;
		foreach ($data as $key => $item) {
			$increment++;
			if($item){
				//initialisation des données
				$devis=ATF::devis()->select_special("id_affaire",$item['facture.id_affaire_fk']);
				ATF::commande()->q->reset()->addField("commande.etat")->where("commande.id_affaire",$item['facture.id_affaire_fk']);
				$commande = ATF::commande()->select_row();

				if($commande["commande.etat"] !== "prolongation" && $commande["commande.etat"] !== "prolongation_contentieux"){

					$societe = ATF::societe()->select($item['facture.id_societe_fk']);
					if($id_refinanceur = ATF::demande_refi()->id_refinanceur($item['facture.id_affaire_fk'])){
						$refinanceur=ATF::refinanceur()->select($id_refinanceur);
					}else{
						$refinanceur=NULL;
					}

		 			$date=date("dmY",strtotime($item['facture.date']));
					$affaire=ATF::affaire()->select($item['facture.id_affaire_fk']);
					if($increment>999){
						$reference="F".date("ym",strtotime($item['facture.date'])).$increment;
					}elseif($increment>99){
						$reference="F".date("ym",strtotime($item['facture.date']))."0".$increment;
					}elseif($increment>9){
						$reference="F".date("ym",strtotime($item['facture.date']))."00".$increment;
					}else{
						$reference="F".date("ym",strtotime($item['facture.date']))."000".$increment;
					}

					$dateDebut = " ".date("d/m/y",strtotime($item['facture.date_periode_debut']));
					$dateFin = " ".date("d/m/y",strtotime($item['facture.date_periode_fin']));
					$datePrelevement = " ".date("dmY",strtotime($item['facture.date_periode_debut']." + ".$affaire['date_previsionnelle']." DAY"));

					$refinancement = "";

					ATF::demande_refi()->q->reset()->where("id_affaire",$item['facture.id_affaire_fk'],"AND")
												   ->where("etat","valide");
					$ResRefinancement = ATF::demande_refi()->select_row();

					if($ResRefinancement){
						$refinancement = ATF::refinanceur()->select($ResRefinancement["id_refinanceur"] , "refinanceur");
					}


					//exceptions
					if($item['facture.type_facture']=='refi'){
						$tiers = $refinanceur["code_refi"];
						$libelle = 'F'.$affaire['ref'].'-'.$societe['code_client'].'/'.$societe['societe'];
						if($infos["rejet"]){
							 $compte_2='771000';
						}else{
							$compte_2='707110';
						}
						$compte_3='445710';
					}elseif($item['facture.type_facture']!='ap'){
						$tiers = $societe['code_client'];
						$libelle = $item['facture.id_facture'].'-'.$societe['code_client'];
						$infos_commande=ATF::commande()->select($item['facture.id_commande_fk']);
						if($affaire['nature']=="vente"){
							if($infos["rejet"]){
							 	$compte_2='771000';
							}else{
								$compte_2='707110';
							}
							$compte_3='445710';
							$type="vente";
						}elseif($item['facture.date_periode_debut'] && $infos_commande['date_debut'] && $infos_commande['date_evolution'] && ($item['facture.date_periode_debut']>$infos_commande['date_evolution'])){
							if($infos["rejet"]){
							 	$compte_2='771000';
							}else{
								$compte_2='706230';
							}
							$compte_3='445713';
							$type="pro";
						}elseif($refinanceur['refinanceur']=='CLEODIS' || !$refinanceur && $item['facture.date_periode_debut']){
							if($infos["rejet"]){
							 	$compte_2='771000';
							}else{
								$compte_2='706200';
							}
							$compte_3='445712';
							$type="auto_porte";
						}elseif($item['facture.date_periode_debut'] && $infos_commande['date_debut'] && ($item['facture.date_periode_debut']<$infos_commande['date_debut'])){
							if($infos["rejet"]){
								 $compte_2='771000';
							}else{
								$compte_2='706300';
							}
							$compte_3='445715';
							$type="mad";
						}else{
							if($infos["rejet"]){
							 	$compte_2='771000';
							}else{
								$compte_2='706400';
							}
							$compte_3='445710';
							$type="divers";
						}
					}

					//insertion des donnÃ©es
					for ($i = 1; $i <= 4; $i++) {
						$row_data=array();
						if($refinanceur['refinanceur']=='CLEOFI'){
							if($i==1){
								$row_data["A"]='G';
								$row_data["B"]=" ".$date;
								$row_data["C"]='VEN';
								$row_data["D"]="411200";
								$row_data["E"]="CCLEOD";

								if($item['facture.prix']<0){
									$row_data["F"]='C';
								}else{
									$row_data["F"]='D';
								}
								if($infos["rejet"]){
								 	$row_data["G"]=round(abs($item['facture.prix']*$item['facture.tva']),2);
								}else{
									if(date("y",strtotime($item['facture.date_periode_debut'])) >= 14 && $devis[0]["tva"]==1.196){$row_data["G"]=round(abs($item['facture.prix']*1.2),2);
									}else{ $row_data["G"]=round(abs($item['facture.prix']*$devis[0]["tva"]),2);	}
								}
								$row_data["H"]=$libelle;
								$row_data["I"]=$reference;
							}elseif($i==2){
								$row_data["A"]='G';
								$row_data["B"]=" ".$date;
								$row_data["C"]='VEN';
								$row_data["D"]="706900";

								if($item['facture.prix']<0){
									$row_data["F"]='D';
								}else{
									$row_data["F"]='C';
								}
								$row_data["G"]=abs($item['facture.prix']);
								$row_data["H"]=$libelle;
								$row_data["I"]=$reference;
							}elseif($i==3){
								$row_data["A"]='A1';
								$row_data["B"]=" ".$date;
								$row_data["C"]='VEN';
								$row_data["D"]="706900";



								if($item['facture.prix']<0){
									$row_data["F"]='D';
								}else{
									$row_data["F"]='C';
								}
								$row_data["G"]=abs($item['facture.prix']);
								$row_data["H"]=$libelle;
								$row_data["I"]=$reference;
								if($affaire["nature"]=="avenant"){
									$row_data["J"]=" 20".substr($affaire["ref"],0,7).$societe["code_client"]."AV";
								}else{
									$row_data["J"]=" 20".substr($affaire["ref"],0,7).$societe["code_client"]."00";
								}
							}elseif($i==4){
								if($item["facture.tva"] != 1){
									$row_data["A"]='G';
									$row_data["B"]=" ".$date;
									$row_data["C"]='VEN';
									$row_data["D"]='445712';
									$row_data["E"]='';
									$row_data["F"]='C';
									$row_data["G"]=abs(($item['facture.prix']*$item['facture.tva'])-$item['facture.prix']);
									$row_data["H"]=$libelle;
									$row_data["I"]=$reference;
								}
							}
						}

						if($row_data){
							if($infos["rejet"]){
								if($row_data["G"] != 0){
									$row_auto++;
									foreach($row_data as $col=>$valeur){
										$sheets['auto']->write($col.$row_auto, $valeur);
									}
								}
							}else{
								$row_auto++;
								foreach($row_data as $col=>$valeur){
									$sheets['auto']->write($col.$row_auto, $valeur);
								}
							}
						}
					}

				}
			}
		}
	 }

	/** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	 public function export_autoportes($infos){
         $this->q->reset();

         $this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

         if($infos['onglet'] === "gsa_facture_facture"){
         	throw new errorATF("Il faut générer les fichier Excell à partir d'un filtre personnalisé");
         }else{
         	$this->q->addAllFields($this->table)
         		 //->where("facture.id_commande","commande.id_commande")
         		 //->addOrder("commande.date_debut","asc")
         		 ->addGroup("facture.id_affaire")
         		 ->setLimit(-1)
	         		 ->unsetCount();
	         $donnees = $this->sa();
			 if($infos["refi"]){
			 	$this->export_xls_autoportes($donnees,true);
			 }else{
			 	$this->export_xls_autoportes($donnees);
			 }
         }
     }

	/** Surcharge pour avoir un export identique Ã  celui de Nebula
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient tous les enregistrements
     */
     public function export_xls_autoportes(&$infos,$refi=FALSE){
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle('Autoporté');
		$sheets=array("auto"=>$worksheet_auto);
		$this->initStyle();
		//mise en place des titres
		$this->ajoutTitreAutoporte($sheets);
		//ajout des donnÃ©es
		if($infos){
			if($refi){
				$this->ajoutDonneesAutoportes($sheets,$infos,true);
			}else{
				$this->ajoutDonneesAutoportes($sheets,$infos);
			}

		}

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		if($refi){
			header('Content-Disposition:inline;filename=export_autoportes_avec_tout.xls');
		}else{
			header('Content-Disposition:inline;filename=export_autoportes.xls');
		}

		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
	}

	/** Mise en place des titres
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
     public function ajoutTitreAutoporte(&$sheets){
        $row_data = array(
        	 "A"=>array('RESEAU',20)
        	,"B"=>array('X',5)
			,"C"=>array('ENTITE',30)
			,"D"=>array('AFFAIRE',20)
			,"E"=>array('REFINANCEUR',30)
			,"F"=>array('DATE DEBUT',10)
			,"G"=>array('PERIODE',10)
			,"H"=>array('JOUR',7)
			,"I"=>array('DUREE',8)
			,"J"=>array('LOYER HT',20)
			,"K"=>array('TOTAL TTC CONTRAT',20)
			,"L"=>array('ACHAT HT',20)
			,"M"=>array('ACHAT TTC',20)
		);

		//A =65 Z=90
		 $lettre2 = 77;
		 $lettre1 = 64;
		 for($an=2015; $an<=2030; $an++){
		 	for($mois=1;$mois<=12; $mois++){
		 		if($mois <10){ $mois = "0".$mois;}
		 		$date = $an."-".$mois."-"."01";
				$stamp = strtotime($date);
				$date = date("M-y", $stamp);

				//DEPART
				if(($lettre1 == 64) && ($lettre2 <90)){
					$lettre2++;
					$char = chr($lettre2);
				}
				else{
					if($lettre2 == 90){
						$lettre1++;
						$lettre2 = 65;
						$char = chr($lettre1).chr($lettre2);
					}
					else{
						$lettre2++;
						$char = chr($lettre1).chr($lettre2);
					}
				}
			    $row_data[$char] = array($date,10);
		 	}
		 }

         foreach($sheets as $nom=>$onglet){
             foreach($row_data as $col=>$titre){
				  $sheets[$nom]->write($col.'1',$titre[0],$this->getStyle("titre1"));
				  $sheets[$nom]->sheet->getColumnDimension($col)->setWidth($titre[1]);
             }
         }

     }

	 /** Mise en place du contenu
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $sheets : contient les 5 onglets
     * @param array $infos : contient tous les enregistrements
	 * @param boolean $refinance TRUE pour sortir toutes les affaires
     */
     public function ajoutDonneesAutoportes(&$sheets,$infos, $refinance=FALSE){

        $row_auto=1;
		$increment=0;
		$InOneMonth = date('Y-m-01',strtotime("+1 month"));
		$InOneMonth = explode("-", $InOneMonth);
		$InOneMonth =  $InOneMonth["0"].$InOneMonth["1"].$InOneMonth["2"];
		foreach ($infos as $key => $item) {
			$afficher = FALSE;
			$increment++;
			if($item){

				ATF::demande_refi()->q->reset()->where("demande_refi.id_affaire",$item["facture.id_affaire_fk"])
											  ->where("demande_refi.etat", "valide")
											  ->setLimit(1)
											  ->addOrder("demande_refi.date","desc");
				$refi = ATF::demande_refi()->select_row();
				//On affiche toutes les affaires meme celles refinancé par un autre que Cleodis
				if($refinance){
					$afficher = TRUE;
				}else{
					//Refinancements par CLEODIS OU CLEOFI ou sans refi
					if(($refi["id_refinanceur"] ==4) || ($refi["id_refinanceur"] ==14) || (!$refi)){
						$afficher = TRUE;
					}
					$com = ATF::commande()->select($item["commande.etat_fk"]);
					$finContratDansMois = explode("-", $com["date_evolution"]);

					$finContratDansMois =  $finContratDansMois["0"].$finContratDansMois["1"].$finContratDansMois["2"];

					//Si le contrat prend fin dans le mois, il ne faut pas l'afficher
					if($finContratDansMois && $finContratDansMois < $InOneMonth){	$afficher = FALSE; }
				}

				if($afficher){
					$devis=ATF::devis()->select_special("id_affaire",$item['facture.id_affaire_fk']);
					$societe = ATF::societe()->select($item['facture.id_societe_fk']);
					ATF::loyer()->q->reset()->where("loyer.id_affaire",$item["facture.id_affaire_fk"]);
					$loyers = ATF::loyer()->select_all();


					foreach($loyers as $k=>$loyer){

						//Ne pas afficher les contrats ou il n'y a qu'un seul loyer
						ATF::facturation()->q->reset()->where("facturation.id_affaire", $item["facture.id_affaire_fk"])
													  ->where("facturation.type", "contrat")
													  ->addOrder("date_periode_debut", "asc");
						$echTest = ATF::facturation()->select_all();


						if($loyer["duree"] && count($echTest)>1){
							$duree = $loyer["duree"];
							ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $item['facture.id_affaire_fk']);
							$sommeBDC = ATF::bon_de_commande()->sa();

							$Achat["HT"] = 0;
							$Achat["TTC"] = 0;
							foreach($sommeBDC as $k=>$v){
								$Achat["HT"] = $Achat["HT"] + $v["prix"];
								$Achat["TTC"] = $Achat["TTC"] + ($v["prix"] * $v["tva"]) ;
							}



							ATF::commande()->q->reset()->where("commande.id_affaire", $item["facture.id_affaire_fk"]);
							$commande = ATF::commande()->select_row();
							ATF::facturation()->q->reset()->where("facturation.id_affaire", $item["facture.id_affaire_fk"])
														  ->where("facturation.date_periode_debut", $commande["commande.date_debut"], "AND", false, ">=")
														  ->addOrder("date_periode_debut", "asc");
							$echeancierTmp = ATF::facturation()->select_all();

							//On reinitialise l'array echeancier pour ne pas avoir des données d'avant
							$echeancier = array();

							//HotFix pour BTWIN
							// On a toujours 2 lignes d'echeance pour la 1er periode
							// A fixer plus tard
							$last_date = $echeancierTmp[0]["date_periode_debut"];
							$echeancier[] = $echeancierTmp[0];
							foreach ($echeancierTmp as $key => $value) {
								if($value["date_periode_debut"] != $last_date){
									$echeancier[] = $value;
									$last_date = $value["date_periode_debut"];
								}
							}

							$fact = 0;
							$row_data=array();

							$row_data["A"]=array($item["societe.code_client"],"border_cel_left");
							$row_data["B"]=array("","border_cel_left");
							$row_data["C"]=array($item["facture.id_societe"],"border_cel_left");
							$row_data["D"]=array($item["facture.id_affaire"],"border_cel_right");

							$res = $this->select($item["facture.id_facture_fk"] );


							if(is_array($refi)){
								$row_data["E"] = array(ATF::refinanceur()->select($refi["id_refinanceur"], "refinanceur") , "border_cel");
							}else{ $row_data["E"] = array("","border_cel");	}

							$row_data["F"]=array($echeancier[$fact]["date_periode_debut"], "border_cel");
							$frequence = "";
							if($loyer["frequence_loyer"]){
								switch ($loyer["frequence_loyer"]) {
									case 'mois':$frequence = "M";  break;
									case 'trimestre':$frequence = "T";	break;
									case 'semestre':$frequence = "S";	break;
									case 'an':$frequence = "A";	break;
								}
							}
							$row_data["G"]=array($frequence,"border_cel");

							//$jour = explode("-",$item['facture.date_periode_debut']);
							$jour = explode("-", $echeancier[$fact]["date_periode_debut"]);
							$row_data["H"]=array($jour[2],"border_cel_right");
							$row_data["I"]=array($loyer["duree"],"border_cel_right");
							$row_data["J"]=array(($loyer["loyer"]+$loyer["assurance"]+$loyer["frais_de_gestion"]),"border_cel_right");
							$row_data["K"]=array($loyer["duree"]*($loyer["loyer"]+$loyer["assurance"]+$loyer["frais_de_gestion"])*1.2,"border_cel_right");
							$row_data["L"]=array(abs($Achat["HT"]),"border_cel_right");
							$row_data["M"]=array(round(abs($Achat["TTC"]),2),"border_cel_right");


							//A =65 Z=90
							$lettre2 = 77;
							$lettre1 = 64;
							for($an=2015; $an<=2030; $an++){
							 	for($mois=1;$mois<=12; $mois++){
							 		if($mois <10){ $mois = "0".$mois;}
							 		$date = $an."-".$mois."-"."01";

									//DEPART
									if(($lettre1 == 64) && ($lettre2 <90)){
										$lettre2++;
										$char = chr($lettre2);
									}else{
										if($lettre2 == 90){
											$lettre1++;
											$lettre2 = 65;
											$char = chr($lettre1).chr($lettre2);
										}else{
											$lettre2++;
											$char = chr($lettre1).chr($lettre2);
										}
									}
									$date = date("Y-m", strtotime($date));

									//$date = $date."-".$jour[2];
									$dateCol = new DateTime($date."-".$jour[2]);
									$dateColFin = new DateTime($date."-31");
									$dateDeb = new DateTime($echeancier[$fact]["date_periode_debut"]);
									$dateFin = new DateTime($echeancier[$fact]["date_periode_fin"]);



									if(($dateCol->getTimestamp()  <= $dateDeb->getTimestamp() && $dateColFin->getTimestamp() >= $dateDeb->getTimestamp())){


										if((($echeancier[$fact]["montant"]+$echeancier[$fact]["assurance"]+$echeancier[$fact]["frais_de_gestion"]) == ($loyer["loyer"]+$loyer["assurance"]+$loyer["frais_de_gestion"])) || (($echeancier[$fact]["montant"]) == ($loyer["loyer"]+$loyer["assurance"]+$loyer["frais_de_gestion"]))){

											$row_data[$char] =  array(($echeancier[$fact]["montant"]+$echeancier[$fact]["assurance"]+$echeancier[$fact]["frais_de_gestion"]),"cel_right");
										}
							    		$fact++;
									}
							 	}
							}
							//log::logger($row_data , "mfleurquin");
							if($row_data){
								$row_auto++;
								foreach($row_data as $col=>$valeur){
									$sheets['auto']->write($col.$row_auto, $valeur[0], $this->getStyle($valeur[1]));
								}
							}
						}
					}
				}
			}
		}
	}

	public function initStyle(){

		$style_titre1 = new excel_style();
		$style_titre1->setWrap()->alignement('center')->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1",$style_titre1->getStyle());
		/*-------------------------------------------*/
		$style_titre1_right = new excel_style();
		$style_titre1_right->setWrap()->alignement("center","right")->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1_right",$style_titre1_right->getStyle());
		/*-------------------------------------------*/
		$style_titre1_left = new excel_style();
		$style_titre1_left->setWrap()->alignement("center", "left")->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1_left",$style_titre1_left->getStyle());
		/*-------------------------------------------*/
		$style_titre2 = new excel_style();
		$style_titre2->setWrap()->alignement('center')->setSize(11)->setBorder("thin");
		$this->setStyle("titre2",$style_titre2->getStyle());
		/*-------------------------------------------*/
		$style_titre2_right = new excel_style();
		$style_titre2_right->setWrap()->alignement("center","right")->setSize(11)->setBorder("thin");
		$this->setStyle("titre2_right",$style_titre2_right->getStyle());
		/*-------------------------------------------*/
		$style_centre = new excel_style();
		$style_centre->alignement();
		$this->setStyle("centre",$style_centre->getStyle());
		/*-------------------------------------------*/
		$style_cel_c = new excel_style();
		$style_cel_c->setWrap()->alignement('center')->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel",$style_cel_c->getStyle());
		/*-------------------------------------------*/
		$style_border_cel_right = new excel_style();
		$style_border_cel_right->setWrap()->alignement("center","right")->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel_right",$style_border_cel_right->getStyle());
		/*-------------------------------------------*/
		$style_border_cel_left = new excel_style();
		$style_border_cel_left->setWrap()->alignement("center","left")->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel_left",$style_border_cel_left->getStyle());
		/*-------------------------------------------*/
		$style_cel_right = new excel_style();
		$style_cel_right->setWrap()->alignement("center","right")->setSize(11);
		$this->setStyle("cel_right",$style_cel_right->getStyle());
	}

	public function setStyle($nom,$objet){
		$this->style[$nom]=$objet;
	}

	public function getStyle($nom){
		return $this->style[$nom];
	}


    /**
    * Recupère toutes les factures d'une même affaire qui n'ont aps encore de relance pour la selection dans le multiselect des relances
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param array $infos
    * @return array
    */
    public function getAllForRelance($infos) {
        $facture = self::select($infos['id_facture']);

        $this->q->reset()->where('id_affaire',$facture["id_affaire"])->where('etat','impayee')->where("id_facture",$facture['id_facture'],"OR",false,"!=");
        $result = $this->sa();

        foreach ($result as $k=>$i) {
            if (!ATF::relance()->getIdRelance($i['id_facture'],'premiere')) {
                $return[] = array("id"=>$i['id_facture'],"reference"=>$i['ref']);
            }
        }

        return $return;
    }

	public function libreToNormale($infos){
		ATF::facturation()->q->reset()->where("id_affaire" , $this->select($infos["id_facture"] , "id_affaire") , "AND")
									  ->where("date_periode_debut" , $this->select($infos["id_facture"] , "date_periode_debut"), "AND")
									  ->where("date_periode_fin" , $this->select($infos["id_facture"] , "date_periode_fin"), "AND");
		$ligne_echeancier = ATF::facturation()->select_row();


		if($this->select($infos["id_facture"] , "type_libre") == "normale"){
			//Il y a deja une ligne echeancier de créée
			if($ligne_echeancier){
				//Si il n'y a pas de facture
				if($ligne_echeancier["id_facture"] == NULL){
					ATF::db($this->db)->begin_transaction();
						ATF::facturation()->u(array("id_facturation" => $ligne_echeancier["id_facturation"] , "id_facture" => $this->decryptId($infos["id_facture"]), "montant" => $this->select($infos["id_facture"] , "prix")));
						$this->u(array("id_facture" => $this->decryptId($infos["id_facture"]) , "type_facture" => "facture"));
						//Ajout du suivis
						$suivis = array( "id_user" => ATF::$usr->get('id_user'),
										 "id_societe" => $this->select($infos["id_facture"] , "id_societe"),
										 "type" => "note",
										 "date" => date("Y-m-d H:i:s"),
										 "texte" => "Passage de la facture libre ".$this->select($infos["id_facture"] , "ref")." en facture normale et ajout à l'echancier",
										 "id_affaire" => $this->select($infos["id_facture"] , "id_affaire"),
										 "type_suivi" => "Contrat",
										 "no_redirect" => true
								  );

						ATF::suivi()->insert($suivis);
					ATF::db($this->db)->commit_transaction();
					ATF::$msg->addNotice("Passage de la facture libre en normale et ajout de la facture à l'echeancier reussie");
				}else{
					throw new errorATF("Il y a déja une facture pour la période du ".$this->select($infos["id_facture"] , "date_periode_debut")." au ".$this->select($infos["id_facture"] , "date_periode_fin"));
				}
			}else{
				ATF::db($this->db)->begin_transaction();
					ATF::loyer()->q->reset()->where("id_affaire",$this->select($infos["id_facture"]));
					$loyer_parent = ATF::loyer()->sa();
					$this->u(array("id_facture" => $this->decryptId($infos["id_facture"]) , "type_facture" => "facture"));
					ATF::facturation()->i(array(
									"id_societe"=>$this->select($infos["id_facture"] , "id_societe"),
									"id_affaire"=>$this->select($infos["id_facture"] , "id_affaire"),
									"id_facture" => $this->decryptId($infos["id_facture"]),
									"montant"=>$this->select($infos["id_facture"] , "prix"),
									"assurance"=>$loyer_parent[0]['assurance'],
									"frais_de_gestion"=>$loyer_parent[0]['frais_de_gestion'],
									"date_periode_fin"=>$this->select($infos["id_facture"] , "date_periode_fin"),
									"date_periode_debut"=>$this->select($infos["id_facture"] , "date_periode_debut"),
									"type"=>"contrat"));
					//Ajout du suivis
					$suivis = array( "id_user" => ATF::$usr->get('id_user'),
									 "id_societe" => $this->select($infos["id_facture"] , "id_societe"),
									 "type" => "note",
									 "date" => date("Y-m-d H:i:s"),
									 "texte" => "Passage de la facture libre ".$this->select($infos["id_facture"] , "ref")." en facture normale et création de la ligne d'echeancier",
									 "id_affaire" => $this->select($infos["id_facture"] , "id_affaire"),
									 "type_suivi" => "Contrat",
									 "no_redirect" => true
							  );

					ATF::suivi()->insert($suivis);
				ATF::db($this->db)->commit_transaction();
				ATF::$msg->addNotice("Passage de la facture libre en normale création de la ligne d'echeancier et ajout de la facture à l'echeancier reussie");
				/*throw new errorATF("Impossible car il n'y a pas de ligne d'echeancier pour la periode du ".$this->select($infos["id_facture"] , "date_periode_debut")." au ".$this->select($infos["id_facture"] , "date_periode_fin"));*/
			}
		}else{
			throw new errorATF("Il n'est pas possible de passer une facture libre ".$this->select($infos["id_facture"] , "type_libre")." en facture normale");
		}
		return true;
	}

	/** Export CEGID
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	public function export_cegid($infos){
		if(!$infos["tu"]){ $this->q->reset(); }

        $this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

        $this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
        $infos = $this->sa();

		$this->export_xls_cegid($infos);
    }


    /** Surcharge pour avoir un export
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient tous les enregistrements
     */
    public function export_xls_cegid(&$infos,$refi=FALSE){
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle('IMPORT CEGID');
		$sheets=array("auto"=>$worksheet_auto);
		$this->initStyle();

		//mise en place des titres
		$row_data = array(
        	 "A"=>array('Compte',60)
        	,"B"=>array('Date entrée',60)
			,"C"=>array('Date de mise en service',60)
			,"D"=>array('Date début amortissement comptable',60)
			,"E"=>array('Date début amortissement fiscal',60)
			,"F"=>array('Réference',60)
			,"G"=>array('Libellé',60)
			,"H"=>array('Prix unitaire',60)
			,"I"=>array('Montant HT',60)
			,"J"=>array('Quantité',60)
			,"K"=>array('Montant TVA',60)
			,"L"=>array('Taux TVA',60)
			,"M"=>array('Prorata TVA',60)
			,"N"=>array('Montant TTC',60)
			,"O"=>array('Type sortie',60)
			,"P"=>array('Date sortie',60)
			,"Q"=>array('Base comptable',60)
			,"R"=>array('Méthode comptable',60)
			,"S"=>array('Durée comptable',60)
			,"T"=>array('Base fiscale',60)
			,"U"=>array('Méthode fiscale',60)
			,"V"=>array('Durée fiscale',60)
			,"W"=>array('Nature bien',60)
			,"X"=>array('Type entrée',60)
			,"Y"=>array('Niveau réalité',60)
			,"Z"=>array('Total cumulantérieur comptable',60)
			,"AA"=>array('Total cumulantérieur fiscal',60)
			,"AB"=>array('Critère 1',60)
			,"AC"=>array('Réference 2',60)
			,"AD"=>array('Compte fournisseur',60)
		);



        foreach($sheets as $nom=>$onglet){
            foreach($row_data as $col=>$titre){
				$sheets[$nom]->write($col.'1',$titre[0],$this->getStyle("titre1"));
				$sheets[$nom]->sheet->getColumnDimension($col)->setWidth($titre[1]);
            }
        }


		//ajout des donnÃ©es
		if($infos){
			$row_auto=1;

			foreach ($infos as $key => $value) {
				$facture  = ATF::facture()->select($value["facture.id_facture_fk"]);
				$affaire  = ATF::affaire()->select($value["facture.id_affaire_fk"]);
				$commande = ATF::commande()->select($value["facture.id_commande_fk"]);
				$societe  = ATF::societe()->select($value["facture.id_societe_fk"]);

				ATF::facture_ligne()->q->reset()->where("facture_ligne.id_facture", $value["facture.id_facture_fk"])->setLimit(1);
				$fournisseur = ATF::facture_ligne()->select_row();
				$fournisseur = ATF::societe()->select($fournisseur["id_fournisseur"], "societe");

				ATF::facture()->q->reset()->where("facture.id_societe", $societe["id_societe"])
											  ->where("facture.id_affaire", $value["facture.id_affaire_fk"])
											  ->addConditionNotNull("facture.date_periode_debut")
											  ->addOrder("facture.date_periode_debut", "asc")
											  ->setLimit(1);

				$first_facture = ATF::facture()->select_row();

				$date_deb = "";
				if($first_facture){	$date_deb = date("d/m/Y", strtotime(ATF::facture()->select($first_facture["facture.id_facture"] , "date_periode_debut"))); }

				$date_mise_service = "";
				if($commande["date_debut"]){ $date_mise_service = date("d/m/Y", strtotime($commande["date_debut"]));}



				$duree = "";
				if($commande["date_debut"] && $commande["date_evolution"]){
					$datetime1 = new DateTime($commande["date_debut"]);
					$datetime2 = new DateTime($commande["date_evolution"]);
					$duree = $datetime1->diff($datetime2);
					$duree = number_format( (intval($duree->format('%a')) / 365) , 3);
				}

	        	$row_data = array(
					        	 "A"=>array('218310')
					        	,"B"=>array($date_deb)
								,"C"=>array($date_mise_service)
								,"D"=>array($date_deb)
								,"E"=>array($date_deb)
								,"F"=>array($facture["ref_cegid"])
								,"G"=>array($societe["societe"]." ".$affaire["ref"]."-".$societe["code_client"])
								,"H"=>array('')
								,"I"=>array($facture["prix"])
								,"J"=>array('1')
								,"K"=>array(($facture["prix"]*$facture["tva"])-$facture["prix"])
								,"L"=>array(abs(($facture['tva']-1)*100),2,'.',' ')
								,"M"=>array('100')
								,"N"=>array($facture["prix"]*$facture["tva"])
								,"O"=>array('00')
								,"P"=>array('30/12/2099')
								,"Q"=>array($facture["prix"])
								,"R"=>array('01')
								,"S"=>array($duree)
								,"T"=>array($facture["prix"])
								,"U"=>array('01')
								,"V"=>array($duree)
								,"W"=>array('01')
								,"X"=>array('01')
								,"Y"=>array('09')
								,"Z"=>array('09')
								,"AA"=>array('0')
								,"AB"=>array('20'.$affaire["ref"].$societe["code_client"])
								,"AC"=>array($affaire["ref"])
								,"AD"=>array($fournisseur)
							);



				$row_auto++;
				foreach($row_data as $col=>$valeur){
					$sheets['auto']->write($col.$row_auto, $valeur[0]);
				}
			}
	    }

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_CEGID.xls');


		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
	}



	public function import_facture_libre(&$infos,&$s,$files=NULL) {


		$infos['display'] = true;
		$path = $files['file']['tmp_name'];

		$facture_insert = 0;
		$erreurs = array();
		$warnings["150 - Enregistrement(s) déjà existant(s)"].", ";

		$f = fopen($path,"r");

	    // Vérification des colonnes
	    $cols = fgetcsv($f, 0, ";");
	    fclose($f);


		ATF::db($this->db)->begin_transaction();

		$f = fopen($path,"r");
		$data = fgetcsv($f, 0, ";");
		fclose($f);
		$entetes = $cols;
		$entetes_necessaire = array(
			"commentaire" => false,
			"nature" => false,
			"redevance" => false,
			"ref_externe" => false,
			"type_libre" => false,
			"ref_affaire" => false,
			"mode de paiement" => false,
			"periode_debut" => false,
			"date" => false,
			"periode_fin" => false,
			"total_ht" => false,
			"date_previsionnelle"=>false
		);

		foreach ($entetes as $key => $value) $entetes_necessaire[$value] = true;

		$nb_entete_manquant = 0;
		foreach ($entetes_necessaire as $key => $value) {
			if($value ==false){
				$erreurs["Entete manquante (".$key.")"] .= $lineCompteur.", ";
				$nb_entete_manquant ++;
			}
		}



		$handle = fopen($path,"r");

		if($nb_entete_manquant == 0){



			$row = 0;
			$lineCompteur = 0;
			while (($data = fgetcsv($handle, 10000, ";")) !== FALSE) {
				$lineCompteur++;
				$row++;

				if($row == 1) continue;

				if($lineCompteur>11 && !$data[2] ) continue;

				$data = array_map("utf8_encode",$data);

				try {


					$col_ref_affaire = array_keys($entetes , "ref_affaire");

					ATF::affaire()->q->reset()->addField("affaire.id_societe")->where("affaire.ref", $data[$col_ref_affaire[0]]);
					$affaire = ATF::affaire()->select_row();


					if($affaire){

						ATF::commande()->q->reset()->where("commande.id_affaire", $affaire["affaire.id_affaire"]);
						$commande = ATF::commande()->select_row();

						if($commande){

							$facture = array(
								"type_facture" => "libre",
								"id_societe" => $affaire["affaire.id_societe_fk"],
								"id_affaire" => $affaire["affaire.id_affaire"],
								"id_commande" => $commande["commande.id_commande"],
							);

							foreach ($data as $key => $value) {
								switch ($entetes[$key]) {
									case 'commentaire' :
									case 'nature' :
									case 'redevance' :
									case 'ref_externe' :
										$facture[$entetes[$key]] = $value;
									break;

									case 'type_libre' :
										$facture[$entetes[$key]] = $value;
									break;

									case 'ref_affaire' :
									break;

									case 'mode de paiement' :
										$facture["mode_paiement"] = $value;
									break;

									case 'periode_debut' :
									case 'date' :
									case 'periode_fin' :
									case 'date_previsionnelle':

										if(strpos($value , "/")){
											$date = explode("/" , $value);
											$date = $date[2]."-".$date[1]."-".$date[0];
										}else{
											$date = $value;
										}
										if($date){
											if($entetes[$key] == "date") $facture["date"] = date("Y-m-d", strtotime($date));
											if($entetes[$key] == "periode_debut") $facture["date_periode_debut_libre"] = date("Y-m-d", strtotime($date));
											if($entetes[$key] == "periode_fin") $facture["date_periode_fin_libre"] = date("Y-m-d", strtotime($date));
											if($entetes[$key] == "date_previsionnelle") $facture["date_previsionnelle"] = date("Y-m-d", strtotime($date));
										}else{
											if($entetes[$key] == "date") $facture["date"] = NULL;
											if($entetes[$key] == "periode_debut") $facture["date_periode_debut_libre"] = NULL;
											if($entetes[$key] == "periode_fin") $facture["date_periode_fin_libre"] = NULL;
											if($entetes[$key] == "date_previsionnelle") $facture["date_previsionnelle"] = NULL;
										}
									break;


									case 'total_ht' :
										$facture["prix_libre"] = $value;
									break;

									default:
									break;
								}
							}

							$fields=[
								  "produit"
								, "quantite"
								, "ref"
								, "id_fournisseur"
								, "prix_achat"
								, "code"
								, "id_produit"
								, "serial"
							];

							ATF::commande_ligne()->q->reset()
										->addField(util::keysOrValues($fields))
										->where("id_commande", $commande["commande.id_commande"])
										->where("id_affaire_provenance",null,null,false,"IS NULL")
										->where("visible_pdf","oui");

							$return = array();
							if ($ligneVisible = ATF::commande_ligne()->select_all() ) {

								foreach ($ligneVisible as $kRow => $row) {
									foreach ($row as $kCol => $value) {
										if($kCol != "commande_ligne.id_commande_ligne"){
											if(strpos($kCol, "id_") !== false){
												$return[$kRow]["facture_ligne.".$kCol."_fk"]=$value;
												$return[$kRow]["facture_ligne.".$kCol]=$value;
											}else{
												$return[$kRow]["facture_ligne.".$kCol]=$value;
											}
										}
									}
									$return[$kRow]["facture_ligne.afficher"]="oui";
								}
								$ligneVisible = $return;
							}

							ATF::commande_ligne()->q->reset()
										->addField(util::keysOrValues($fields))
										->where("id_commande", $commande["commande.id_commande"])
										->where("id_affaire_provenance",null,null,false,"IS NOT NULL")->setView(["order"=>$fields]);
							$return = array();
							if ($ligneRepris = ATF::commande_ligne()->select_all() ) {
								foreach ($ligneRepris as $kRow => $row) {
									foreach ($row as $kCol => $value) {
										if($kCol != "commande_ligne.id_commande_ligne"){
											if(strpos($kCol, "id_") !== false){
												$return[$kRow]["facture_ligne.".$kCol."_fk"]=$value;
												$return[$kRow]["facture_ligne.".$kCol]=$value;
											}else{
												$return[$kRow]["facture_ligne.".$kCol]=$value;
											}
										}
									}
									$return[$kRow]["facture_ligne.afficher"]="oui";
								}
								$ligneRepris = $return;
							}

							ATF::commande_ligne()->q->reset()
										->addField(util::keysOrValues($fields))
										->where("id_commande", $commande["commande.id_commande"])
										->where("id_affaire_provenance",null,null,false,"IS NULL")
										->where("visible_pdf","non")->setView(["order"=>$fields]);
							$return = array();
							if ($ligneNonVisible = ATF::commande_ligne()->select_all() ) {
								foreach ($ligneNonVisible as $kRow => $row) {
									foreach ($row as $kCol => $value) {
										if($kCol != "commande_ligne.id_commande_ligne"){
											if(strpos($kCol, "id_")  !== false ){
												$return[$kRow]["facture_ligne.".$kCol."_fk"]=$value;
												$return[$kRow]["facture_ligne.".$kCol]=$value;
											}else{
												$return[$kRow]["facture_ligne.".$kCol]=$value;
											}
										}
									}
									$return[$kRow]["facture_ligne.afficher"]="oui";
								}
								$ligneNonVisible = $return;
							}

							$this->insert(array(
								"facture"=> $facture,
								"values_facture" =>
									array(
										"produits_repris" => json_encode($ligneRepris) ,
										"produits" => json_encode($ligneVisible) ,
										"produits_non_visible" => json_encode($ligneNonVisible) ,
									)
								)
							);
							$facture_insert ++;
						}else{
							$erreurs["Contrat non trouvé (".$data[$col_ref_affaire[0]].")"] .= $lineCompteur.", ";
						}
					}else{
						$erreurs["Affaire non trouvée (".$data[$col_ref_affaire[0]].")"] .= $lineCompteur.", ";
					}
				} catch (errorATF $e) {

					$msg = $e->getMessage();

					if (preg_match("/generic message : /",$msg)) {
					  $tmp = json_decode(str_replace("generic message : ","",$msg),true);
					  $msg = $tmp['text'];
					}

			        if ($e->getErrno()==1062) {
			          if ($infos['ignore']) {
			              $warnings[$e->getErrno()." - Enregistrement(s) déjà existant(s)"] .= $lineCompteur.", ";
			          } else {
			              $erreurs[$e->getErrno()." - Enregistrement(s) déjà existant(s)"] .= $lineCompteur.", ";
			          }
			        } else {
			            $erreurs[$e->getErrno()." - ".$msg] .= $lineCompteur.", ";
			        }
				}



		    }
		}


    	fclose($handle);


		if (!empty($erreurs)) {
	      $return['errors'] = $erreurs;
	      $return['success'] = false;
	      ATF::db($this->db)->rollback_transaction();
	    } else {
	      $return['warnings'] = $warnings;

	      $return['success'] = true;
	      $return["factureInserted"] = $facture_insert;
	      ATF::db($this->db)->commit_transaction();
	    }


		return json_encode($return);
	}

	public function import_facture_controle_statut(&$infos,&$s,$files=NULL) {
		$logFile = "controleFactureStatutCleodis";
		log::logger("=================== Début de fonction import_facture_controle_statut", $logFile);
		$infos['display'] = true;
		$path = $files['fileStatut']['tmp_name'];
		log::logger("Début de fonction import_facture_controle_statut", $logFile);

		$erreurs = array();
		try {
			$f = fopen($path,"r");

		    // Vérification des colonnes
		    $entetes = fgetcsv($f, 0, ";");
			$expectedEntetes = array( "ref_facture", "statut");
		    if (count($entetes) != count($expectedEntetes)) {
		    	throw new errorATF("Le nombre de colonne est incorrect ".count($entetes)." au lieu de 2");
		    }

			foreach ($entetes as $col => $name) {
				if($name != $expectedEntetes[$col]){
					$erreurs[] = "Erreur entête colonne ".$col." : Valeur attendu : ".$expectedEntetes[$col]." / Valeur actuelle : ".$name;
					$nb_entete_manquant++;
				}
			}

			ATF::db($this->db)->begin_transaction();


			if (count($erreurs)) {
				throw new errorATF(implode("<br>", $erreurs));
			}
			$this->q->reset();
			// $allFactures = $this->sa(); // a remplacer par un IN basé sur le fichier

			$facturesNotFound = $facturesEtatDifferend = $impayeesBDDNotInCSV = [];
			$refInFile = '';
			$nbFactureCsv = 0;
			while (($data = fgetcsv($f, 0, ";")) !== FALSE) {

				$nbFactureCsv++;

				$refInFile .= '"'.$data[0].'",';
				// $indexFound = array_search($data[1], array_column($allFactures, 'ref'));
				ATF::facture()->q->reset()->addAllFields("facture")->where("facture.ref", $data[0])->setLimit(1)->setStrict();
				$facture = [];
				$facture = ATF::facture()->select_row();
				log::logger("Recherche facture ".$data[0]." - Statut : ".$data[1]." - résultat ", $logFile);
				// log::logger($facture, $logFile);
				// if ($indexFound !== false && !empty($allFactures[$indexFound])) {
				if ($facture['facture.id_facture']) {
					log::logger("Found", $logFile);
					if ($facture['facture.etat'] != $data[1]) {
						$facturesEtatDifferend[] = $data;
						log::logger("Etat différent ! BDD: ".$facture['facture.etat']." / CSV: ".$data[1], $logFile);
					} else {
						log::logger("Etat IDEM - RAS", $logFile);

					}
				} else {
					$facturesNotFound[] = $data;
					log::logger("Not found ", $logFile);
				}
			}
	    	fclose($handle);


			$this->q->reset()->addField("facture.ref", "ref")
							 ->addField("facture.etat", "etat")
							->andWhere("facture.ref", substr($refInFile, 0, -1) ,"subquery", "NOT IN",false, true)
							 ->where("facture.etat", "impayee");
			$resImpayeesBDDNotInCSV = $this->select_all();

			foreach($resImpayeesBDDNotInCSV as $key => $value) {
				$impayeesBDDNotInCSV[] = array($value["ref"], $value["etat"]);
			}


			$return['warnings'] = $warnings;
			$return['rapport'] = "Rapport : <br><br>";
			$return['rapport'] .= "Nombre de facture dans le CSV : ".$nbFactureCsv."<br>";
			$return['rapport'] .= "Nombre de facture avec un état différent en BDD : ".count($facturesEtatDifferend)."<br>";
			$return['rapport'] .= "Nombre de facture non trouvées en BDD : ".count($facturesNotFound)."<br>";
			$return['rapport'] .= "Nombre de facture impayée en BDD non présente dans le fichier : ".count($impayeesBDDNotInCSV)."<br>";
			$return['success'] = true;
			ATF::db($this->db)->commit_transaction();


			require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
			require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
			$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());

			$workbook = new PHPExcel;

			$sheets = array("Etat différents","Non trouvées", "Impaye non présent dans fichier");

			$worksheet_auto = new PHPEXCEL_ATF($workbook,0);

	        // Premier onglet
	        $sheet = $workbook->getActiveSheet();
			$workbook->setActiveSheetIndex(0);
		    $sheet->setTitle("Fact OPTIMA avec etat diff XLS");

		    $sheet->fromArray(array("Référence facture","Etat"), NULL, 'A1');
			$sheet->fromArray($facturesEtatDifferend, NULL, 'A2');

	        // Deuxième onglet
        	$sheet = $workbook->createSheet(1);
			$workbook->setActiveSheetIndex(1);
		    $sheet->setTitle("Fact XLS absentes d OPTIMA");

		    $sheet->fromArray(array("Référence facture","Etat"), NULL, 'A1');
			$sheet->fromArray($facturesNotFound, NULL, 'A2');

			// Troisieme onglet
        	$sheet = $workbook->createSheet(2);
			$workbook->setActiveSheetIndex(2);
		    $sheet->setTitle("Fact XLS avec etat diff OPTIMA");

		    $sheet->fromArray(array("Référence facture","Etat"), NULL, 'A1');
			$sheet->fromArray($impayeesBDDNotInCSV, NULL, 'A2');

			foreach ($workbook->getWorksheetIterator() as $worksheet) {

			    $workbook->setActiveSheetIndex($workbook->getIndex($worksheet));

			    $sheet = $workbook->getActiveSheet();
			    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
			    $cellIterator->setIterateOnlyExistingCells(true);
			    /** @var PHPExcel_Cell $cell */
			    foreach ($cellIterator as $cell) {
			        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
			    }
			}

			$writer = new PHPExcel_Writer_Excel5($workbook);

			$writer->save($fname);
			PHPExcel_Calculation::getInstance()->__destruct();
			$return['fname'] = $fname;

		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
			$return['errors'] = $e->getMessage();

			$return['success'] = false;
		}



		return json_encode($return);
	}

	public function download_facture_controle_statut(&$infos,&$s,$files=NULL) {
		$infos['display'] = true;
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=rapport_controle_facture-'.date("YmdHis").'.xls');
		header("Cache-Control: private");
		$fh=fopen($infos['fname'], "rb");
		fpassthru($fh);
		// unlink($fname);
	}

	public function getAffaireMere($id_affaire){
		if(ATF::affaire()->select($f["id_affaire"], "nature") == "avenant"){
			ATF::affaire()->q->reset()->where("id_affaire", $id_affaire);
			$aff = ATF::affaire()->select_row();
			return $this->getAffaireMere($aff["id_affaire"]);
		}else{
			return $id_affaire;
		}
	}


	/**
	* Retourne le mandat SLIMPAY d'une affaire passée en parametre
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*
	*/
	public function getMandatSlimpay($id_affaire){
		if($mandatSlimpay = ATF::affaire()->select($id_affaire , "RUM")){
			return $mandatSlimpay;
		}else{
			if($id_parent = ATF::affaire()->select($id_affaire , "id_parent")){
				return $this->getMandatSlimpay($id_parent);
			}else{
				throw new errorATF("Error Processing Request", 1);

			}
		}
	}

	public function _aPrelever($infos) {
		return $this->aPrelever($infos);
	}


	/**
	* permet d'envoyer les factures par mail, pour les factures ayant une date_envoi = date en param et envoyé = non
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*
	*/
	public function sendFactureMail($date) {
		log::logger("-----------------------------" , "sendFactureMail");
		log::logger("Arrivée dans le sendFactureMail pour la date du ".$date , "sendFactureMail");
		ATF::facture()->q->reset()->where("envoye", "non", "AND")
								 ->where("date_envoi", $date, "AND");
		$factures_a_envoyer = ATF::facture()->sa();

		log::logger("On a ".count($factures_a_envoyer)." a envoyé pour cette date" , "sendFactureMail");

		foreach ($factures_a_envoyer as $key => $value) {
			try {
				$facture_info = ATF::facture()->select($value["id_facture"]);
				$societe = ATF::societe()->select($facture_info["id_societe"]);

				log::logger("-- Facture ".$facture_info['ref'] , "sendFactureMail");

				if($societe["id_contact_facturation"]){
					log::logger("---- On a un contact de facturation pour cette societe " , "sendFactureMail");
					$contact= ATF::contact()->select($societe["id_contact_facturation"]);
				}else{
					log::logger("---- On a pas de contact de facturation pour cette societe " , "sendFactureMail");
					$contact = NULL;
				}
				$ref = $facture_info['ref_externe'] ? $facture_info['ref_externe'] : $facture_info['ref'];

				if ($contact) {
					log::logger("---- On prepare le mail " , "sendFactureMail");
					$email = array(
						"email" => NULL,
						"texte" => "Bonjour ".$contact['nom']." ".$contact['prenom'].", <br />
						Nous avons le plaisir de vous envoyer votre facture n°".$ref."<br />
						Merci de votre confiance",
						"html" => true,
						"template"=> "facture"
					);


					if($contact["email"]) {
						$email['email']=$contact["email"];
					}else{
						$email['email']=$contact["email_perso"];
					}

					if ($email['email'] != NULL) {
						log::logger("---- On envoi le mail à ".$contact['email'] , "sendFactureMail");

						$suivi_message = "Envoi de la facture ".$ref.
									" au client ".$societe["societe"]." (email: ".$email["email"].") ".
									" pour l'affaire ".ATF::affaire()->select($facture_info["id_affaire"], "ref");

						ATF::affaire()->mailContact($email,$value["id_facture"],"facture", array("facture"=> "fichier_joint"));
						ATF::facture()->u(array("id_facture"=> $value["id_facture"], "envoye"=> "oui"));

						// On recupere la facturation associée à cette facture
						ATF::facturation()->q->reset()->where("id_facture", $value["id_facture"]);
						if ($facturation = ATF::facturation()->select_row()) {
							ATF::facturation()->u(array("id_facturation"=> $facturation["id_facturation"], "envoye"=> "oui"));
						}
					} else {
						log::logger("---- Le contact de facturation n'a pas de mail ou mail perso" , "sendFactureMail");
						$suivi_message = "Erreur lors de l'envoi de la facture  ".$ref." au client ".$societe["societe"]."\nRaison: Pas d'email sur le contact de facturation (".$contact['nom']." ".$contact['prenom'].")";
					}
				} else {
					$suivi_message = "Erreur lors de l'envoi de la facture  ".$ref." au client ".$societe["societe"]."\nRaison: Pas de contact de facturation";
				}

			} catch (errorATF $e) {
				$suivi_message = "Erreur lors de l'envoi de la facture  ".$ref." au client ".$societe["societe"]."\nRaison: ".$e->getMessage();
			}

			$suivi = array(
				"id_societe"=> $facture_info["id_societe"]
				,"id_affaire"=> $facture_info["id_affaire"]
				,"type_suivi"=>'Comptabilité'
				,"texte"=>$suivi_message
				,'public'=>'oui'
				,'id_contact'=>NULL
				,'suivi_societe'=>NULL
				,'suivi_notifie'=>NULL
				,'champsComplementaire'=>NULL
			);
			$suivi["no_redirect"] = true;

			ATF::suivi()->insert($suivi);

		}
		log::logger("Fin du batch ".$date , "sendFactureMail");
		log::logger("-----------------------------" , "sendFactureMail");
	}

	/**
	* Recupere le status SLIMPAY d'une demande de prélèvement et met à jour le status si celui ci à changé
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @author Fransisco FERNANDEZ <ffrenandez@absystech.fr>
	*/
	public function statusDebitEnCours(){

		$this->q->reset()->where("facture.date", date("Y-m-d", strtotime("-1 year")), "AND", null, ">=");
		if($factures = $this->select_all()){
			foreach ($factures as $kfacture => $vfacture) {

				//On récupère la derniere transaction
				ATF::slimpay_transaction()->q->reset()->where("id_facture", $vfacture["facture.id_facture"])->addOrder("id_slimpay_transaction", "DESC");
				$transaction = ATF::slimpay_transaction()->select_all();
				if($transaction){

					//On récupère la derniere transaction connue (en BDD) pour cette facture
					$state = ATF::slimpay()->getStatutDebit($transaction[0]["ref_slimpay"]);
					 // $state = json_decode($transaction[0]["retour"], true); // Pour du DEV


					//Si le state retourné par SLIMPAY est different de celui en BDD, on met à jour
					if($state["executionStatus"] != $transaction[0]["executionStatus"]){
						ATF::slimpay_transaction()->u(array("id_slimpay_transaction"=> $transaction[0]["id_slimpay_transaction"],
															"executionStatus"=>$state["executionStatus"],
															"retour"=>json_encode($state)
													  ));

						//Si le statut de la transaction est rejected, il faut allez rechercher la Transaction rejouée
						if($state["executionStatus"] == "rejected") {

							ATF::constante()->q->reset()->where("constante","__NOTIFIE_PRELEVEMENT_IMPAYEE__");
							$notifie_impaye = ATF::constante()->select_row();

							//un suivi sans destinataire "Facture xxxx impayée"
							$suivis = array("suivi"=> array(
													"id_societe" => $this->select($vfacture["facture.id_facture"] , "id_societe"),
													"type" => "note",
													"date" => date("Y-m-d H:i:s"),
													"texte" => "Facture ".$this->select($vfacture["facture.id_facture"] , "ref")." impayée",
													"id_affaire" => $this->select($vfacture["facture.id_facture"] , "id_affaire"),
													"type_suivi" => "Contrat",
													"no_redirect" => true,
													"suivi_notifie"=>array($notifie_impaye["valeur"])
											  	)
											);

							ATF::suivi()->insert($suivis);

							switch ($state["rejectedReason"]) {
								case 'MS02':
								case 'MD07':
									$customKey = 'contestation_debiteur';
									break;
								case 'AM04':
								case '411':
									$customKey = 'provision_insuffisante';
									break;
								case '641':
								case 'C11':
									$customKey = 'opposition_compte';
									break;
								case '903':
									$customKey = 'decision_judiciaire';
									break;
								case 'AC04':
									$customKey = 'compte_cloture';
									break;
								case 'AC01':
								case 'RC01':
								case 'MD01':
								case 'MD02':
								case 'CNOR':
								case 'DNOR':
									$customKey = 'coor_banc_inexploitable';
									break;
								case '2011':
								case 'AG01':
									$customKey = 'pas_dordre_de_payer';
									break;
								default:
									$customKey = 'non_preleve';
									break;
							}
							ATF::facture()->updateEnumRejet(
								array(
									"id_facture" => $vfacture["facture.id_facture"],
									"key" => "rejet",
									"value" => $customKey,
									"date_rejet" =>  $state["executionDate"]
								)
							);
						}
					}
				}

			}
		}


	}

	/**
	* Renvoi toutes les factures equi ne sont pas payé et qui n'ont pas au moins 1 transaction SLIMPAY
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function aPrelever($infos){
		//Recuperer les factures qui n'ont pas de prelevement SLIMPAY  & les factures donc le dernier prelevement est Rejected
		$q = "select f.*
		from facture f
		where etat='impayee'
		and f.date_paiement is null
		and f.id_facture not in (select id_facture from slimpay_transaction st)";

		$return = ATF::db()->sql2array($q);
		return $this->retourAffichageAPrelever($return);
	}

	/**
	* Renvoi toutes les factures equi ne sont pas payé et qui n'ont pas au moins 1 transaction SLIMPAY
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function aPreleverEchec($infos){
		//Recuperer les factures qui n'ont pas de prelevement SLIMPAY  & les factures donc le dernier prelevement est Rejected
		$q = "select f.*
		from facture f
		where etat='impayee'
		and (
			f.id_facture in (
				select st2.id_facture
				from slimpay_transaction st2
				where st2.id_slimpay_transaction in (
					select max(st3.id_slimpay_transaction)
					from slimpay_transaction st3
					where st3.id_facture =st2.id_facture
					and st3.executionStatus='rejected'
				)
			)
		)";

		$return = ATF::db()->sql2array($q);
		return $this->retourAffichageAPrelever($return);

	}

	public function retourAffichageAPrelever($return) {
		foreach ($return as $key => $value) {
			$return[$key]["client"] = ATF::societe()->nom($value["id_societe"]);
			$return[$key]["date"] = date("d/m/Y" , strtotime($return[$key]["date"]));
			$return[$key]["jour_previsionnel"] = ATF::affaire()->select($value["id_affaire"], "date_previsionnelle");
			$return[$key]["date_periode_debut"] = $return[$key]["date_periode_debut"] ? date("d/m/Y" , strtotime($return[$key]["date_periode_debut"])) : "";
			$return[$key]["date_periode_fin"] = $return[$key]["date_periode_fin"] ? date("d/m/Y" , strtotime($return[$key]["date_periode_fin"])): "";
			$return[$key]["prix_ttc"] =  number_format(($value["prix"] * $value["tva"]), 2 , ".", "");

			$id_type_affaire = ATF::affaire()->select($value["id_affaire"], "id_type_affaire");
			if ($id_type_affaire) {
				if (ATF::type_affaire()->select($id_type_affaire, "assurance_sans_tva") == "oui" && $value["prix_sans_tva"] != 0) {
					$return[$key]["prix_ttc"] = number_format((($value["prix"] * $value["tva"]) + $value["prix_sans_tva"] ) , 2 , ".", "");
				}
			}
		}

		switch(ATF::$codename){
			case "bdomplus":
				$libelle = "Abonnement BDOM+ ".ATF::$usr->trans(date("F", strtotime("+1 month")))." ".date("Y", strtotime("+1 month"));
			break;

			case "go_abonnement":
				$libelle = "Abonnement GO Abonnement ".ATF::$usr->trans(date("F", strtotime("+1 month")))." ".date("Y", strtotime("+1 month"));
			break;

			case "assets":
				$libelle = "Abonnement Assets ".ATF::$usr->trans(date("F", strtotime("+1 month")))." ".date("Y", strtotime("+1 month"));
			break;

			default:
				$libelle = "Location Cléodis ".ATF::$usr->trans(date("F", strtotime("+1 month")))." ".date("Y", strtotime("+1 month"));
		}

		$result = array(
						"libelle"=> $libelle,
						"date_prelevement"=> date("Y-m-01", strtotime("+1 month")),
						"lignes" => $return
					   );

		return $result;
	}
	/**
	 * Retourne les dernieres affaires
	 * Utilisé par l'espace client / conseiller / adv afin d'afficher le module sur la homepage
	 *	@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 *
	 * @return Array
	 */
	public function _getLastFactures($get, $post) {
		ATF::facture()->q->reset()
						 ->addField('affaire.affaire', 'dossier')
						 ->addField('facture.date', 'date')
						 ->addField('type_affaire.type_affaire', 'type_affaire')
						 ->addField('type_affaire.assurance_sans_tva', 'assurance_sans_tva')
						 ->addField('facture.prix', 'prix')
						 ->addField('facture.prix_sans_tva', 'prix_sans_tva')
						 ->addField('facture.etat', 'etat')
						 ->addField('facture.designation', 'designation')
						 ->addField('facture.ref', 'ref')
						 ->addField('facture.ref_externe', 'ref_externe')
						 ->addField('facture.tva', 'tva')

						->addField('client.societe', "societe")
						->addField('client.ref', "ref_societe")

						->from("facture","id_affaire","affaire","id_affaire","affaire")
						->from("affaire","id_type_affaire","type_affaire","id_type_affaire","type_affaire")
						->from("facture","id_societe","societe","id_societe","client")

						->setLimit($post['limit'])
						->addOrder('facture.date', 'DESC');

		if ($post['id_societe']) {
			ATF::facture()->q->where('affaire.id_societe', $post['id_societe']);
		}
		return ATF::facture()->sa();

	}

	/**
	* Regrouper les factures du meme mandat SLIMPAY et meme date de prelevement et envoyer le prélèvement SLIMPAY
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos["libelle] String
	* @param array $infos["factures] JSON Stringify [{id_facture:..., date_prelevement: ...},{id_facture:..., date_prelevement: ...}]
	*/
	public function _massPrelevementSlimpay($infos) {
		$factures = json_decode($infos["factures"]);

		$data = [];

		foreach ($factures as $key => $value) {

			$f = ATF::facture()->select($value->id_facture);
			$mandat_slimpay = $this->getMandatSlimpay($f["id_affaire"]);

			$data[$mandat_slimpay][$value->date_prelevement]["libelle"] .= $f["ref"]." ";


			$prix = $f["prix"] * $f["tva"];
			$id_type_affaire = ATF::affaire()->select($f["id_affaire"], "id_type_affaire");
			if ($id_type_affaire) {
				if (ATF::type_affaire()->select($id_type_affaire, "assurance_sans_tva") == "oui" && $f["prix_sans_tva"] != 0) {
					$prix = ($f["prix"] * $f["tva"]) + $f["prix_sans_tva"];
				}
			}

			if($data[$mandat_slimpay][$value->date_prelevement]["paymentReference"]){
				$data[$mandat_slimpay][$value->date_prelevement]["prix"] = number_format($data[$mandat_slimpay][$value->date_prelevement]["prix"] + $prix, 2 , ".", "");
				$data[$mandat_slimpay][$value->date_prelevement]["id_facture"][] = $value->id_facture;

				$id_affaire = $this->getAffaireMere($f["id_affaire"]);
				$d = str_replace(ATF::affaire()->select($id_affaire, "ref"), "", $f["ref"]);

				$data[$mandat_slimpay][$value->date_prelevement]["paymentReference"] .= "/".$d;
			}else{
				$data[$mandat_slimpay][$value->date_prelevement]["prix"] = number_format($prix,2 , ".", "");
				$data[$mandat_slimpay][$value->date_prelevement]["id_facture"][] = $value->id_facture;
				$data[$mandat_slimpay][$value->date_prelevement]["paymentReference"] = $f["ref"];
			}
		}

		foreach ($data as $mandat => $dates) {
			foreach ($dates as $date => $value) {
				if(!$infos["libelle"]) $infos["libelle"] = $value["libelle"];
				$this->createPrelEtSlimpayTransaction($value["id_facture"], $mandat,$value["prix"],$infos["libelle"], $date,$value["paymentReference"]);
			}

		}
	}


	/**
	* Regrouper les factures du meme mandat SLIMPAY et envoyer le prélèvement SLIMPAY
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*
	*/
	public function massPrelevementSlimpay($infos){

		$data = array();

		if($infos["factures"]){
			foreach ($infos["factures"] as $key => $value) {
				$f = ATF::facture()->select($key);
				$mandat_slimpay = $this->getMandatSlimpay($f["id_affaire"]);

				$data[$mandat_slimpay]["libelle"] .= $f["ref"]." ";

				$prix = $f["prix"] * $f["tva"];
				$id_type_affaire = ATF::affaire()->select($f["id_affaire"], "id_type_affaire");
				if ($id_type_affaire) {
					if (ATF::type_affaire()->select($id_type_affaire, "assurance_sans_tva") == "oui" && $f["prix_sans_tva"] != 0) {
						$prix = ($f["prix"] * $f["tva"]) + $f["prix_sans_tva"];
					}
				}

				if($data[$mandat_slimpay]["paymentReference"]){
					$data[$mandat_slimpay]["prix"] = number_format($data[$mandat_slimpay]["prix"] + $prix, 2 , ".", "");
					$data[$mandat_slimpay]["id_facture"][] = $key;

					$id_affaire = $this->getAffaireMere($f["id_affaire"]);
					$d = str_replace(ATF::affaire()->select($id_affaire, "ref"), "", $f["ref"]);

					$data[$mandat_slimpay]["paymentReference"] .= "/".$d;
				}else{
					$data[$mandat_slimpay]["prix"] = number_format($prix,2 , ".", "");
					$data[$mandat_slimpay]["id_facture"][] = $key;
					$data[$mandat_slimpay]["paymentReference"] = $f["ref"];
				}
			}

			foreach ($data as $key => $value) {
				if(!$infos["libelle"]) $infos["libelle"] = $value["libelle"];
				$this->createPrelEtSlimpayTransaction($value["id_facture"], $key,$value["prix"],$infos["libelle"], $infos["date"],$value["paymentReference"]);
			}
		}
		return true;
	}

	public function createPrelEtSlimpayTransaction($factures, $ref_mandate, $montant, $libelle, $date_prelevement, $paymentReference) {

		$status = ATF::slimpay()->createDebit($ref_mandate, $montant, $libelle, $date_prelevement, $paymentReference);

		foreach ($factures as $kfacture => $vfacture) {

			ATF::slimpay_transaction()->i(
				array(
					"id_facture"=> $vfacture,
					"ref_slimpay" => $status["id"],
					"executionStatus"=>$status["executionStatus"],
					"date_execution"=>$status["executionDate"],
					"retour"=> json_encode($status)
				)
			);
			$infos_facture = $this->select($vfacture);

			$suivis = array("suivi"=>
					array(
					"id_societe" => $infos_facture["id_societe"],
					"id_affaire" => $infos_facture["id_affaire"],
					"type" => "note",
					"date" => date("Y-m-d H:i:s"),
					"texte" => "Prélèvement envoyé à Slimpay pour la facture ".$infos_facture["ref"]." ; prélévement prévu le ".date("d/m/Y", strtotime($date_prelevement))." libellé envoyé :".$libelle,
					"type_suivi" => "Comptabilité",
					"no_redirect" => true,
				)
			);
			ATF::suivi()->insert($suivis);

			$this->updateDate(array("id_facture" => $vfacture,"key"=> "date_paiement", "value" =>$date_prelevement));
		}
	}



};

class facture_cleodisbe extends facture_cleodis { };
class facture_itrenting extends facture_cleodis { };
class facture_cap extends facture_cleodis { };

class facture_bdomplus extends facture_cleodis {

	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		$this->fieldstructure();

		$this->onglets = array('facture_ligne','slimpay_transaction');

		$this->addPrivilege("export_bdomplus");

		$this->addPrivilege("aPrelever");
		$this->addPrivilege("massPrelevementSlimpay");
	}

	public function getRefExterne(){
		$prefix = "F930C";

		$this->q->reset()
				->addCondition("ref_externe",$prefix."%","AND",false,"LIKE")
				->addField('SUBSTRING(`ref_externe`,6)+1',"max_ref")
				->addOrder('ref_externe',"DESC")
				->setDimension("row")
				->setLimit(1);
		$nb=$this->sa();


		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="00000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="0000".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<10000){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<100000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="000001";
		}
		return $prefix.$suffix;

	}


	public function export_bdomplus(&$infos){
		$infos["display"] = true;

		$q = "SELECT facture.*
		 	  FROM facture
			  WHERE `id_facture` NOT IN (SELECT id_facture FROM export_facture WHERE `fichier_export` = 'flux_vente')";

		$data = ATF::db()->sql2array($q);

		ATF::db()->begin_transaction();
		try{
			foreach ($data as $key => $value) {

				$snapshot = ATF::affaire()->select($value["id_affaire"], "snapshot_pack_produit");

				$json=json_decode($snapshot,true);

				$principal = array("produit"=>"", "ref"=>"");

				if($json){
					foreach ($json["lignes"] as $kl => $vl) {
						if($vl["principal"] == "oui"){
							$principal["produit"] = $vl["produit"]["produit"];
							$principal["ref"] = $vl["produit"]["ref"];
						}
					}
				}



				$client = ATF::societe()->select($value["id_societe"]);


				$donnees[$key][$i][1] = substr($value["ref_externe"], 4);
		    	$donnees[$key][$i][2] = substr($value["ref_externe"], 0, 4);
		    	$donnees[$key][$i][3] = $client["particulier_nom"];
		    	$donnees[$key][$i][4] = $client["particulier_prenom"];
		    	$donnees[$key][$i][5] = $client["cp"]; // Code postal factuation
		    	$donnees[$key][$i][6] = $client["ville"];; //Ville facturation
		    	$donnees[$key][$i][7] = "";
		    	$donnees[$key][$i][8] = $client["particulier_portable"]; // Telephone portable
		    	$donnees[$key][$i][9] = $client["particulier_email"];
		    	$donnees[$key][$i][10] = $principal["ref"];
		    	$donnees[$key][$i][11] = $principal["produit"];
		    	$donnees[$key][$i][12] = number_format($value["prix"] * $value["tva"], 2 , ",", "");
		    	$donnees[$key][$i][13] = "";
				$donnees[$key][$i][14] = date("Ymd", strtotime($value["date"]));
				$donnees[$key][$i][15] = "";
		    	$donnees[$key][$i][16] = "";
		    	$donnees[$key][$i][17] = "";
		    	$donnees[$key][$i][18] = "";
		    	$donnees[$key][$i][19] = "";
				$donnees[$key][$i][20] = "";
				$donnees[$key][$i][21] = "";
		    	$donnees[$key][$i][22] = "";
				$donnees[$key][$i][23] = "";
				$donnees[$key][$i][24] = "";
				$donnees[$key][$i][25] = $client["adresse"]; //Adresse facturation
				$donnees[$key][$i][26] = $client["adresse_2"]; //Adresse facturation 2
				$donnees[$key][$i][27] = $client["adresse_3"];
				$donnees[$key][$i][28] = "";
				$donnees[$key][$i][29] = "";
				$donnees[$key][$i][30] = "FRA";
				$donnees[$key][$i][31] = $value["ref_magasin"]; // Ref de la facture magasin

				ATF::export_facture()->i(array("id_facture" => $value["id_facture"], "fichier_export"=> "flux_vente"));
			}

			$string = "";


	        $filename = 'CLEODIS_VT.csv';

	        foreach ($donnees as $key => $value) {
				foreach ($value as $k => $v) {
					for($i=1;$i<=31;$i++){
						if(isset($v[$i])){
							$string .= $v[$i];
							if($i!=31) $string .= ";";
						}else{
							if($i!=31) $string .= ";";
						}
					}
					$string .= "\n";
				}
			}

			ATF::db()->commit_transaction();
		}catch(errorATF $e){
			ATF::db()->rollback_transaction();
			throw new errorATF("Erreur lors de la génération du fichier");
		}

    	header("Content-type: text/csv");
    	header("Content-Transfer-Encoding: UTF-8");
		header("Content-Disposition: attachment; filename=bdomplus".date("Ymd").".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
        echo $string;

	}

};
class facture_boulanger extends facture_cleodis {
	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		$this->fieldstructure();



		unset($this->files["fichier_joint"], $this->colonnes['fields_column']["fichier_joint"]);

	}

	public function getRefExterne(){
		$prefix = "F930C";

		$this->q->reset()
				->addCondition("ref_externe",$prefix."%","AND",false,"LIKE")
				->addField('SUBSTRING(`ref_externe`,6)+1',"max_ref")
				->addOrder('ref_externe',"DESC")
				->setDimension("row")
				->setLimit(1);
		$nb=$this->sa();


		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="00000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="0000".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<10000){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<100000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="000001";
		}
		return $prefix.$suffix;
	}


};

class facture_assets extends facture_cleodis { };

class facture_go_abonnement extends facture_cleodis {

	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		$this->fieldstructure();

		$this->onglets = array('facture_ligne','slimpay_transaction');

		$this->addPrivilege("aPrelever");
		$this->addPrivilege("massPrelevementSlimpay");
	}

	function getRef($id_affaire,$type="facture"){
		$affaire=ATF::affaire()->select($id_affaire);

		$this->q->reset()
				->addCondition("id_affaire",$id_affaire)
				->addCondition("type_facture",$type)
				->addOrder("ref_reel","DESC")
				->setDimension("row");

		if($affaire["nature"]=='avenant'){
			$this->q->addField('ROUND( SUBSTRING(  `ref` , 15 ) )',"ref_reel");
		}else{
			$this->q->addField('ROUND( SUBSTRING(  `ref` , 11 ) )',"ref_reel");
		}

		$facture=$this->sa();

		if(!$facture){
			$suffix=0;
		}else{
			$suffix=$facture["ref_reel"];
		}

		if($type=="ap"){
			$sufType="-AP";
		}elseif($type=="refi"){
			$sufType="-RE";
		}elseif($type=="libre"){
			$sufType="-LI";
		}elseif($type=="facture"){
			$sufType="";
		}

		//Si jamais pour une raison x ou y la ref existe déjà il faut incrémenter jusqu'à trouver une ref dispo (problème lorsque cléodis se trompe de type et que l'on doit modifier le type sans changer la ref...)
		$find=false;
		$i=1;
		while($find==false){
			$this->q->reset()->addCondition("ref",$affaire["ref"]."-".($suffix+$i).$sufType);
			if(!$this->sa()){
				$ref=$affaire["ref"]."-".($suffix+$i).$sufType;
				$find=true;
			}else{
				$i++;
			}
		}

		return $ref;

	}

	public function getRefExterne(){
		$prefix = "F";

		$this->q->reset()
				->addCondition("ref_externe",$prefix."%","AND",false,"LIKE")
				->addField('SUBSTRING(`ref_externe`,2)+1',"max_ref")
				->addOrder('ref_externe',"DESC")
				->setDimension("row")
				->setLimit(1);
		$nb=$this->sa();


		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="00000000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="0000000".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="000000".$nb["max_ref"];
			}elseif($nb["max_ref"]<10000){
				$suffix="00000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100000){
				$suffix="0000".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000000){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<10000000){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<100000000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="000000001";
		}
		return $prefix.$suffix;

	}


	/** Mise en place des titres
	 * @author Morgan Fleurquin <mfleurquin@absystech.fr>
     * @param array $sheet : contient l'onglet
     */
	public function ajoutTitre(&$sheet){
        $row_data = array(
        	"A"=>''
        	,"B"=>'Date d\'écriture'
			,"C"=>'Journal'
			,"D"=>'Comptes'
			,"E"=>'Code libellé'
			,"F"=>'Sens'
			,"G"=>'Montant'
			,"H"=>'Libellé'
			,"I"=>'N° de pièce'
			,"J"=>'Centre'
			,"K"=>'Périodicité - date de début'
			,"L"=>'Périodicité - date de fin'
			,"M"=>'Date d\'échéance'
		);

		 $i=0;
		 foreach($row_data as $col=>$titre){
			$sheet->setCellValueByColumnAndRow($i , 1, $titre);
			$i++;
        }
     }

	/** Mise en place du contenu
	* @author Morgan Fleurquin <mfleurquin@absystech.fr>
    * @param array $sheets : contient l'onglet
    * @param array $infos : contient tous les enregistrements
    */
    public function ajoutDonnees(&$sheet,$infos){

		$row_auto=1;
		$increment=0;
		foreach ($infos as $key => $item) {
			$increment++;
			if($item){

				$id_affaire = ATF::facture()->select($item["facture.id_facture_fk"], "id_affaire");
				$id_societe = ATF::affaire()->select($id_affaire, "id_societe");

				$num_chassis = ATF::affaire()->select($id_affaire, "num_chassis");

				$ligne = [];

				// On recupere l'affaire pour voir si il y a un type d'affaire et si il n'y a pas de TVA sur Assurance
				$sans_tva = false;
				$id_type_affaire = ATF::affaire()->select($id_affaire, "id_type_affaire");
				if ($id_type_affaire) {
					if (ATF::type_affaire()->select($id_type_affaire , "assurance_sans_tva") == "oui"){
						$sans_tva = true;
					}
				}

				$choix = "defaut";


				if ($item["facture.type_facture"] === "facture") {
					if ($item["facture.prix"] > 0) {
						$choix = "facture_mensuelle";
					}
				} elseif ($item["facture.type_facture"] == "avoir" && ($item["facture.nature"] == "engagement" || $item["facture.nature"] == "contrat")) {
					$choix = "avoir_facture_mensuelle";

				} elseif ($item["facture.type_facture"] == "libre") {
					if ($item["facture.type_libre"] == "prorata") {
						if ($item["facture.prix"] > 0) {
							$choix = "facture_prorata";
						} else {
							$choix = "avoir_facture_prorata";
						}
					}elseif (($item["facture.nature"] == "engagement" || $item["facture.nature"] == "contrat")) {
						if ($item["facture.prix"] < 0) {
							$choix = "avoir_facture_mensuelle";
						} else {
							$choix = "facture_mensuelle";
						}
					}elseif ($item["facture.nature"] == "prolongation") {
						if ($item["facture.prix"] > 0) {
							$choix = "facture_prolongation";
						} else {
							$choix = "avoir_facture_prolongation";

						}
					}
				}

				$ligne[1]["D"] = ATF::societe()->select($id_societe, "ref");
				$ligne[1]["F"] = "D";
				$ligne[2]["F"] = "C";
				$ligne[3]["F"] = "C";

				switch ($choix) {
					case "facture_mensuelle":
					case "avoir_facture_mensuelle":
						$code_libelle = "F";

						$ligne[2]["D"] = "706200";
						$ligne[3]["D"] = "445712";
						$ligne[4]["D"] = "706500";
						$ligne[4]["F"] = "C";

						if ($choix === "avoir_facture_mensuelle") {
							$code_libelle = "A";
							$ligne[1]["F"] = "C";
							$ligne[2]["F"] = "D";
							$ligne[3]["F"] = "D";
							$ligne[4]["F"] = "D";
						}
					break;

					case "facture_prorata":
					case "avoir_facture_prorata":
						$ligne[2]["D"] = "706300";
						$ligne[3]["D"] = "445715";
						$ligne[4]["D"] = "706500";
						$ligne[4]["F"] = "C";
						$code_libelle = "F";

						if ($choix === "avoir_facture_prorata") {
							$code_libelle = "A";
							$ligne[1]["F"] = "C";
							$ligne[2]["F"] = "D";
							$ligne[3]["F"] = "D";
							$ligne[4]["F"] = "D";
						}
					break;

					case "facture_prolongation":
					case "avoir_facture_prolongation":
						$code_libelle = "F";
						$ligne[2]["D"] = "706220";
						$ligne[3]["D"] = "445713";
						$ligne[4]["D"] = "706500";
						$ligne[4]["F"] = "C";
						if ($choix === "avoir_facture_prolongation") {
							$code_libelle = "A";
							$ligne[1]["F"] = "C";
							$ligne[2]["F"] = "D";
							$ligne[3]["F"] = "D";
							$ligne[4]["F"] = "D";
						}
					break;

					default:

						$ligne[2]["D"] = "706400";
						$ligne[3]["D"] = "445710";
						$code_libelle = "F";

						if ($item["facture.prix"] < 0 || $item["facture.type_facture"] == "avoir") {
							$code_libelle = "A";
							$ligne[1]["F"] = "C";
							$ligne[2]["F"] = "D";
							$ligne[3]["F"] = "D";
						}

					break;
				}

				log::logger($item["facture.ref_externe"] ." --- ". $choix , "mfleurquin");

				//insertion des donnÃ©es
				foreach ($ligne as $key => $value) {


					$row_data=array();

					$row_data["A"] = '';
					$row_data["B"] = $item['facture.date'];
					$row_data["C"] = 'VEN';
					$row_data["D"] = $value["D"];
					$row_data["E"] = $code_libelle;
					$row_data["F"] = $value["F"];


					switch($key) {
						case "1":
							if ($sans_tva) {
								$total = ($item["facture.prix"] * $item["facture.tva"]) + $item["facture.prix_sans_tva"];
							} else {
								$total = ($item["facture.prix"] * $item["facture.tva"]);
							}
						break;

						case "2":
							$total = $item["facture.prix"];
						break;

						case "3":

							$total = ($item["facture.prix"] * $item["facture.tva"]) - $item["facture.prix"];
						break;

						case "4":
							$total = $item["facture.prix_sans_tva"];
						break;
					}
					$row_data["G"] = round(abs($total),2);

					$row_data["H"] = $item["facture.id_affaire"]."-".$item["facture.id_societe"] ;
					$row_data["I"] = $item["facture.ref_externe"];
					$row_data["J"] = substr($num_chassis, -10)." ";
					$row_data["K"] = ($item['facture.date_periode_debut']) ? $item['facture.date_periode_debut'] : "";
					$row_data["L"] = ($item['facture.date_periode_fin']) ? $item['facture.date_periode_fin'] : "";
					$row_data["M"] = ($item['facture.date_previsionnelle']) ? $item['facture.date_previsionnelle'] : "";

					if ($key == 4 && (!$sans_tva || $item["facture.prix_sans_tva"] == 0)) {
						$row_data = array();
					}



					if($row_data){
						$indexCol = 0;
						$row_auto++;
						foreach($row_data as $col=>$valeur){
							if (($col === "B" || $col === "K" || $col === "L" || $col === "M") && $valeur !== "" ) {
								$dateTime = new DateTime($valeur);
								$sheet->setCellValueByColumnAndRow($indexCol , $row_auto, PHPExcel_Shared_Date::PHPToExcel( $dateTime ));
								$sheet->getStyleByColumnAndRow($indexCol , $row_auto)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
							} else {

								$sheet->setCellValueByColumnAndRow($indexCol , $row_auto, $valeur);
							}
							$sheet->getColumnDimension($col)->setAutoSize(true);
							$indexCol++;
						}

					}
				}
			}
		}
	}

};