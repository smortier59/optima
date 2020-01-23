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
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","width"=>50,"align"=>"center")
            ,'facture.rejet'=>array("renderer"=>"updateEnumFactureRejetCledodis","width"=>200)
            ,'relance'=>array("custom"=>true,"nosort"=>true,"renderer"=>"relanceFacture","width"=>70)
            ,'facture.date_rejet'=>array("renderer"=>"updateDate","width"=>170)
            ,'facture.date_regularisation'=>array("renderer"=>"updateDate","width"=>170)
            ,'facture.nature'
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
			"prix"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);

		$this->colonnes['panel']['dates_facture_libre'] = array(
			"date_periode_debut_libre"=>array("custom"=>true,"xtype"=>"datefield"),
			"date_periode_fin_libre"=>array("custom"=>true,"xtype"=>"datefield"),
			"prix_libre"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
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

		$this->onglets = array('facture_ligne');
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
				if ($facture) {
					$periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true);
					if($date_previsionnelle=ATF::affaire()->select($facture['id_affaire'],"date_previsionnelle")){
						$day=$date_previsionnelle;
					}else{
						$day=0;
					}
					return date('Y-m-d',strtotime($periode["date_periode_debut"]."+".$day." day"));
				}else{
					return date("Y-m-d");
				}
			case "date_periode_debut":
				if ($facture) {
					$periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true);
					return $periode["date_periode_debut"];
				}
				break;
			case "date_periode_fin":
				if ($facture) {
					$periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true);
					return $periode["date_periode_fin"];
				}
				break;
			case "prix":
				if ($facture) {
					if($periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true)){
						$prix=($periode["montant"]+$periode["frais_de_gestion"]+$periode["assurance"]);
					}elseif(ATF::affaire()->select($facture['id_affaire'],"nature"=="vente")){
						$prix=$facture["prix"];
					}
				}
				if($prix){
					return $prix;
				}
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



			$dateFinPeriode = $dateFinPeriode->format('t-m-Y');

			if($nbJProRata > 0 && $total != 0){
				$facture["facture"] = array(
		            "id_societe" => $affaire["id_societe"],
		            "type_facture" => "libre",
		            "mode_paiement" => "prelevement",
		            "id_affaire" => $affaire["id_affaire"],
		            "type_libre" => "normale",
		            "date" => date("d-m-Y"),
		            "id_commande" => $commande["id_commande"],
		            "date_previsionnelle" => date("d-m-Y"),
		            "date_periode_debut" => $infos["date_installation_reel"],
		            "date_periode_fin" => $dateFinPeriode,
		            "prix" => round($total, 2),
		            "date_periode_debut_libre" => $infos["date_installation_reel"],
		            "date_periode_fin_libre" => $dateFinPeriode,
		            "prix_libre" => round($total, 2),
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

		if(strtotime(date("Y-m-d")) < strtotime($infos["date_debut_contrat"])){
			$date_previsionnelle = $infos["date_debut_contrat"];
		}
		else{
			$date_previsionnelle = date("Y-m-d");
		}

		$dateTimeDebContrat = new DateTime(date("Y-m-d", strtotime($infos["date_debut_contrat"])));

		$totalLoyer = $loyers[0]["loyer"] + $loyers[0]["assurance"] + $loyers[0]["frais_de_gestion"];

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
		$dateFinPeriode = $dateFinPeriode->format('t-m-Y');

		if($totalLoyer != 0){
			$mode_paiement = "prelevement";
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
	            "prix_libre" => round($totalLoyer, 2),
	            "prix" => round($totalLoyer, 2),
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

		if(($infos["type_facture"] === "libre") && (!$infos["nature"])){
			throw new errorATF("Il faut une nature pour la facture libre",351);
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
			if($infos["type_libre"] !== "contentieux" ){
				$infos["tva"]=1;
			}
		}elseif($infos["type_facture"]=="midas"){
			unset($infos_ligne_repris , $infos_ligne_non_visible , $infos_ligne);
			$infos["prix"]=$infos["prix_midas"];
			$infos["commentaire"] = $infos["periode_midas"];
		}elseif($infos["type_facture"]=="facture"){
			if($facturation= ATF::facturation()->periode_facturation($commande['id_affaire'])){
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
					}else{ $facturation = ATF::facturation()->periode_facturation($commande['id_affaire'],true); }
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

		if(ATF::$codename == "bdomplus" || ATF::$codename = "boulanger") $infos["ref_externe"] = $this->getRefExterne();


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
    * Fonction qui permet de mettre à jour la date
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    * @param array $infos date garantie
    * @param type pour savoir si l'on cherche une affaire qui annule  et remplace ($type=='new') ou une affaire qui EST annulée et remplacée ($type=='old')
    * @return boolean à true si la transaction c'est bien passé
    */
	public function updateDate($infos){
		if ($infos['value'] == "undefined") $infos["value"] = "";
		$infos["key"]=str_replace($this->table.".",NULL,$infos["key"]);
		$infosMaj["id_".$this->table]=$infos["id_".$this->table];
		$infosMaj[$infos["key"]]=$infos["value"];

		if(($infos["key"] == "date_rejet") || ($infos["key"] == "date_regularisation")){
			$infos["id_facture"] = ATF::facture()->decryptId($infos["id_facture"]);
			if($infos["key"] == "date_rejet"){
				if(ATF::facture()->select($infos["id_facture"], "date_rejet") != NULL){
					throw new errorATF("Impossible de modifier une date de rejet car elle est déja renseignée",877);
					return true;
				}
			}

			if($infos["key"] == "date_regularisation"){
				$this->updateEnumRejet($infos);
			}

			$infosMaj["id_facture"] = $infos["id_facture"];

			if($this->u($infosMaj)){
				ATF::$msg->addNotice(
					loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
					,ATF::$usr->trans("notice_success_title")
				);
			}
			return true;
		}else{
			if($infosMaj[$infos["key"]]){
				$infosMaj["etat"]="payee";
			}else{
				$infosMaj["etat"]="impayee";
			}



			if($this->u($infosMaj)){
				ATF::$msg->addNotice(
					loc::mt(ATF::$usr->trans("notice_update_success_date"),array("record"=>$this->nom($infosMaj["id_".$this->table]),"date"=>$infos["key"]))
					,ATF::$usr->trans("notice_success_title")
				);

	//			$id_affaire=$this->select($infosMaj["id_".$this->table],"id_affaire");
	//			ATF::affaire()->redirection("select",$id_affaire);
			}
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
	public function updateEnumRejet($infos){
		if($this->select($infos["id_".$this->table],"etat")=="impayee"){
			$commande = $this->select($infos["id_facture"] , "facture.id_commande");
			ATF::commande()->q->reset()->where("commande.id_commande",$commande)->addField("commande.etat" , "etat");
			$etatCommande = ATF::commande()->select_row();
			$etatCommande = $etatCommande["etat"];

			if((($infos["value"] != "non_rejet") && ($infos["value"] != "non_preleve_mandat")) && (($infos["key"] != "date_regularisation" && $infos["value"] != "")) ){
				if(!stripos($etatCommande, "contentieux")){
					if($etatCommande === "mis_loyer" || $etatCommande === "prolongation" || $etatCommande === "restitution"){
						$etatCommande = $etatCommande."_contentieux";
					}
				}
			}else{
				if(!$this->contientFactureRejetee($commande, $infos["id_facture"])){
					if($etatCommande === "mis_loyer_contentieux"){
						$etatCommande = "mis_loyer";
					}elseif( $etatCommande === "prolongation_contentieux"){
						$etatCommande = "prolongation";
					}elseif( $etatCommande === "restitution_contentieux"){
						$etatCommande = "restitution";
					}
				}
			}
			ATF::commande()->u(array("id_commande" => $commande , "etat" => $etatCommande));

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
			ATF::affaire()->redirection("select",ATF::affaire()->cryptId(ATF::commande()->select($commande, id_affaire)));
			return true;
		}else{
			throw new errorATF("Impossible de modifier ce ".ATF::$usr->trans($this->table)." car elle est en '".ATF::$usr->trans("payee")."'",877);
		}
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

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle('Autoporté');
		$sheets=array("auto"=>$worksheet_auto);

		//mise en place des titres
		$this->ajoutTitre($sheets);

		//ajout des donnÃ©es
		if($infos){
			 $this->ajoutDonnees($sheets,$infos);
		}

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_comptable.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
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
     public function ajoutTitre(&$sheets){
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

         foreach($sheets as $nom=>$onglet){
             foreach($row_data as $col=>$titre){
				  $sheets[$nom]->write($col.'1',$titre);
				  $sheets[$nom]->sheet->getColumnDimension($col)->setAutoSize(true);
             }
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
     public function ajoutDonnees(&$sheets,$infos){

		$row_auto=1;
		$increment=0;
		foreach ($infos as $key => $item) {
			$increment++;
			if($item){
				//initialisation des données
				$devis=ATF::devis()->select_special("id_affaire",$item['facture.id_affaire_fk']);
				$infos_commande=ATF::commande()->select($item['facture.id_commande_fk']);
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

				// Récupération de : Date de debut, de fin et de prélèvement
				$dateDebut = ($item['facture.date_periode_debut']) ? " ".date("d/m/y",strtotime($item['facture.date_periode_debut'])) : " ";
				$dateFin = ($item['facture.date_periode_fin']) ? " ".date("d/m/y",strtotime($item['facture.date_periode_fin'])) : " ";
				$datePrelevement = " ".date("dmY",strtotime($item['facture.date_periode_debut']." + ".$affaire['date_previsionnelle']." DAY"));

				$refinancement = "";

				ATF::demande_refi()->q->reset()->where("id_affaire",$item['facture.id_affaire_fk'],"AND")
											   ->where("etat","valide");
				$ResRefinancement = ATF::demande_refi()->select_row();

				if($ResRefinancement){
					$refinancement = ATF::refinanceur()->select($ResRefinancement["id_refinanceur"] , "refinanceur");
				}


				$choix = "defaut";

				if($item['facture.type_facture'] == "libre"){
					if($item["facture.type_libre"] == "cout_copie") $choix = "libre_cout_copie";
					if($$item["facture.type_libre"] == "transfert") $choix = "libre_transfert";
				}elseif($item['facture.type_facture']=='refi'){
					if($refinancement == "FRANFINANCE") $choix = "refi_refinanceur_SGEF";
					elseif($refinancement == "CLEOFI") $choix = "refi_refinanceur_CLEOFI";
					else  $choix = "refi_autre";
				}elseif ($devis[0]["tva"] == 1) {
					$choix = "facture_sans_tva";
				}else{
					if($affaire['nature']=="vente"){
						$choix = "affaire_vente";
					}else{
						//Prolongation
						if($item['facture.date_periode_debut'] && $infos_commande['date_debut'] && $infos_commande['date_evolution']
															   && ($item['facture.date_periode_debut']>$infos_commande['date_evolution'])){
							$choix = "prolongation";
						}
						// Pro rata
						elseif( $item['facture.date_periode_debut'] && $infos_commande['date_debut'] && ($item['facture.date_periode_debut']<$infos_commande['date_debut'])){
							$choix = "pro_rata";
						}else{

							if($item['facture.date_periode_debut']){
								$commande = ATF::commande()->select($item['facture.id_commande_fk']);
								//Si le contrat est en cours pendant la période de la facture, pas d'analytique
								if(strtotime($commande["date_debut"]) <= strtotime($item['facture.date_periode_debut']) && strtotime($commande["date_evolution"]) >=  strtotime($item['facture.date_periode_fin'])){
								   	$en_cours = true;
								}else{ $en_cours = false; }

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


				$h = $item['facture.id_facture']."-".$societe['code_client'];

				//$h = 'F'.$affaire['ref'].'-'.$societe['code_client'].'/'.$societe['societe'];
				$ligne[1] = array("D"=> "411000" , "H"=> $h);
				$ligne[2] = array("D"=> "706400" , "H"=> $h);
				$ligne[3] = array("D"=> "706400" , "H"=> $h);
				$ligne[4] = array("D"=> "445710" , "H"=> $h);
				$libelle = $societe['code_client'];



				switch ($choix) {
					case 'libre_cout_copie':
						$ligne[1]["D"] ="411000";
						$ligne[2]["D"] ="706220";
						$ligne[3]["D"] ="706220";
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

					case 'prolongation':
						$ligne[2]["D"] = "706230";
						$ligne[3]["D"] = "706230";
						$ligne[4]["D"] = "445713";
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
						$row_data["B"] = " ".$date;
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
						$row_data["B"] = " ".$date;
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
						$row_data["B"] = " ".$date;
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
						$row_data["B"] = " ".$date;
						$row_data["C"] = 'VEN';
						$row_data["D"] = $ligne[$i]["D"];
						$row_data["E"] = "";
						$row_data["F"] = 'C';
						$row_data["G"] = round(abs(($item['facture.prix']*$item['facture.tva'])-$item['facture.prix']),2);
						$row_data["H"] = $ligne[$i]["H"];
						$row_data["I"] = $reference;
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
};

class facture_cleodisbe extends facture_cleodis {

	/** Mise en place du contenu
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $sheets : contient les 5 onglets
     * @param array $infos : contient tous les enregistrements
     */
     public function ajoutDonnees(&$sheets,$infos){
		$row_auto=1;
		$increment=0;
		foreach ($infos as $key => $item) {
			$increment++;
			if($item){
				//initialisation des données
				$devis=ATF::devis()->select_special("id_affaire",$item['facture.id_affaire_fk']);
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



				$refinancement = "";

				ATF::demande_refi()->q->reset()->where("id_affaire",$item['facture.id_affaire_fk'],"AND")
											   ->where("etat","valide");
				$ResRefinancement = ATF::demande_refi()->select_row();

				if($ResRefinancement){
					$refinancement = ATF::refinanceur()->select($ResRefinancement["id_refinanceur"] , "refinanceur");
				}


				// Récupération de : Date de debut, de fin et de prélèvement
				$dateDebut = " ".date("d/m/y",strtotime($item['facture.date_periode_debut']));
				$dateFin = " ".date("d/m/y",strtotime($item['facture.date_periode_fin']));
				$datePrelevement = " ".date("dmY",strtotime($item['facture.date_periode_debut']." + ".$affaire['date_previsionnelle']." DAY"));


				$infos_commande = ATF::commande()->select($item['facture.id_commande_fk']);


				$libelle = 'F'.$item['facture.id_facture'].'-'.$societe['code_client'].'/'.$societe['societe'];


				//Facture Cout copie
				if($item["facture.type_facture"] == "libre" && $item["facture.type_libre"] == "cout_copie"){
					$compte1 = "411000";
					$compte2 = "707230";
					$compte3 = "707230";
					$compte4 = "445710";
				}else if($item["facture.type_facture"] == "refi"){
					//Facture refinancement
					$compte1 = "411000";
					$compte2 = "707110";
					$compte3 = "707110";
					$compte4 = "445710";
					$dateDebut = "";
					$dateFin = "";
				}else if($refinancement == "BELFIUS LEASE SERVICES"){
					$compte1 = "411300";
					$compte2 = "707110";
					$compte3 = "707110";
					$compte4 = "445710";
				}else if(strtotime($infos_commande['date_debut']) > strtotime($item['facture.date_periode_debut'])){

					//Refinancé et autre CLEODIS BE
					if($refinancement !== "" && $refinancement !== "CLEODIS BE"){
						// Prorata sur contrat refinance
						$compte1 = "411000";
						$compte2 = "706300";
						$compte3 = "706300";
						$compte4 = "445710";
					} else {
						// Prorata sur contrat autoporte
						$compte1 = "411000";
						$compte2 = "706200";
						$compte3 = "706200";
						$compte4 = "445710";
					}
				}else if((strtotime($infos_commande['date_evolution']) < strtotime($item['facture.date_periode_debut']))
					){
					// Prolongation/Restitution CLEODIS BE
					$compte1 = "411000";
					$compte2 = "707230";
					$compte3 = "707230";
					$compte4 = "445710";
				} else {
					//Autoporte CLEODIS BE
					$compte1 = "411000";
					$compte2 = "706200";
					$compte3 = "706200";
					$compte4 = "445710";
				}

				//insertion des donnÃ©es
				for ($i = 1; $i <= 4; $i++) {
					$row_data=array();
					if($i==1){
						$row_data["A"]='G';
						$row_data["B"]=" ".$date;
						$row_data["C"]='VEN';
						$row_data["D"]=$compte1;

						if($item['facture.type_facture']=="refi"){
							$row_data["E"]=$refinanceur['code_refi']." ".$refinanceur["refinanceur"];
						}else{
							$row_data["E"]=$societe["code_client"];
						}

						if($item['facture.prix']<0){
							$row_data["F"]='C';
						}else{
							$row_data["F"]='D';
						}


						$row_data["G"]=round(abs($item['facture.prix']*$item['facture.tva']),2);

						$row_data["H"]=$libelle;
						$row_data["I"]=$reference;
						$row_data["K"]=$dateDebut;
						$row_data["L"]=$dateFin;
						$row_data["M"]=$datePrelevement;
						$row_data["N"]=$refinancement;
					}elseif($i==2){
						$row_data["A"]='G';
						$row_data["B"]=" ".$date;
						$row_data["C"]='VEN';
						$row_data["D"]=$compte2;

						if($item['facture.prix']<0){
							$row_data["F"]='D';
						}else{
							$row_data["F"]='C';
						}
						$row_data["G"]=abs($item['facture.prix']);
						$row_data["H"]=$libelle;
						$row_data["I"]=$reference;
						$row_data["K"]=$dateDebut;
						$row_data["L"]=$dateFin;
						$row_data["M"]=$datePrelevement;
						$row_data["N"] = $refinancement;
					}elseif($i==3){
						$row_data["A"]='A1';
						$row_data["B"]=" ".$date;
						$row_data["C"]='VEN';
						$row_data["D"]= $compte3;
						if($item['facture.prix']<0){
							$row_data["F"]='D';
						}else{
							$row_data["F"]='C';
						}
						$row_data["G"]=abs($item['facture.prix']);
						$row_data["H"]=$libelle;
						$row_data["I"]=$reference;

						if($affaire["nature"]=="avenant"){
							$row_data["J"]=" 20".substr($affaire["ref"],0,7).$societe["code_client"]."AV ";
						}else{
							$row_data["J"]=" 20".substr($affaire["ref"],0,7).$societe["code_client"]."00 ";
						}

						$row_data["K"]=$dateDebut;
						$row_data["L"]=$dateFin;
						$row_data["M"]=$datePrelevement;
						$row_data["N"]=$refinancement;

					}elseif($i==4){
						$row_data["A"]='G';
						$row_data["B"]=" ".$date;
						$row_data["C"]='VEN';
						$row_data["D"]=$compte4;
						$row_data["E"]='';
						$row_data["F"]='C';
						$row_data["G"]=abs(($item['facture.prix']*$item['facture.tva'])-$item['facture.prix']);
						$row_data["H"]=$libelle;
						$row_data["I"]=$reference;
						$row_data["K"]=$dateDebut;
						$row_data["L"]=$dateFin;
						$row_data["M"]=$datePrelevement;
						$row_data["N"]=$refinancement;
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




};
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

	/**
	* Renvoi toutes les factures equi ne sont pas payé et qui n'ont pas au moins 1 transaction SLIMPAY
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*
	*/
	public function aPrelever($infos){
		$q = "SELECT facture.*
		 	  FROM facture
			  WHERE `id_facture` NOT IN (SELECT id_facture FROM slimpay_transaction)
			  AND etat = 'impayee'
			  AND date_paiement IS NULL
			  ORDER BY `facture`.`id_societe`, `facture`.`id_affaire`";

		$return = ATF::db()->sql2array($q);

		foreach ($return as $key => $value) {
			$return[$key]["client"] = ATF::societe()->nom($value["id_societe"]);
			$return[$key]["date"] = date("d/m/Y" , strtotime($return[$key]["date"]));
			$return[$key]["date_periode_debut"] = date("d/m/Y" , strtotime($return[$key]["date_periode_debut"]));
			$return[$key]["date_periode_fin"] = date("d/m/Y" , strtotime($return[$key]["date_periode_fin"]));
			$return[$key]["prix_ttc"] = number_format(($value["prix"] * $value["tva"]), 2 , ".", "");
		}

		$libelle = "Abonnement BDOM+ ".ATF::$usr->trans(date("F", strtotime("+1 month")))." ".date("Y", strtotime("+1 month"));

		$result = array(
						"libelle"=> $libelle,
						"date_prelevement"=> date("Y-m-01", strtotime("+1 month")),
						"lignes" => $return
					   );

		return $result;
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

				if($data[$mandat_slimpay]["paymentReference"]){
					$data[$mandat_slimpay]["prix"] = number_format($data[$mandat_slimpay]["prix"] + ($f["prix"]*__TVA__),2 , ".", "");
					$data[$mandat_slimpay]["id_facture"][] = $key;

					$id_affaire = $this->getAffaireMere($f["id_affaire"]);
					$d = str_replace(ATF::affaire()->select($id_affaire, "ref"), "", $f["ref"]);

					$data[$mandat_slimpay]["paymentReference"] .= "/".$d;
				}else{
					$data[$mandat_slimpay]["prix"] = number_format(($f["prix"]*__TVA__),2 , ".", "");
					$data[$mandat_slimpay]["id_facture"][] = $key;
					$data[$mandat_slimpay]["paymentReference"] = $f["ref"];
				}
			}

			foreach ($data as $key => $value) {
				if(!$infos["libelle"]) $infos["libelle"] = $value["libelle"];
				$status = ATF::slimpay()->createDebit($key,$value["prix"],$infos["libelle"], $infos["date"],$value["paymentReference"]);

				log::logger($status , "mfleurquin");

				foreach ($value["id_facture"] as $kfacture => $vfacture) {
					ATF::slimpay_transaction()->i(array(
													"id_facture"=> $vfacture,
													"ref_slimpay" => $status["id"],
												    "executionStatus"=>$status["executionStatus"],
												    "date_execution"=>$status["executionDate"],
												    "retour"=> json_encode($status)
												   	)
											 	);
				}
			}
		}
		return true;
	}

	/**
	* Recupere le status SLIMPAY d'une demande de prélèvement et met à jour le status si celui ci à changé
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
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

					log::logger("Count ".(count($transaction)-1), "mfleurquin");
					log::logger($state , "mfleurquin");
					log::logger($transaction , "mfleurquin");

					//Si le state retourné par SLIMPAY est different de celui en BDD, on met à jour
					if($state["executionStatus"] != $transaction[0]["executionStatus"]){
						ATF::slimpay_transaction()->u(array("id_slimpay_transaction"=> $transaction[0]["id_slimpay_transaction"],
															"executionStatus"=>$state["executionStatus"],
															"retour"=>json_encode($state)
													  ));

						//Si le statut de la transaction est rejected, il faut allez rechercher la Transaction rejouée
						if($state["executionStatus"] === "rejected") {
							//un suivi sans destinataire "Facture xxxx impayée"
							$suivis = array("suivi"=> array(
													"id_societe" => $this->select($vfacture["facture.id_facture"] , "id_societe"),
													"type" => "note",
													"date" => date("Y-m-d H:i:s"),
													"texte" => "Facture ".$this->select($vfacture["facture.id_facture"] , "ref")." impayée",
													"id_affaire" => $this->select($vfacture["facture.id_facture"] , "id_affaire"),
													"type_suivi" => "Contrat",
													"no_redirect" => true,
													"suivi_notifie"=>array(116)
											  	)
											);

							ATF::suivi()->insert($suivis);

						}
					}




					if($state["replayCount"] == 0) log::logger("Transaction Initiale" , "mfleurquin");
					if($state["replayCount"] == 1) log::logger("Transaction rejouée 1 fois" , "mfleurquin");
					if($state["replayCount"] == 2) log::logger("Transaction rejouée 2 fois" , "mfleurquin");
				}

			}
		}



		/*$this->q->reset()->whereIsNotNull("id_slimpay");

		if($factures = $this->select_all()){
			foreach ($factures as $key => $value) {

				$facture = $this->select($value["facture.id_facture"]);

				$status = ATF::slimpay()->getStatutDebit($facture["id_slimpay"]);


				log::logger("Paiement : ".$facture["id_slimpay"]."  ---> " , "StatutDebitSlimpay");
				log::logger($status , "StatutDebitSlimpay");

				if($facture["executionStatus"] !== $status["executionStatus"]){
					$this->u(array("id_facture"=>$facture["id_facture"],
								   "executionStatus"=>$status["executionStatus"]
								  )
							);

					if($status["executionStatus"] === "processed") {
						$this->u(array("id_facture"=>$facture["id_facture"],
										"etat"=> "payee",
										"date_paiement"=>date("Y-m-d", strtotime($status["executionDate"]))
									));
					}

					if($status["executionStatus"] === "rejected") {
						$this->u(array("id_facture"=>$facture["id_facture"],
										// "rejet"=>"non_preleve",
										"date_rejet"=>date("Y-m-d")
									));
					}

					if($status["executionStatus"] === "contested") {
						$this->u(array("id_facture"=>$facture["id_facture"],
										"rejet"=>"contestation_debiteur",
										"date_rejet"=>date("Y-m-d", strtotime($status["executionDate"])),
										"etat"=>"impayee",
										"date_paiement"=> NULL
									));
					}

					if($status["executionStatus"] === "rejected") {
						//un suivi sans destinataire "Facture xxxx impayée"
						$suivis = array("suivi"=> array(
												"id_societe" => $this->select($facture["id_facture"] , "id_societe"),
												"type" => "note",
												"date" => date("Y-m-d H:i:s"),
												"texte" => "Facture ".$this->select($facture["id_facture"] , "ref")." impayée",
												"id_affaire" => $this->select($facture["id_facture"] , "id_affaire"),
												"type_suivi" => "Contrat",
												"no_redirect" => true,
												"suivi_notifie"=>array(18,26)
										  	)
										);

						ATF::suivi()->insert($suivis);

					}else{
						//si le nouveau statut est différent de rejected, on crée une tâche à destination de Benjamin Tronquit et Estelle Tampigny "Changement de statut de la facture XXXX. Merci de vérifier".
						//Ne pas créer de tache si la facture passe en processed
						if($status["executionStatus"] !== "processed"){
							$tache = array("tache"=>array(
										   "id_societe"=> $this->select($facture["id_facture"] , "id_societe"),
	                                       "tache"=>"Changement de statut de la facture ".$this->select($facture["id_facture"] , "ref").". Merci de vérifier",
	                                       "id_affaire"=>$this->select($facture["id_facture"] , "id_affaire"),
	                                       "type_tache"=>"note",
	                                       "horaire_fin"=>date('Y-m-d h:i:s', strtotime('+3 day')),
	                                       "no_redirect"=>"true"
	                                     ),
				                        "dest"=>array(18,26)
	                    			);
        					$id_tache = ATF::tache()->insert($tache);
						}

					}

				}

			}

		}*/

	}

};
class facture_bdom extends facture_cleodis { };
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