<?
/** Classe facture
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../facture.class.php";
class facture_lm extends facture {
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
		);

		// Panel principal
		$this->colonnes['primary'] = array(
			"id_societe"=>array("disabled"=>true)
			,"id_affaire"=>array("disabled"=>true)
			,"id_commande"=>array("disabled"=>true)
			,"type_facture"
			,"type_libre"=>array("disabled"=>true)
			,"redevance"=>array("disabled"=>true)
			,"mode_paiement"
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
			"prix"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);

		$this->colonnes['panel']['dates_facture_libre'] = array(
			"date_periode_debut_libre"=>array("custom"=>true,"xtype"=>"datefield"),
			"date_periode_fin_libre"=>array("custom"=>true,"xtype"=>"datefield"),
			"prix_libre"=>array("custom"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
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

		$this->colonnes['panel']['slimpay'] = array(
			"id_slimpay",
			"executionStatus",
			"executionDate"
		);

		// Propriété des panels
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>3);
		$this->panels['refi'] = array("visible"=>true,'nbCols'=>3,"hidden"=>true);
		$this->panels['dates_facture'] = array("visible"=>true,'nbCols'=>3);
		$this->panels['dates_facture_libre'] = array("visible"=>true,'nbCols'=>3,"hidden"=>true);
		$this->panels['lignes_repris'] = array('nbCols'=>1);
		$this->panels['lignes'] = array('nbCols'=>1);
		$this->panels['lignes_non_visible'] = array('nbCols'=>1);
		$this->panels['loyer_lignes'] = array('nbCols'=>1);
		$this->panels['courriel'] = array('nbCols'=>2,"checkboxToggle"=>true);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] = array('ref','tva','etat','date_paiement','date_relance','id_user','envoye_mail','rejet',"id_slimpay","executionStatus","executionDate");
		$this->fieldstructure();

		$this->onglets = array('facture_ligne');
		//$this->no_insert = true;
		//$this->no_update = true;
		$this->addPrivilege("majMail","update");
		$this->addPrivilege("export_special");
		$this->addPrivilege("export_special2");
		$this->addPrivilege("export_autoportes");
		$this->addPrivilege("updateEnumRejet");
		$this->addPrivilege("getAllForRelance");
		$this->addPrivilege("libreToNormale");
		$this->addPrivilege("export_cegid");
		$this->addPrivilege("export_GL_LM");

		$this->addPrivilege("aPrelever");
		$this->addPrivilege("massPrelevementSlimpay");




		$this->field_nom="ref";
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true);
		$this->selectAllExtjs=true;
	}

	/**
	* Renvoi toutes les factures en attente de prélèvement
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*
	*/
	public function aPrelever($infos){
		$this->q->reset()->whereIsNull("date_paiement","AND")->whereIsNull("id_slimpay","AND");
		$return = $this->sa();
		foreach ($return as $key => $value) {
			$return[$key]["client"] = ATF::societe()->nom($value["id_societe"]);
			$return[$key]["date"] = date("d/m/Y" , strtotime($return[$key]["date"]));
			$return[$key]["date_periode_debut"] = date("d/m/Y" , strtotime($return[$key]["date_periode_debut"]));
			$return[$key]["date_periode_fin"] = date("d/m/Y" , strtotime($return[$key]["date_periode_fin"]));
		}
		return $return;
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
					$data[$mandat_slimpay]["prix"] += $f["prix"];
					$data[$mandat_slimpay]["id_facture"][] = $key;

					$id_affaire = $this->getAffaireMere($f["id_affaire"]);
					$d = str_replace(ATF::affaire()->select($id_affaire, "ref"), "", $f["ref"]);

					$data[$mandat_slimpay]["paymentReference"] .= "/".$d;
				}else{
					$data[$mandat_slimpay]["prix"] = $f["prix"];
					$data[$mandat_slimpay]["id_facture"][] = $key;
					$data[$mandat_slimpay]["paymentReference"] = $f["ref"];
				}
			}

			foreach ($data as $key => $value) {
				if(!$infos["libelle"]) $infos["libelle"] = $value["libelle"];
				$status = ATF::slimpay()->createDebit($key,$value["prix"],$infos["libelle"], $infos["date"],$value["paymentReference"]);
				foreach ($value["id_facture"] as $kfacture => $vfacture) {
					$this->u(array("id_facture"=>$vfacture,
								   "id_slimpay"=>$status["id"],
								   "executionStatus"=>$status["executionStatus"],
								   "executionDate"=>$status["executionDate"],
								  )
							);
				}
			}
		}
		return true;
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
	* Recupere le status SLIMPAY d'une demande de prélèvement et met à jour le status si celui ci à changé
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*
	*/
	public function statusDebitEnCours(){
		$this->q->reset()->whereIsNotNull("id_slimpay","AND")
						 ->where("executionStatus","processed","AND",false,"!=");

		if($factures = $this->select_all()){
			foreach ($factures as $key => $value) {

				$facture = $this->select($value["facture.id_facture"]);
				$status = ATF::slimpay()->getStatutDebit($facture["id_slimpay"]);

				log::logger("Paiement : ".$facture["id_slimpay"]."  ---> " , "StatutDebitSlimpay");
				log::logger($status , "StatutDebitSlimpay");

				if($facture["executionStatus"] !== $status["executionStatus"] || $status["executionStatus"] != "processed" ){
					$this->u(array("id_facture"=>$facture["id_facture"],
								   "executionStatus"=>$status["executionStatus"]
								  )
							);
					if($status["executionStatus"] === "processed") {
						$this->u(array("id_facture"=>$facture["id_facture"],
										"status"=> "payee",
										"date_paiement"=>date("Y-m-d", strtotime($status["executionDate"]))
									));
					}

					if($status["executionStatus"] === "rejected") {
						$this->u(array("id_facture"=>$facture["id_facture"],
										"rejet"=>"non_preleve",
										"date_rejet"=>date("Y-m-d", strtotime($status["executionDate"]))
									));
					}

					if($status["executionStatus"] === "contested") {
						$this->u(array("id_facture"=>$facture["id_facture"],
										"rejet"=>"contestation_debiteur",
										"date_rejet"=>date("Y-m-d", strtotime($status["executionDate"]))
									));
					}
				}
			}

		}

	}

	/**
	* Retourne le mandat SLIMPAY d'une affaire passée en parametre
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*
	*/
	public function getMandatSlimpay($id_affaire){
		if($mandatSlimpay = ATF::affaire()->select($id_affaire , "ref_mandate")){
			return $mandatSlimpay;
		}else{
			if($id_parent = ATF::affaire()->select($id_affaire , "id_parent")){
				return $this->getMandatSlimpay($id_parent);
			}else{
				throw new errorATF("Error Processing Request", 1);

			}
		}
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
			case "nature":
				if ($facture) {
					$periode = ATF::facturation()->periode_facturation($facture['id_affaire'],true);
					return $periode["nature"];
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
				$commande = new commande_lm($commande['id_commande']);
				$affaire = $commande->getAffaire();
				$affaire_parente = $affaire->getParentAvenant();
				ATF::parc()->updateExistenz($commande,$affaire,$affaire_parente);
			}


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

		//Ligne pour crontab batch_facture -- MAJ TVA à 19.6%
		if($infos["batch"]){ $batch = true; unset($infos["batch"]); $ref = $infos["ref"];}

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

		if($infos["type_facture"]=="service_complementaire" || $infos["type_facture"]=="installation"){
			unset($infos["date_periode_debut"],$infos["date_periode_fin"]);
		}elseif($infos["type_facture"]=="libre"){
			$infos["prix"]=$infos["prix_libre"];
			$infos["date_periode_debut"]=$infos["date_periode_debut_libre"];
			$infos["date_periode_fin"]=$infos["date_periode_fin_libre"];
			if($infos["type_libre"] !== "normale" ){
				$infos["tva"]=1;
			}
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

		if(!$batch){
			if($infos["tva"] == "1.196"){
				$infos["tva"]= "1.2";
			}
		}else{ $infos["tva"] = $infos["batchtva"]; $infos["ref"] = $ref; unset($infos["batchtva"]); }


		//Si on a une TVA 19.6 et qu'on est en 2014 ---> TVA passe à 20% !!
		/*if($infos["type_facture"]=="refi"){
			if((date("Y" , strtotime($infos["date"])) >= 2014) || (date("Y" , strtotime($infos["date_previsionnelle"])) >= 2014)){
				$infos["tva"]= "1.2";
			}
		}else{
			$infos["tva"]= "1.2";
			/*if($infos["tva"]== "1.196" && date("Y" , strtotime($infos["date_periode_debut"])) >= 2014){
				$infos["tva"]= "1.2";
			}
		}*/

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
			$commande = new commande_lm($commande['id_commande']);
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
				//log::logger(strtotime($item['facture.date_periode_debut']),'amaitre');
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
					}elseif($refinanceur['refinanceur']=='lm' || !$refinanceur && $item['facture.date_periode_debut']){
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
					if($i==1){
						$row_data["A"]='G';
						$row_data["B"]=" ".$date;
						$row_data["C"]='VEN';
						if($refinanceur['refinanceur']=='BMF'){
							$row_data["D"]="467000";
							//          $row_data["E"]=$refinanceur['code_refi'];
							$row_data["E"]="B".substr($societe["code_client"],1);
						}elseif($item['facture.type_facture']=="refi"){
							$row_data["D"]="411000";
							$row_data["E"]=$refinanceur['code_refi']." ".$refinanceur["refinanceur"];
						}else{
							$row_data["D"]="411000";
							//          $row_data["E"]=$refinanceur['code_refi'];
							$row_data["E"]=$societe["code_client"];
						}
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
						$row_data["K"]=$dateDebut;
						$row_data["L"]=$dateFin;
						$row_data["M"]=$datePrelevement;
						$row_data["N"] = $refinancement;
					}elseif($i==2){
						$row_data["A"]='G';
						$row_data["B"]=" ".$date;
						$row_data["C"]='VEN';

						if($item['facture.prix']<0){
							$row_data["F"]='D';
						}else{
							$row_data["F"]='C';
						}
						$row_data["G"]=abs($item['facture.prix']);
						if($devis[0][ "type_contrat"] == "presta"){	$row_data["D"]=='706240'; }
						else{
							if($refinanceur['refinanceur']=='BMF'){
								if($infos["rejet"]){
							 		$row_data["D"]=='771000';
								}else{
									$row_data["D"]="706500";
								}
								//         $row_data["E"]="B".substr($societe["code_client"],1);
							}elseif($refinanceur['refinanceur']=='CLEOFI'){

								$infos_commande=ATF::commande()->select(ATF::facture()->select($item['facture.id_facture_fk'] , "id_commande"));
								if($infos_commande['date_evolution'] > $item['facture.date_periode_debut']){
									if($infos["rejet"]){
								 		$row_data["G"]=abs(($item['facture.prix']*$item['facture.tva']));
									}else{
									    if(date("y",strtotime($item['facture.date_periode_debut'])) >= 14 && $devis[0]["tva"]==1.196){ $row_data["G"]=round(abs($item['facture.prix']*1.2),2); }
									    else{ $row_data["G"]=round(abs($item['facture.prix']*$devis[0]["tva"]),2); }
									}
									$row_data["D"]="467500";
								}else{
									$row_data["G"]=abs($item['facture.prix']);
									$row_data["D"]="706230";
								}
								//         $row_data["E"]=$societe["code_client"];
							}else{
								$row_data["D"]=$compte_2;
								//         $row_data["E"]=$societe["code_client"];
							}
						}


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
						if($affaire["nature"]=="avenant"){
							//Faire en sorte que l1296 = 2008 et non pas 208
							$row_data["J"]="20".substr($affaire["ref"],0,7).$societe["code_client"]."AV";
						}else{
							$row_data["J"]="20".substr($affaire["ref"],0,7).$societe["code_client"]."00";
						}
						if($devis[0][ "type_contrat"] == "presta"){	$row_data["D"]=='706240'; }
						else{
							if($refinanceur['refinanceur']!='CLEOFI'){
								if($refinanceur['refinanceur']=='BMF'){
									if($infos["rejet"]){
							 			$row_data["D"]=='771000';
									}else{
										$row_data["D"]="706500";
									}
									//             $row_data["E"]="B".substr($societe["code_client"],1);
								}else{
									$row_data["D"]= $compte_2;;
									//             $row_data["E"]=$societe["code_client"];
								}
							}elseif($refinanceur['refinanceur']=='CLEOFI'){
								$infos_commande=ATF::commande()->select(ATF::facture()->select($item['facture.id_facture_fk'] , "id_commande"));
								//Si prolongation
								if($infos_commande['date_evolution'] < $item['facture.date_periode_debut']){
									$row_data["D"]='706230';
								}else{
									$row_data["D"]= $compte_2;
								}
							}
						}

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
					}elseif($i==4){
						if($refinanceur['refinanceur']!='CLEOFI'){

							$row_data["A"]='G';
							$row_data["B"]=" ".$date;
							$row_data["C"]='VEN';
							if($refinanceur['refinanceur']=='BMF'){
								$row_data["D"]="445712";
								//             $row_data["E"]="B".substr($societe["code_client"],1);
							}else{
								$row_data["D"]=$compte_3;
								//             $row_data["E"]=$societe["code_client"];
							}
							if($item['facture.prix']<0){
								$row_data["F"]='D';
							}else{
								$row_data["F"]='C';
							}

							if($infos["rejet"]){
						 		$row_data["G"]=abs(($item['facture.prix']*$item['facture.tva']-$item['facture.prix']));
							}else{
								if(date("y",strtotime($item['facture.date_periode_debut'])) >= 14 && $devis[0]["tva"]==1.196){$row_data["G"]=abs(($item['facture.prix']*1.2-$item['facture.prix']));
								}else{$row_data["G"]=abs(($item['facture.prix']*$devis[0]["tva"]-$item['facture.prix'])); }

							}
							$row_data["H"]=$libelle;
							$row_data["I"]=$reference;
							$row_data["K"]=$dateDebut;
							$row_data["L"]=$dateFin;
							$row_data["M"]=$datePrelevement;
							$row_data["N"] = $refinancement;
						}elseif($refinanceur['refinanceur']=='CLEOFI'){

							$infos_commande=ATF::commande()->select(ATF::facture()->select($item['facture.id_facture_fk'] , "id_commande"));
							//Si prolongation
							if($infos_commande['date_evolution'] < $item['facture.date_periode_debut']){
								$row_data["A"]='G';
								$row_data["B"]=" ".$date;
								$row_data["C"]='VEN';
								$row_data["D"]='445713';
								$row_data["E"]='';
								$row_data["F"]='C';
								$row_data["G"]=abs(($item['facture.prix']*$item['facture.tva'])-$item['facture.prix']);
								$row_data["H"]=$libelle;
								$row_data["I"]=$reference;
								$row_data["K"]=$dateDebut;
								$row_data["L"]=$dateFin;
								$row_data["M"]=$datePrelevement;
								$row_data["N"] = $refinancement;
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


	/** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	 public function export_autoportes(&$infos){
	 	$infos["display"] = true;

        require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());



        ATF::affaire()->q->reset()->where("affaire.etat","commande","OR","sous_req_affaire","=")
        				 		  ->where("affaire.etat","facture","OR","sous_req_affaire","=")
        				 		  ->from("affaire","id_affaire","commande","id_affaire")
        				 		  ->whereIsNotNull("commande.date_debut");
        $donnees = ATF::affaire()->sa();

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		$feuilles[]["titre"] = "Engagement";
		$feuilles[]["titre"] = "Prolongation";
		foreach ($feuilles as $key => $value) {
			if($key > 0) $objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($key);
			$objPHPExcel->getActiveSheet()->setTitle($value["titre"]);
			$this->ajoutTitreAutoporte($objPHPExcel);
			if($value["titre"] == "Engagement"){
				$this->ajoutDonneesAutoportes($objPHPExcel,$donnees,false);
			}else{
				$this->ajoutDonneesAutoportes($objPHPExcel,$donnees,true);
			}

		}

		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="export_autoportes.xls"');
		header("Cache-Control: private");
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');


	}

	/** Mise en place des titres
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
    public function ajoutTitreAutoporte(&$objPHPExcel){
        $row_data = array(
        	array('X',5)
			,array('CLIENT',30)
			,array('AFFAIRE',80)
			,array('DATE DEBUT',15)
			,array('PERIODE',15)
			,array('JOUR',15)
			,array('DUREE',15)
			,array('LOYER HT',15)
			,array('TOTAL TTC CONTRAT',15)
			,array("PRESTATAIRE/FOURNISSEUR",20)
			,array('ACHAT HT',15)
			,array('ACHAT TTC',15)
		);


		for($an=2016; $an<=2030; $an++){
		 	for($mois=1;$mois<=12; $mois++){
		 		if($mois <10){ $mois = "0".$mois;}
		 		$date = $an."-".$mois."-"."01";
				$stamp = strtotime($date);
				$date = date("M-y", $stamp);
			    $row_data[] = array($date,10);
		 	}
		}
		//A =65 Z=90
		$lettre2 = 64;
		$lettre1 = 64;
		//DEPART
        foreach($row_data as $col=>$titre){
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


        	$objPHPExcel->getActiveSheet()->setCellValue($char.'1', $titre[0]);
        	$objPHPExcel->getActiveSheet()->getColumnDimension($char)->setWidth($titre[1]);

        }
     }

	 /** Mise en place du contenu
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $sheets : contient les 5 onglets
     * @param array $infos : contient tous les enregistrements
	 * @param boolean $refinance TRUE pour sortir toutes les affaires
     */
     public function ajoutDonneesAutoportes(&$objPHPExcel,$infos,$prolongation=false){
        $row_auto=1;
		$InOneMonth = date('Y-m-01',strtotime("+1 month"));
		$InOneMonth = explode("-", $InOneMonth);
		$InOneMonth =  $InOneMonth["0"].$InOneMonth["1"].$InOneMonth["2"];
		foreach ($infos as $key => $item) {
			$row_data = array();
			$increment++;
			if($item){
				$commande = ATF::commande()->select($item["id_commande"]);
				$afficher = false;

				if($prolongation == true && ($commande["etat"] == "prolongation" || $commande["etat"] == "prolongation_contentieux")){
					$afficher = true;
				}elseif($prolongation == false && ($commande["etat"] !== "prolongation" && $commande["etat"] !== "prolongation_contentieux")){
					$afficher = true;
				}

				if($afficher){
					$row_auto++;

					$loyers = array();
					ATF::loyer()->q->reset()->where("id_affaire",$item["id_affaire"]);
					$loyers = ATF::loyer()->select_all();

					$periode = $jour_edition = $duree = $loyerHT = $loyerTTC = "";

					// Récupération des loyers de l'affaire
					foreach ($loyers as $kl => $vl) {
						if($kl == 0){
							$periode .= $vl["frequence_loyer"];
							$jour_edition .= "01";
							$duree .= $vl["duree"];
							$loyerHT .= number_format($vl["loyer"] - ($vl["loyer"]*0.2),2);
							$loyerTTC .= $vl["loyer"];
						}else{
							$periode .= "\n".$vl["frequence_loyer"];
							$jour_edition .= "\n"."01";
							$duree .= "\n".$vl["duree"];
							$loyerHT .= "\n".number_format($vl["loyer"] - ($vl["loyer"]*0.2),2);
							$loyerTTC .= "\n".$vl["loyer"];
						}
					}

					//Récuperation des prix d'achat de l'affaire
					$departement = ATF::db()->escape_string(substr(ATF::affaire()->select($item["id_affaire"],"cp_adresse_livraison"),0,2));
					ATF::commande_ligne()->q->reset()->where("id_commande", $item["id_commande"]);
					$lignes = ATF::commande_ligne()->sa();



					$prix_achat = 0;

					foreach ($lignes as $kl => $vl) {

						ATF::produit_fournisseur()->q->reset()->andWhere("id_fournisseur",$vl["id_fournisseur"])
															  ->andWhere("recurrence","achat")
															  ->andWhere("id_produit",$vl["id_produit"])
															  ->andWhere('departement','(^|,)'.$departement.'($|,)','dep','REGEXP')
															  ->whereIsNull('departement','OR','dep');
						$achat = ATF::produit_fournisseur()->select_row();


						if($achat){
							$prix_achat += $vl["quantite"] * $achat["prix_prestation"];
						}
					}



			        $row_data[]  = '';
					$row_data[]  = ATF::societe()->nom($item["id_societe"]);
					$row_data[]  = $item["ref"]." - ".$item["affaire"];
					$row_data[]  = date("d/m/Y", strtotime($item["date_debut"]));
					$row_data[]  = $periode;
					$row_data[]  = $jour_edition;
					$row_data[]  = $duree;
					$row_data[]  = $loyerHT;
					$row_data[]  = $loyerTTC;
					$row_data[]  = '';
					$row_data[]  = $prix_achat;
					$row_data[]  = $prix_achat*1.20;


					//A =65 Z=90
					$lettre2 = 64;
					$lettre1 = 64;
					//DEPART
			        foreach($row_data as $k=>$v){
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

						$objPHPExcel->getActiveSheet()->setCellValue($char.$row_auto, $v);
						$objPHPExcel->getActiveSheet()->getStyle($char.$row_auto)->getAlignment()->setWrapText(true);
	        		}

	        		ATF::facturation()->q->reset()->where("facturation.id_affaire", $item["id_affaire"])
												  ->where("facturation.date_periode_debut", $item["date_debut"], "AND", false, ">=")
												  ->addOrder("date_periode_debut", "asc");
					$echeancier = ATF::facturation()->select_all();




					$fact = 0;
					$jour = explode("-", $echeancier[$fact]["date_periode_debut"]);

					for($an=2016; $an<=2030; $an++){
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
							$date = $date."-".$jour[2];
							$dateCol = new DateTime($date);
							$dateDeb = new DateTime($echeancier[$fact]["date_periode_debut"]);
							$dateFin = new DateTime($echeancier[$fact]["date_periode_fin"]);

							if(($dateCol->getTimestamp()  == $dateDeb->getTimestamp())){

								if($echeancier[$fact]["montant"] || ($echeancier[$fact]["montant"] == $loyer["loyer"])){
									$objPHPExcel->getActiveSheet()->setCellValue($char.$row_auto, $echeancier[$fact]["montant"]);

									//Engagement -> vert
									//Prolongation probable -> orange
									//Prolongation -> rouge
									$color = '32cd32';
									if($echeancier[$fact]["nature"] == "prolongation_probable"){
										$color = 'ffa500';
									}elseif ($echeancier[$fact]["nature"] == "prolongation") {
										$color = 'FF0000';
									}

									$objPHPExcel->getActiveSheet()->getStyle($char.$row_auto)->applyFromArray(
									    array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => $color)
									        )
									    )
									);
								}
					    		$fact++;
							}

					 	}
					}

					$fournisseurs = array();

					ATF::facturation_fournisseur()->q->reset()->where("id_affaire", $item["id_affaire"]);
					$facturations = ATF::facturation_fournisseur()->sa();

					if($facturations){
						foreach ($facturations as $kff => $vff) {
							$fournisseurs[$vff["id_fournisseur"]][]= $vff;
						}
					}

					if($fournisseurs){
						foreach ($fournisseurs as $kfournisseur => $vfournisseur) {
							$row_data = array();
							$row_auto++;

							$row_data[]  = '';
							$row_data[]  = '';
							$row_data[]  = "";
							$row_data[]  = "";
							$row_data[]  = "";
							$row_data[]  = "";
							$row_data[]  = "";
							$row_data[]  = "";
							$row_data[]  = "";
							$row_data[]  = ATF::societe()->select($kfournisseur , "societe");
							$row_data[]  = "";
							$row_data[]  = "";
							$row_data[]  = "";

							//A =65 Z=90
							$lettre2 = 64;
							$lettre1 = 64;
							//DEPART
					        foreach($row_data as $k=>$v){
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

								$objPHPExcel->getActiveSheet()->setCellValue($char.$row_auto, $v);
								$objPHPExcel->getActiveSheet()->getStyle($char.$row_auto)->getAlignment()->setWrapText(true);
			        		}

			        		$fact = 0;
							$jour = explode("-", $vfournisseur[$fact]["date_periode_debut"]);

							for($an=2016; $an<=2030; $an++){
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
									$date = $date."-".$jour[2];
									$dateCol = new DateTime($date);
									$dateDeb = new DateTime($vfournisseur[$fact]["date_periode_debut"]);

									if(($dateCol->getTimestamp()  == $dateDeb->getTimestamp())){

										if($vfournisseur[$fact]["montant"] || ($vfournisseur[$fact]["montant"] == $loyer["loyer"])){
											$objPHPExcel->getActiveSheet()->setCellValue($char.$row_auto, $vfournisseur[$fact]["montant"]);
										}
							    		$fact++;
									}

							 	}
							}

						}
					}
				}
			}
		}

		$ArrayCol = array(array("Engagement","32cd32"), array("Prolongation probable","ffa500"), array("Prolongation","FF0000"));
		$row_auto = $row_auto+2;
		foreach ($ArrayCol as $kcol => $vcol) {

			$row_auto ++;

			$objPHPExcel->getActiveSheet()->getStyle("A".$row_auto)->applyFromArray(
								    array(
								        'fill' => array(
								            'type' => PHPExcel_Style_Fill::FILL_SOLID,
								            'color' => array('rgb' => $vcol["1"])
								        )
								    )
								);
			$objPHPExcel->getActiveSheet()->setCellValue("B".$row_auto, $vcol[0]);
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


	public function export_GL_LM(&$infos){
		$infos["display"] = true;

		$this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel

        $this->q->addAllFields($this->table)->setLimit(-1)->unsetCount();
        $data = $this->sa();

        $string = "";
        $total_debit = 0;
        $total_credit = 0;
        $lignes = 0;

        $donnees = array();

        foreach ($data as $key => $value) {
        	$code_magasin = "M0380"; //Par defaut web

        	$rayon =  $legaccHT = NULL;

        	if(ATF::affaire()->select($value["facture.id_affaire_fk"] , "type_souscription") == "magasin" && ATF::affaire()->select($value["facture.id_affaire_fk"] , "id_magasin")){
        		$code_magasin = ATF::magasin(ATF::affaire()->select($value["facture.id_affaire_fk"] , "id_magasin"), "entite_lm");
        	}

        	//On recupere le 1er produit de la facture pour connaitre le pack et donc le rayon
        	ATF::facture_ligne()->q->reset()->where("id_facture",$value["facture.id_facture_fk"])
        									->setLimit(1);
        	$ligne = ATF::facture_ligne()->select_row();

        	$pack = ATF::pack_produit()->select(ATF::produit()->select($ligne["id_produit"] , "id_pack_produit"));

        	switch ($pack["type_pack_magasin"]) {
        		case 'alarme':
        			if($value["facture.type_facture"] == "facture" || $value["facture.type_facture"] == "libre") $legaccHT = "706200";
        			//if($value["facture.type_facture"] == "installation") $legaccHT = "706200";
        			if($value["facture.type_facture"] == "service_complementaire") $legaccHT = "706203";
        		break;
        		case 'chaudiere':
        			if($value["facture.type_facture"] == "facture" || $value["facture.type_facture"] == "libre") $legaccHT = "706200";
        			//if($value["facture.type_facture"] == "installation") $legaccHT = "706201";
        			if($value["facture.type_facture"] == "service_complementaire") $legaccHT = "706203";

        		break;
        		case 'adoucisseur':
        			if($value["facture.type_facture"] == "facture" || $value["facture.type_facture"] == "libre") $legaccHT = "706200";
        			//if($value["facture.type_facture"] == "installation") $legaccHT = "706203";
        			if($value["facture.type_facture"] == "service_complementaire") $legaccHT = "706203";
        		break;
        	}

        	$rayon = ATF::rayon()->select($pack["id_rayon"] , "centre_cout_profit");

        	$ref_societe = ATF::societe()->select($value["facture.id_societe_fk"] , "ref");

        	$lettrage_date_facture = $this->getMoisFrancais(date("m", strtotime($value["facture.date_periode_debut"])))." - ".date("Y", strtotime($value["facture.date_periode_debut"]));

        	for($i=1;$i<4;$i++){
	        	if($i==1){
	        		//TTC
	        		$donnees[$key][$i][1] = "1";
		        	$donnees[$key][$i][2] = "1"; //Code pays a donner par LM
		        	$donnees[$key][$i][3] = "54";
		        	$donnees[$key][$i][4] = $code_magasin;
		        	$donnees[$key][$i][5] = 120; //Code BU a donner par LM
		        	$donnees[$key][$i][6] = "CLEODIS";
		        	$donnees[$key][$i][7] = "10"; //Type information a donner par LM
		        	$donnees[$key][$i][8] = date("Ymd", strtotime($value["facture.date"]));
		        	$donnees[$key][$i][9] = "1";
		        	$donnees[$key][$i][10] = "EUR";
		        	$donnees[$key][$i][11] = date("Ymd", strtotime($value["facture.date"]));
		        	$donnees[$key][$i][12] = "00000"; //Rayon du pack
		        	$donnees[$key][$i][13] = "1";
	        		$donnees[$key][$i][14] = "411101"; //Compte Comptable
	        		$donnees[$key][$i][15] = "000000000"; //Code projet
		        	$donnees[$key][$i][16] = "0";
		        	$donnees[$key][$i][17] = "0";
		        	$donnees[$key][$i][18] = "0";
		        	$donnees[$key][$i][19] = "0";
					$donnees[$key][$i][20] = $value["facture.prix"]; //Montant Debit
					$donnees[$key][$i][21] = "0"; //Montant Credit
		        	$donnees[$key][$i][22] = "0";
					$donnees[$key][$i][23] = "FACTURE ".$value["facture.id_facture"]." / ".$ref_societe." / ".$lettrage_date_facture; //reference affaire/facture/periode
					$donnees[$key][$i][24] = date("Ymd", strtotime($value["facture.date"]));
					$donnees[$key][$i][25] = "";
					$donnees[$key][$i][26] = "";
					$donnees[$key][$i][27] = "";
					$donnees[$key][$i][28] = "";
					$donnees[$key][$i][29] = "";
					$donnees[$key][$i][30] = "";
					$donnees[$key][$i][31] = "";
					$donnees[$key][$i][32] = "";
					$donnees[$key][$i][33] = $ref_societe;
					$donnees[$key][$i][34] = "";
					$donnees[$key][$i][35] = "";
					$donnees[$key][$i][36] = "";

					$total_debit += $value["facture.prix"];

	        	}elseif($i==2){
	        		//HT
	        		$donnees[$key][$i][1] = "1";
		        	$donnees[$key][$i][2] = "1"; //Code pays a donner par LM
		        	$donnees[$key][$i][3] = "54";
		        	$donnees[$key][$i][4] = $code_magasin;
		        	$donnees[$key][$i][5] = 120; //Code BU a donner par LM
		        	$donnees[$key][$i][6] = "CLEODIS";
		        	$donnees[$key][$i][7] = "10";; //Type information a donner par LM
		        	$donnees[$key][$i][8] = date("Ymd", strtotime($value["facture.date"]));
		        	$donnees[$key][$i][9] = "1";
		        	$donnees[$key][$i][10] = "EUR";
		        	$donnees[$key][$i][11] = date("Ymd", strtotime($value["facture.date"]));
		        	$donnees[$key][$i][12] = $rayon; //Centre de cout/profit a donner LM
		        	$donnees[$key][$i][13] = "1";
	        		$donnees[$key][$i][14] = $legaccHT;
	        		$donnees[$key][$i][15] = "000000000"; //Code projet
		        	$donnees[$key][$i][16] = "0";
		        	$donnees[$key][$i][17] = "0";
		        	$donnees[$key][$i][18] = "0";
		        	$donnees[$key][$i][19] = "0";
					$donnees[$key][$i][20] = "0"; //Montant Debit
					$donnees[$key][$i][21] = number_format($value["facture.prix"]/$value["facture.tva"] ,2); //Montant Credit
		        	$donnees[$key][$i][22] = "0";
					$donnees[$key][$i][23] = "FACTURE ".$value["facture.id_facture"]." / ".$ref_societe." / ".$lettrage_date_facture;//reference affaire/facture/periode
					$donnees[$key][$i][24] = date("Ymd", strtotime($value["facture.date"]));

					$total_credit += $value["facture.prix"]*($value["facture.tva"]-1);

	        	}elseif($i==3){
	        		//TVA
	        		$donnees[$key][$i][1] = "1";
		        	$donnees[$key][$i][2] = "1"; //Code pays a donner par LM
		        	$donnees[$key][$i][3] = "54";
		        	$donnees[$key][$i][4] = $code_magasin;
		        	$donnees[$key][$i][5] = 120; //Code BU a donner par LM
		        	$donnees[$key][$i][6] = "CLEODIS";
		        	$donnees[$key][$i][7] = "10";; //Type information a donner par LM
		        	$donnees[$key][$i][8] = date("Ymd", strtotime($value["facture.date"]));
		        	$donnees[$key][$i][9] = "1";
		        	$donnees[$key][$i][10] = "EUR";
		        	$donnees[$key][$i][11] = date("Ymd", strtotime($value["facture.date"]));
		        	$donnees[$key][$i][12] = "00000"; //Centre de cout/profit
		        	$donnees[$key][$i][13] = "1";
	        		$donnees[$key][$i][14] = "445733";
	        		$donnees[$key][$i][15] = "000000000"; //Code projet
		        	$donnees[$key][$i][16] = "0";
		        	$donnees[$key][$i][17] = "0";
		        	$donnees[$key][$i][18] = "0";
		        	$donnees[$key][$i][19] = "0";
					$donnees[$key][$i][20] = "0"; //Montant Debit
					$donnees[$key][$i][21] = number_format($value["facture.prix"] - ($value["facture.prix"]/$value["facture.tva"]) ,2); //Montant Credit
		        	$donnees[$key][$i][22] = "0";
					$donnees[$key][$i][23] =  "FACTURE ".$value["facture.id_facture"]." / ".$ref_societe." / ".$lettrage_date_facture; //reference affaire/facture/periode
					$donnees[$key][$i][24] = date("Ymd", strtotime($value["facture.date"]));

					$total_credit += ($value["facture.prix"] - ($value["facture.prix"]*($value["facture.tva"]-1)));
	        	}
        	}
        }

        $sequence = ATF::constante()->getSequence("__SEQUENCE_GL__");
        $filename = 'CLEODIS_VT'.$sequence.'.fic';

        header('Content-Type: application/fic');
		header('Content-Disposition: attachment; filename="'.$filename.'"');


		foreach ($donnees as $key => $value) {
			foreach ($value as $k => $v) {
				for($i=1;$i<=36;$i++){
					if(isset($v[$i])){
						$string .= $v[$i];
						if($i!=36) $string .= ";";
					}else{
						if($i!=36) $string .= ";";
					}
				}
				$string .= "\n";
				$lignes ++;
			}
		}
        $string .= "99;".$total_debit.";".$total_credit.";"."EUR\n";

        $lignes++;
        $string .=  "0;".$lignes.";".date("Ymd");
        echo $string;
	}


	/**
	 * Retourne le mois passé en parametre en Francais
	 * @param  string $mois
	 * @return string mois en lettre en Francais
	 */
	public function getMoisFrancais($mois){
		$month = array(
					    "01" => 'Janvier',
					    "02" => 'Février',
					    "03" => 'Mars',
					    "04" => 'Avril',
					    "05" => 'Mai',
					    "06" => 'Juin',
					    "07" => 'Juillet',
					    "08" => 'Août',
					    "09" => 'Septembre',
					    "10" => 'Octobre',
					    "11" => 'Novembre',
					    "12" => 'Decembre'
					);
		return strtoupper($month[$mois]);
	}

};