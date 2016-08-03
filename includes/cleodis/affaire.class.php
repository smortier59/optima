<?
/** Classe affaire
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../affaire.class.php";
class affaire_cleodis extends affaire {

	public function __construct() {
		$this->table = "affaire";
		parent::__construct();
		$this->fieldstructure();
	}

	/** 
	* Retourne l'objet devis associé à la commande passée en paramètre
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return devis_cleodis
	*/
	function getDevis($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire){
			ATF::devis()->q->reset()->setStrict()->addField('devis.id_devis')->addCondition("devis.id_affaire",$id_affaire)->setDimension("cell");
			if($id_devis = ATF::devis()->sa()) {
				return new devis_cleodis($id_devis);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/** 
	* Retourne l'objet prolongation associée à la commande passée en paramètre
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return devis_cleodis
	*/
	function getProlongation($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire){
			ATF::prolongation()->q->reset()->setStrict()->addField('id_prolongation')->addCondition("id_affaire",$id_affaire)->setDimension("cell");
			if($id_prolongation = ATF::prolongation()->sa()) {
				return new prolongation($id_prolongation);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/** 
	* Retourne l'objet demande_refi acceptée associée à la commande passée en paramètre
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return devis_cleodis
	*/
	function getDemandeRefiValidee($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire){
			if($demande_refi = $this->refiValid($id_affaire)) {
				return new demande_refi($demande_refi["id_demande_refi"]);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* Met à jour une valeur d'attribut 
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $attribute
	* @param string $value
	* @return mixed Résultat de la requête d'effacement
	*/	
	function set($attribute,$value){
		$oldValue = $this->get($attribute);
		if ($return = parent::set($attribute,$value)) {
			switch ($attribute) {
				case "etat":
					ATF::$msg->addNotice(loc::mt(
						ATF::$usr->trans("etat_change",$this->table)
						,array(
							"old"=>ATF::$usr->trans($oldValue,$this->table)
							,"new"=>ATF::$usr->trans($value,$this->table)
							,"ref"=>$this->get("ref")
						)
					));
					break;
					
				case "nature":
					ATF::$msg->addNotice(loc::mt(
						ATF::$usr->trans("nature_change",$this->table)
						,array(
							"old"=>ATF::$usr->trans($oldValue,$this->table)
							,"new"=>ATF::$usr->trans($value,$this->table)
							,"ref"=>$this->get("ref")
						)
					));
					break;
			}
		}
	}

	/** 
	* Retourne la date de livraison prévue en utilisant la date de début passée en paramètre, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string date (Y-m-d)
	* @param int $delai en jours
	* @return string date (Y-m-d)
	*/
	function getDateLivraison($date_debut,$delai=21){
		$date_debut = strtotime($date_debut);
		return date("Y-m-d",mktime(
			0,0,0,
			date('m',$date_debut),
			date('d',$date_debut)+$delai,
			date('Y',$date_debut)
		));
	}
	


	/** 
	* Met à jour le forecast d'une affaire en fonction de son état, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $etat
	* @return affaire_cleodis
	*/
	function majForecastProcess(){
		$this->notSingleton();
		
		// 10% Proposition créée
		if ($devis = $this->getDevis()) {
			$forecast += 10;
		}
		
		// +15% Contrat créé
		if ($commande = $this->getCommande()) {
			$forecast += 15;
			
			//	+25% Contrat signé (retour contrat renseigné)
			if ($commande->estSigne()) {
				$forecast += 25;
			}
		
			// +15% Contrat démarré   
			if ($commande->estEnCours()) {
				$forecast += 15;
			}
		}
		
		if ($demandeRefiValidee = $this->getDemandeRefiValidee()) {
			$forecast += 25;
		}
		
		// +10% Date d'installation prévue renseignée de moins d'un mois
		if ($date_install_prevue = $this->get("date_installation_prevu")) {
			if (strtotime($date_install_prevue) <= time()+86400*28) {
				$forecast += 10;
			}
		}
						
		if($forecast){
			$this->set("forecast",$forecast);
			return true;
		}else{
			return false;
		}
	}


	
	function num_avenant($ref){
		$tab_ref=explode("AVT",$ref);
		return ($tab_ref[1]);
	}
	
	/**
    * Fonction qui retourne la demande_refi d'une affaire en état valide
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    * @param int id_affaire
    * @return array demande_refi
    */    
	public function refiValid($id_affaire){
		ATF::demande_refi()->q->reset()->addCondition("id_affaire",$id_affaire)
									   ->addCondition("etat","valide")
									   ->setDimension("row");
									  
		if($refi=ATF::demande_refi()->sa()){
			return $refi;
		}else{
			return false;
		}
	}

	/**
    * Retourne les infos du compte en T
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	* 		$infos[id_affaire]
	* @return string html
    */
	public function getCompteT(&$infos) {
		$infos["display"]=true;
		if ($infos["id_affaire"]) {
			ATF::$html->assign("id_affaire",$infos["id_affaire"]);
			ATF::$html->assign("type",$infos["type"]);
			$affaire = new affaire_cleodis($infos["id_affaire"]);
			$devis = $affaire->getDevis();
			$commande = $affaire->getCommande();
			ATF::$html->assign("affaire",$affaire);
			
			// Lignes
			if ($commande) {
					// Si on a une commane, on utilise les lignes du contrat
				$lignesDataVisibles = $commande->getLignes("visible");
				$lignesDataNonVisibles = $commande->getLignes("invisible");
				$lignesDataReprises = $commande->getLignes("reprise");
			} else {
				// Sinon on utilise les lignes du devis
				$lignesDataVisibles = $devis->getLignes("visible");
				$lignesDataNonVisibles = $devis->getLignes("invisible");
				$lignesDataReprises = $devis->getLignes("reprise");
			}
			
			// Valeurs des grids de lignes
			foreach (array("lignesDataVisibles","lignesDataNonVisibles","lignesDataReprises") as $grid) {
				ATF::$html->assign($grid,json_encode($$grid));
				
				// Calcul total
				$total = 0;
				foreach ($$grid as $i) {
					$total += $i["quantite"]*$i["prix_achat"];
				}
				$lignesTotal += $total;
				ATF::$html->assign($grid."Total",$total);				
			}
			ATF::$html->assign("lignesTotal",$lignesTotal);				
			
			// Factures des dépenses
			ATF::facture_fournisseur()->q->reset()
				->addField("facture_fournisseur.ref")
				->addField("facture_fournisseur.id_fournisseur")
				->addField("facture_fournisseur.date")
				->addField("facture_fournisseur.prix")
				->where("id_affaire",$affaire->get("id_affaire"));
			$facturesDataFournisseurs = util::removeTableInKeys(ATF::facture_fournisseur()->select_all()); // On préfixe pour avoir la jointure auto des clés étrangères, mais les clés font chier ExtJS en retour
			
			// Factures non parvenues
			ATF::facture_non_parvenue()->q->reset()
				->addField("facture_non_parvenue.ref")
				->addField("facture_non_parvenue.date")
				->addField("facture_non_parvenue.prix")
				->where("id_affaire",$affaire->get("id_affaire"));
			$facturesDataNonParvenues = util::removeTableInKeys(ATF::facture_non_parvenue()->select_all()); // On préfixe pour avoir la jointure auto des clés étrangères, mais les clés font chier ExtJS en retour
			
			// Factures de recettes
			ATF::facture()->q->reset()
				->addField("facture.ref")
				->addField("facture.type_facture")
				->addField("facture.date")
				->addField("facture.prix")
				->from("facture","id_demande_refi","demande_refi","id_demande_refi")
				->where("facture.type_facture","refi","OR","casRefi","!=")->where("demande_refi.etat","accepte","OR","casRefi") // Ne pas sélectionner de demande de refi sauf si elle est acceptée
				->where("facture.type_facture","ap","OR",NULL,"!=") // Ne pas sélectionner les avis de prélèvements car il ne sont pas des recettes
				->where("facture.id_affaire",$affaire->get("id_affaire"));
			$facturesCleodisData = util::removeTableInKeys(ATF::facture()->sa()); // On préfixe pour avoir la jointure auto des clés étrangères, mais les clés font chier ExtJS en retour
			foreach (array("facturesDataNonParvenues","facturesDataFournisseurs","facturesCleodisData") as $grid) {
				ATF::$html->assign($grid,json_encode($$grid));
				
				// Calcul total
				$total = 0;
				foreach ($$grid as $i) {
					$total += $i["prix"];
				}
				if ($grid=="facturesDataNonParvenues" && $total<0) {
					$total = 0;
				}
				if ($grid=="facturesDataNonParvenues" || $grid=="facturesDataFournisseurs") {
					$facturesTotal += $total;
				}
				${$grid."Total"}=$total;
				ATF::$html->assign($grid."Total",$total);
			}
			ATF::$html->assign("facturesTotal",$facturesTotal);
		
			// Taux de l'affaire
			if ($infos["type"]=="manager") { // Protection suffisante
				if ($dr = $affaire->getDemandeRefiValidee()) {
					$taux = $dr->get("taux");
				}
				if (!$taux) {
					$taux = $affaire->get("taux_refi_reel");
				}
			} else {
				$taux = $infos["taux"];
				if ($taux>=0 && strlen($taux)) {
					$affaire->set("taux_refi",$taux); // Sauvegarde du taux modifié
				} else {
					$taux = $affaire->get("taux_refi");
				}
			}
			if (!$taux) {
				$taux = 0;
			}
			ATF::$html->assign("taux",$taux);
			

			// Calcul du loyer actualisé			
			$vr = 0;
			$loyers = $affaire->getCompteTLoyersActualises($taux,$vr);
			$loyerDataVA = $loyers[0]["pv"];
			ATF::$html->assign("vr",round($vr,2));
			
			// Loyers et calcul de valeur actualisée
			ATF::$html->assign("loyerData",json_encode($loyers));
			ATF::$html->assign("loyerDataVA",$loyerDataVA);

			// Calculs finaux
			ATF::$html->assign("resteAFacturer",$affaire->getResteAPayer());
			if ($infos["type"]=="manager") {
				$depensesTotal = $facturesTotal ? $facturesTotal : $lignesTotal;
			} else {
				$depensesTotal = $lignesTotal;
			}
			ATF::$html->assign("depensesTotal",$depensesTotal);
			ATF::$html->assign("marge",$marge = round($loyerDataVA - $depensesTotal,2));
			ATF::$html->assign("margePourcent",round(100*$marge/$loyerDataVA,2));
			
			return ATF::$html->fetch("compte_t.tpl.htm");
		}
	}
	
	/**
    * Retourne les loyers restant à facturer
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return cell
    */
	public function getResteAPayer() {
		$this->notSingleton();
		
		return ATF::facturation()->getResteAPayer($this->infos["id_affaire"]);
	}
	
	/**
    * Retourne les loyers de l'affaire
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire
	* @return array
    */
	public function getLoyers($id_affaire) {
		ATF::loyer()->q->reset()
			->addField("loyer")
			->addField("duree")
			->addField("assurance")
			->addField("frais_de_gestion")
			->addField("frequence_loyer")
			->where("id_affaire",$id_affaire)
			->addOrder("id_loyer","desc");
		return ATF::loyer()->select_all();		
	}
	
	/**
    * Retourne les loyers actualisés
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param float $taux
	* @param float $vr
	* @param array $loyers Loyer si besoin
	* @return array
    */
	public function getCompteTLoyersActualises($taux,&$vr=NULL,$loyers=NULL) {
		
		$this->notSingleton();
		
		// Récupérer les loyers
		if (!$loyers) {
			$loyers = $this->getLoyers($this->get("id_affaire"));
		}
		
		// Librairie externe Finance
		require_once 'finance.class.php';
			
		
		// Baser les calculs sur la valeur résiduelle de la demande de refi acceptée
		if ($demandeRefi = $this->getDemandeRefiValidee()) {
			$vr = $demandeRefi->get("valeur_residuelle");
		}

		
		$f = new Financial;
		$freq = array("mois"=>12,"trimestre"=>4,"semestre"=>2,"an"=>1);
		$vr2 = $vr;
		foreach ($loyers as $i => $loyer) {
			if ($pv) {
				$vr2 = $pv; 
			}

			$pv = -$f->PV($taux/$freq[$loyer["frequence_loyer"]]/100, $loyer["duree"], ($loyer["loyer"]+$loyer["frais_de_gestion"]+$loyer["assurance"]), $vr2 , 1);
			$loyers[$i]["pv"] = round($pv,2);
		}
		$loyers = array_reverse($loyers);
		return $loyers;
	}
	
	/**
    * Retourne le loyer actualisé total
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	*		int		$infos[id_affaire]
	*		float	$infos[taux]
	*		float	$infos[vr]  Valeur résiduelle
	* @return float
    */
	public function getCompteTLoyerActualise(&$infos) {		
		$a = new affaire_cleodis($infos["id_affaire"]);

		if ($c = $a->getCommande()) {
			$date_debut = $c->get("date_debut");
		}
		if ($date_debut && $infos["date_cession"]) {
				
			$date1 = new DateTime(substr($infos["date_cession"],0,8).'01');
			$date1->modify('+1 month'); // On prend le premier jour du mois suivant ( nécessaire en cas de date de cession en dernier jour de période pleine,et à cause du pb des bisextile, et en plus a ce bug de merde : https://bugs.php.net/bug.php?id=52480 )
			
			$date_2 = new DateTime($date_debut);
		
			$duree_ecoulee_restante = $duree_ecoulee = $date1->diff($date_2)->format('%y')*12 + $date1->diff($date_2)->format('%m');
			if($date1->diff($date_2)->format('%d')>0) $duree_ecoulee_restante = $duree_ecoulee = $duree_ecoulee+1;

			// On "rogne" les mois deja écoulé jusqu'àla date de cession	
			$frequence_loyer=array("mois"=>1,"trimestre"=>3,"semestre"=>6,"an"=>12);
			$loyers = $this->getLoyers($infos["id_affaire"]);
			$loyers = array_reverse($loyers);
			foreach ($loyers as $k => $loyer) {
		
				if ($duree_ecoulee_restante>0) { // Tant qu'il reste de la durée à rogner
					$duree = ceil($loyer["duree"]*$frequence_loyer[$loyer["frequence_loyer"]]);
	
					$duree_max_pouvant_etre_retiree_de_ce_loyer = min($duree,$duree_ecoulee_restante);
		
					$loyer["duree"] -= $duree_max_pouvant_etre_retiree_de_ce_loyer / $frequence_loyer[$loyer["frequence_loyer"]];
					$duree_ecoulee_restante -= $duree_max_pouvant_etre_retiree_de_ce_loyer;
	
					$loyers[$k] = $loyer;
				}
			}
			$loyers = array_reverse($loyers);
		}	
	
		$loyers = $a->getCompteTLoyersActualises($infos["taux"],$infos["vr"],$loyers);
	
		//date_default_timezone_set($fuseau);	// Fin du truc chelou	: https://bugs.php.net/bug.php?id=52480
		return $loyers[0]["pv"];
	}
	
	/**
    * Retourne les affaires avec les affaires parentes en plus
    * @author Quentin JANON <qjanon@absystech.fr>
    */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		$this->q->addJointure("affaire","id_affaire","commande","id_affaire")
				->addField("commande.etat");
		$return = parent::select_all($order_by,$asc,$page,$count);
		$a = new affaire_cleodis();
		foreach ($return['data'] as $k=>$i) {
			if ($i['affaire.nature'] == 'AR') {
				foreach ($a->getParentAR($i['affaire.id_affaire']) as $k_=>$i_) {
					$return['data'][$k]['parentes'] .= '<a href="#affaire-select-'.$a->cryptId($i_['id_affaire']).'.html">'.$i_['ref'].'</a>, ';
				}
			} elseif ($i['affaire.nature'] == 'vente' || $i['affaire.nature'] == 'avenant') {
				if ($affaire=$a->getParentAvenant($i['affaire.id_affaire'])) {
					$return['data'][$k]['parentes'] .= '<a href="#affaire-select-'.$a->cryptId($affaire->get('id_affaire')).'.html">'.$affaire->get('ref').'</a>, ';
				}
			} else {
				$return['data'][$k]['parentes'] = "";	
			}
		}
		return $return;
	}
	
	/**
    * Vérification et structuration de l'envoi des mails
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $email du client
	* 		$last_id int de l'enregistrement du module concerné
	*		$table table du module
	*		$path chemin du fichier concerné
	* @return boolean
    */
	public function mailContact($email,$last_id,$table,$paths){
		$enregistrement = ATF::$table()->select($last_id);	
		if($email["email"]){
			$recipient = $email["email"];
		}elseif($enregistrement["id_contact"]){
			if(!ATF::contact()->select($enregistrement["id_contact"],"email")){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("Il n'y a pas d'email pour le contact ".ATF::contact()->nom($enregistrement["id_contact"]),349);
			}else{
				$recipient = ATF::contact()->select($enregistrement["id_contact"],"email");
			}
		}else{
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Il n'y a pas d'email",350);
		}

		if(ATF::$usr->getID()){
			$from = ATF::user()->nom(ATF::$usr->getID())." <".ATF::user()->select(ATF::$usr->getID(),"email").">";
		}else{
			$societe = ATF::societe()->select(246);
			$from = $societe["societe"]." <".$societe["email"].">";
		}
		
		if(!$email["objet"]){
			$info_mail["objet"] = "Votre ".$table." référence : ".$enregistrement["ref"];
		}else{
			$info_mail["objet"] = $email["objet"];
		}
		
		$info_mail["from"] = $from;
		$info_mail["html"] = false;
		$info_mail["template"] = $table;
		$info_mail["texte"] = $email["texte"];
		$info_mail["recipient"] = $recipient;
		//Ajout du fichier

		$mail = new mail($info_mail);
		foreach($paths as $key=>$item){
			$path = ATF::$table()->filepath($last_id,$item);		
			$mail->addFile($path,$key.$enregistrement["ref"].".pdf",true);						
		}
		$mail->send();
		
		if($email["emailCopie"]){
			$info_mail["recipient"] = $email["emailCopie"];
			$copy_mail = new mail($info_mail);
			foreach($paths as $key=>$item){
				$path = ATF::$table()->filepath($last_id,$item);		
				$copy_mail->addFile($path,$key.$enregistrement["ref"].".pdf",true);						
			}
			$copy_mail->send();
		}
		return true;
	}


	/**
    * Retourne les pourcentages materiels et immateriels
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id_affaire
	* @return array
    */
	public function getPourcentagesMateriel($id_affaire) {
		$id_affaire = ATF::affaire()->decryptId($id_affaire);
		ATF::devis()->q->reset()->where("devis.id_affaire", $id_affaire);
		$devis = ATF::devis()->select_row();
		
		ATF::devis_ligne()->q->reset()->where("devis_ligne.id_devis", $devis["id_devis"]);
		$lignes = ATF::devis_ligne()->select_all();
		$pourcentagesImmat = $pourcentagesMat = 0;
		foreach ($lignes as $key => $value) {
			if($value["prix_achat"]){
				if($value["type"] != "fixe" && $value["type"] != "portable"){ $pourcentagesImmat = $pourcentagesImmat + ($value["prix_achat"]*$value["quantite"]);	 }
				else{ $pourcentagesMat = $pourcentagesMat + ($value["prix_achat"]*$value["quantite"]);  }
			}			
		}

		$total = $pourcentagesImmat + $pourcentagesMat;

		$return = array("immat"=>$pourcentagesImmat, 
						"pourcentagesImmat" => round(($pourcentagesImmat * 100)/$total, 2),
						"mat"=>$pourcentagesMat, 
						"pourcentagesMat" => round(($pourcentagesMat * 100)/$total , 2));		

		return $return;
	}
};
class affaire_midas extends affaire_cleodis {
	function __construct($table_or_id=NULL) {
		$this->table = "affaire"; 
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			'affaire.affaire'
			,'affaire.id_societe'
			,'affaire.etat'=>array("renderer"=>"etatAffaire","width"=>30)
			,'commande.etat'=>array("width"=>30,"renderer"=>"etat")
			,'parentes'=>array("custom"=>true,"nosort"=>true)
			,'dernier_loyer'=>array("custom"=>true)
			,'date_dem'=>array("custom"=>true,"renderer"=>"datefield")
		);

		$this->colonnes['primary'] = array(
			"ref"=>array("disabled"=>true)
			,"affaire"
			,"etat"
			,"date"
			,"id_societe"
			,"id_filiale"
			,"nature"
			,"forecast"
			,"parentes"=>array("custom"=>true)
			,"filles"=>array("custom"=>true)
			,'RIB'
			,'IBAN'
			,'BIC'
			,'nom_banque'
			,'ville_banque'
		);

		
		$this->colonnes['panel']['date_affaire'] = array(
			"specificDate"=>array("custom"=>true)
		);
		$this->panels['date_affaire'] = array("visible"=>true, 'nbCols'=>1);

		unset($this->colonnes['panel']['rib_facturation'],$this->colonnes['panel']['refRefi']);
		unset($this->panels['rib_facturation'],$this->panels['refRefi']);
		unset($this->files["facturation"]);

		$this->onglets = array(
			'loyer'
			,'devis'
			,'commande'
			,'parc'
		);
		$this->filtre_ob['affaire_franc']=array("titre"=>"Franchisées","function"=>"selectAllFranch");
		$this->filtre_ob['affaire_franc_cours']=array("titre"=>"Franchisées en cours","function"=>"selectAllFranchCours");
		$this->filtre_ob['affaire_franc_cours_info']=array("titre"=>"Franchisées en cours informatique","function"=>"selectAllFranchCoursInfo");

		$this->filtre_ob['affaire_suc']=array("titre"=>"Succursales","function"=>"selectAllSuc");
		$this->filtre_ob['affaire_suc_cours']=array("titre"=>"Succursales en cours","function"=>"selectAllSucCours");
		$this->filtre_ob['affaire_suc_cours_info']=array("titre"=>"Succursales en cours informatique","function"=>"selectAllSucCoursInfo");
		$this->fieldstructure();
	}
	
	/** On affiche que les sociétés midas
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		ATF::loyer()->q->reset()->setToString();
		$subquery=ATF::loyer()->sa("loyer.id_loyer","desc");
		
		$this->q->addField("(loy.loyer+(IF(loy.assurance>0,loy.assurance,0))+(IF(loy.frais_de_gestion>0,loy.frais_de_gestion,0)))","dernier_loyer")
				->addField("commande.date_debut","date_dem")
				->addJointure("affaire","id_affaire","loy","id_affaire","loy",NULL,NULL,NULL,"left",false,$subquery)
				->addJointure("affaire","id_affaire","commande","id_affaire")
				->addJointure("affaire","id_societe","societe","id_societe")
				->addCondition("societe.code_client","M%","OR",false,"LIKE")
				->addCondition("societe.divers_3","Midas")
				->addGroup("affaire.id_affaire");
		return parent::select_all($order_by,$asc,$page,$count);
	}
	
	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllFranch(){
		$this->q->addConditionNull("societe.id_filiale");
		return $this->select_all();
	}
	
	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllFranchCours(){
		$this->q->addCondition("commande.etat","prolongation","OR")->addCondition("commande.etat","mis_loyer","OR");
		return $this->selectAllFranch();
	}
	
	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllFranchCoursInfo(){
		$this->recupCoursInfo($this->q);
		return $this->selectAllFranch();
	}
	
	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllSuc(){
		$this->q->addConditionNotNull("societe.id_filiale");
		return $this->select_all();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllSucCours(){
		$this->q->addCondition("commande.etat","prolongation","OR")->addCondition("commande.etat","mis_loyer","OR");
		return $this->selectAllSuc();
	}
	
	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllSucCoursInfo(){
		$this->recupCoursInfo($this->q);
		return $this->selectAllSuc();
	}
	
	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function recupCoursInfo(&$q){
		ATF::parc()->q->reset()->addField("count(*)","nbre_info")
								->addField("parc.id_affaire","id_affaire")
								->addCondition("libelle","%HP%","OR",false,"LIKE")
								->addCondition("libelle","%NEC%","OR",false,"LIKE")
								->addCondition("libelle","%Brother%","OR",false,"LIKE")
								->addGroup("parc.id_affaire")
								->setToString();
		$subquery=ATF::parc()->sa();
		
		$q->addCondition("commande.etat","prolongation","OR")
			->addCondition("commande.etat","mis_loyer","OR")
			->addCondition("par.nbre_info",0,"OR",false,">")
			->addJointure("affaire","id_affaire","par","id_affaire","par",NULL,NULL,NULL,"left",false,$subquery);
	}
};

class affaire_cleodisbe extends affaire_cleodis { };
class affaire_cap extends classes_optima { 
	function __construct($table_or_id=NULL) {
		$this->table = "affaire"; 
		parent::__construct();

		$this->colonnes["fields_column"] = array(	
			 'affaire.ref'
			,'affaire.date'
			,'affaire.id_societe'
		);

		$this->actions_by = array("insert"=>"audit","update"=>"audit");
		$this->fieldstructure();
		
		
		$this->onglets = array(
			 "audit"
			,"mandat"
		);
		
		
		$this->field_nom="ref";
		$this->foreign_key["id_societe"] = "societe";
		
		$this->no_delete = true;
		$this->no_update = true;
		$this->no_insert = true;
		$this->can_insert_from = array("societe");
	}


};
class affaire_exactitude extends affaire_cleodis {
	function __construct($table_or_id=NULL) {
		$this->table = "affaire"; 
		parent::__construct($table_or_id);	
		
		$this->colonnes['fields_column']['tableau_chasse']=array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70,"renderer"=>"uploadFile");
		

		$this->fieldstructure();

		$this->onglets = array(	
			 'affaire_candidat'=>array('opened'=>true)
			,'tableau_chasse'=>array('opened'=>true)		
			,'devis'=>array('opened'=>true)			
			//,'commande'=>array('opened'=>true)			
			,'facture'=>array('opened'=>true)		
			,'suivi'
			,'tache'
		);

		$this->files["tableau_chasse"] = array("type"=>"xls","no_generate"=>true);
		
	}
 };
?>