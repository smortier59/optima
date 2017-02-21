<?
/** Classe facture
* @package Optima
* @subpackage Absystech
*/
require_once dirname(__FILE__)."/../facture.class.php";
class facture_absystech extends facture {
	/**
	* Mail de facture
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr> Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	private $facture_mail;

	/**
	* Mail de copy de facture
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr> Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	private $facture_copy_mail;

	/**
	* Mail actuel
	* @var mixed
	*/
	private $current_mail=NULL;

	/**	* Constructeur par défaut
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "facture";
		$this->colonnes['fields_column'] = array(
			'facture.ref'=>array("width"=>100,"align"=>"center")
			,'facture.id_societe'
			,'facture.date'=>array("width"=>100,"align"=>"center")
			,'facture.date_previsionnelle'=>array("width"=>100,"align"=>"center")
			,'facture.etat'=>array("width"=>30,"renderer"=>"etat")
			,'facture.date_effective'=>array("width"=>100,"align"=>"center")
			,'facture.frais_de_port'=>array("renderer"=>"money","width"=>80)
			,'facture.prix'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'prix_ttc'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'solde'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'retard'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"duree","width"=>80)
			,'interet'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
			,'actions'=>array("custom"=>true,"nosort"=>true,"align"=>"center","width"=>100,"renderer"=>"actionsFacture")
		);

		$this->colonnes['primary'] = array(
			"id_societe"=>array("autocomplete"=>array(
				"function"=>"autocompleteAvecTVA"
				,"mapping"=>array(
					array('name'=> 'tva', 'mapping'=> 'raw_0')
					,array('name'=>'id', 'mapping'=> 1)
					,array('name'=> 'nom', 'mapping'=> 2)
					,array('name'=> 'detail', 'mapping'=> 3, 'type'=>'string' )
					,array('name'=> 'nomBrut', 'mapping'=> 'raw_2')
				)
			))
			,"date"
			,'date_previsionnelle'=>array("obligatoire"=>true)
			,'date_relance'
			,'date_modification'
			,'regenerate'=>array("custom"=>true)
			,'id_affaire'
			,'affaire_sans_devis'=>array("xtype"=>"checkbox","null"=>true)
			,'affaire_sans_devis_libelle'=>array("xtype"=>"textfield","null"=>true)
			,'infosSup'
			,'dematerialisation'=>array("xtype"=>"checkbox","null"=>true)
		);

		$this->colonnes['panel']['mode_facturation'] = array(
			"mode"=>array("custom"=>true,"data"=>array("facture","avoir","factor","acompte"),"xtype"=>"combo","listeners"=>array("change"=>"ATF.changeModeFacture"))
			,"id_facture_parente"=>array("disabled"=>true)
			,"date_debut_periode"
			,"periodicite"=>array("disabled"=>false,"listeners"=>array("change"=>"ATF.changePeriode"))
			,'id_termes'=>array("updateOnSelect"=>true,"custom"=>true)
			,"date_fin_periode"
			,"acompte_pourcent"=>array("custom"=>true,"xtype"=>"numberfield","listeners"=>array("change"=>"ATF.changeAcompte"))
			,"finale"=>array("custom"=>true,"xtype"=>"checkbox")
		);

		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['total'] = array(
			"sous_total"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"frais_de_port"=>array("custom"=>true,"listeners"=>array("change"=>"ATF.changeFraisDePort"),"formatNumeric"=>true,"xtype"=>"textfield")
			,"prix"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"prix_achat"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"marge"=>array("custom"=>true,"readonly"=>true)
			,"marge_absolue"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"tva"=>array("formatNumeric"=>true,"xtype"=>"textfield","num_decimal_places"=>3)
		);

		$this->colonnes['panel']['courriel'] = array(
			"email"=>array("custom"=>true,'null'=>true)
			,"emailCopie"=>array("custom"=>true,'null'=>true)
			,"emailTexte"=>array("custom"=>true,'null'=>true,"xtype"=>"htmleditor")
		);

		// Propriété des panels
		$this->panels['mode_facturation'] = array("visible"=>true,'nbCols'=>3,"collapsible"=>false);
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>4);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>4);
		$this->panels['courriel'] = array('nbCols'=>2);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array('type_facture','divers_1','ref','id_user','etat','id_commande','regenerate','date_effective');

		$this->colonnes['bloquees']['update'][] = "affaire_sans_devis";
		$this->colonnes['bloquees']['update'][] = "affaire_sans_devis_libelle";

		//IMPORTANT, complte le tableau de colonnes avec les infos MYSQL des colonnes
		$this->fieldstructure();

		$this->foreign_key["id_facture_parente"] = "facture";

		$this->addPrivilege('rapprochementFacture');
		$this->addPrivilege('rapprochementFactureSA');
		$this->addPrivilege('getTVA');
		$this->addPrivilege('generatePDF','insert');
		$this->addPrivilege("lettre_change");
		$this->addPrivilege("autocompleteFactureAvoirDispo");


		$this->onglets = array('facture_ligne','facture_paiement');
		$this->stats_types = array("user","users");
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true,"quickMail"=>true);
		//$this->files["lettre_de_change"] = array("type"=>"pdf","no_upload"=>true);
		$this->field_nom = "ref";
		$this->formExt=true;


		$this->autocomplete = array(
			"field"=>array("facture.ref","facture.prix")
			,"show"=>array("facture.ref","facture.prix")
			,"popup"=>array("facture.ref","facture.prix")
			,"view"=>array("facture.ref","facture.prix")
		);
	}

	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->addField($key);
				}
			}
		}
		$this->q
			->addJointure("facture","id_societe","societe","id_societe")
			->addJointure("facture","id_facture","facture_paiement","id_facture")
			->addField("ROUND(facture.prix*facture.tva,2)","prix_ttc")
			->addField("ROUND(IF(facture.date_effective IS NOT NULL
								,0
								,IF(
									(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
									,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
									,(facture.prix*facture.tva)
								)),2)","solde")
			->addField("TO_DAYS(IF(facture.date_effective IS NOT NULL,facture.date_effective,NOW())) - TO_DAYS(facture.date_previsionnelle)","retard")
			->addField("IF(facture.etat!='perte'
							,IF((TO_DAYS(IF(facture.date_effective IS NULL,NOW(),facture.date_effective)) - TO_DAYS(facture.date_previsionnelle))>1
								,40+ ((((TO_DAYS(IF(facture.date_effective IS NULL,NOW(),facture.date_effective)) - TO_DAYS(facture.date_previsionnelle)) *0.048)/365)
								    *ROUND(IF(
										(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
										,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
										,facture.prix*facture.tva
									),2))
							,IF( ((((TO_DAYS(IF(facture.date_effective IS NULL,NOW(),facture.date_effective)) - TO_DAYS(facture.date_previsionnelle)) *0.048)/365)
								    *ROUND(IF(
										(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
										,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
										,facture.prix*facture.tva
									),2))>0
								, ((((TO_DAYS(IF(facture.date_effective IS NULL,NOW(),facture.date_effective)) - TO_DAYS(facture.date_previsionnelle)) *0.048)/365)
							    *ROUND(IF(
									(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
									,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
									,facture.prix*facture.tva
								),2))
								, 0 )
							)
						,0)","interet")
			->addGroup("facture.id_facture");
		$return = parent::select_all($order_by,$asc,$page,$count);

		foreach ($return['data'] as $k=>$i) {
			//{if $fact["etat"] == "impayee"  && $current_class->is_past($fact["date_previsionnelle"]) && ATF::relance()->getNumeroDeRelance($idCrypt)}
			if ($i['facture.id_facture']) { // Seulement si on a une clé, car dans lec as d'un autocomplete on demande pas ce field...
				$rNo = ATF::relance()->getNumeroDeRelance($i['facture.id_facture']);
				if ($i['facture.etat'] == "impayee" && $this->is_past($i['facture.date_previsionnelle']) && $rNo) {
					$return['data'][$k]['allowRelance'] = true;
					$return['data'][$k]['allowPDFRelance'] = true;
				} else {
					$return['data'][$k]['allowRelance'] = false;
					$return['data'][$k]['allowPDFRelance'] = false;
				}
				if ($rNo=='fourth') {
					$return['data'][$k]['allowPDFRelance'] = false;
				}

				if ($i['solde']>0) {
					$return['data'][$k]['allowSolde'] = true;
				} else {
					$return['data'][$k]['allowSolde'] = false;
				}

				if ($i['facture.etat']=="payee") {
					$return['data'][$k]['retard'] = 0;
					$return['data'][$k]['interet'] = 0;
				}

			}
		}
		return $return;
	}

	/**
	* Prédicat sur l'antériorité par rapport à la date d'aujourd'hui
	* @param string $jour
	* @return bool true si le jour est antérieur à aujourd'hui

	public function getInterets($id) {
		$facture = $this->select($id);
		if ($facture['etat']!="perte") {
			//Début du calcul
			$calcul = 0.01/30;
			// Calcul du nombre de jours entre la date previsionnelle et la date effective de paiement
			$nbJours = round((strtotime($facture['date_effective']?$facture['date_effective']:date("Y-m-d")) - strtotime($facture['date_previsionnelle']))/(60*60*24));
			// On multiplie le résultat au calcul
			$calcul = $calcul * $nbJours;
			// On récupère la somme des paiement pour cette facture
			foreach (ATF::facture_paiement()->ss("id_facture",$facture['id_facture']) as $k=>$i) {
				$sum += $i['montant'];
			}
			$round = $facture['prix']*$facture['tva'] - $sum;
			if ($round < 0) {
				$round = round($facture['prix']*$facture['tva'],2);
			}
			$calcul = $calcul * $round;

			if ($calcul>=65) {
				return round($calcul,2);
			}

		}
		return false;
	}
	*/
	/**
	* Prédicat sur l'antériorité par rapport à la date d'aujourd'hui
	* @param string $jour
	* @return bool true si le jour est antérieur à aujourd'hui
	*/
	public function is_past($jour) {
		$now = time();
		$date = strtotime($jour);

		if ($date < $now){
			return true;
		}else{
			return false;
		}
	}

	/*public function cloner($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		unset($infos["facture"]["id_facture"]);
		return parent::cloner($infos,$s,$files,$cadre_refreshed,$nolog);
	}*/

	public function getLastFacture($id_affaire,$idRef=false,$copieur=false) {
		$facture = ATF::facture()->select($idRef);

		$this->q->reset()
				->addField("id_facture")
				->addField("date")
				->addField("ref")
				->where('id_affaire',$id_affaire)
				->setLimit(1);

		if ($idRef){
			if($copieur){
				$this->q->where('id_facture',$idRef,"AND","","!=")
				        ->where('date',$facture["date"],"AND","","<")
				        ->addOrder('date','desc');
			}else{
				$this->q->where('id_facture',$idRef,"AND","","<")->addOrder('id_facture','desc');
			}

		} else {
			$this->q->addOrder('date','desc');
		}
		$facs = $this->sa();
		if($facs[0]){
			return $facs[0];
		}
		return $facs;
	}


	/**
	* Surcharge de l'insert afin d'insérer les lignes de factures et modifier l'état de l'affaire sur l'insert d'une facture
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @author Cyril Charlier <ccharlier@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
 		$preview=$infos["preview"];
		$type_check=$infos[$this->table]["mode"];
		$finale=$infos[$this->table]["finale"];
		$acompte_pourcent=$infos[$this->table]["acompte_pourcent"];
		$id_commande=$infos[$this->table]["id_commande"];

		$this->infoCollapse($infos);

		// FLAG qui identifie une facture provenant d'un echeancier
		$echeancier = $infos['id_echeancier'] ? true : false;

		if($infos["type_facture"] == "acompte"){
			$finale = false;
			$infos["type_facture"] = "facture";
		}

		if(!count($infos_ligne)){
			throw new errorATF("Une facture doit comporter au moins une ligne.",161);
		}

		if(!$infos["id_societe"]){
			throw new errorATF("Vous devez spécifier la société (Entité)",167);
		}else{
			if(ATF::societe()->estFermee($infos["id_societe"])){
				throw new errorATF("Impossible d'ajouter une facture sur une entité fermée");
			}
		}

		if ($infos["affaire_sans_devis"]) {
			if (!$infos["affaire_sans_devis_libelle"]) {
				throw new errorATF("Pour une affaire sans devis, il faut saisir un libellé de votre choix.",162);
			}
			unset($infos["affaire_sans_devis"]);
			// Alors on crée une affaire pour l'occasion
			$affaire_sans_devis = true;
		}
		elseif(!$infos["id_affaire"]){
			throw new errorATF("Vous devez spécifier une affaire, sinon cochez la case CREER AFFAIRE SANS DEVIS, alors une affaire sera créée avec pour nomination 'Libellé affaire sans devis'.",160);
		}

		// Dematerialisation
		if($infos["dematerialisation"]){
			ATF::_r('dematerialisation',true);
			unset($infos["dematerialisation"]);
		}




		// On calcul le prix par rapport aux lignes
		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("facture_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}

			if(!$item["quantite"]) $item["quantite"]=0;

			$prixFinal += $item["prix"]*$item["quantite"];
		}

		$prixFinal += $infos["frais_de_port"];
		/*Formatage des numériques*/
		$infos["prix"]=util::stringToNumber($prixFinal);
		$infos["frais_de_port"]=util::stringToNumber($infos["frais_de_port"]);

		if($infos["emailTexte"]){
			$email["email"]=$infos["email"];
			$email["emailCopie"]=$infos["emailCopie"];
			$email["texte"]=$infos["emailTexte"];
		}else{
			$email=false;
		}

		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"],$infos["marge"],$infos["prix_achat"],$infos["sous_total"],$infos["mode"],$infos["acompte_pourcent"],$infos["id_commande"],$infos["finale"],$infos["preview"],$infos["marge_absolue"]);

		if($id_commande){
			$infos["id_affaire"]=ATF::commande()->select($id_commande,"id_affaire");
		}
		$infos["id_user"] = ATF::$usr->getID();
		$infos["ref"] = ATF::affaire()->getRef($infos["date"],"facture");

		if(!$infos["date_previsionnelle"]){
			$infos["date_previsionnelle"] = date('Y-m-d',strtotime(date("Y-m-d")." + 30 day"));
		}

		$societe=ATF::societe()->select($infos["id_societe"]);

		//Seuls les associés et Emma peuvent modifier la tva
		$tva=$this->getTVA($societe["id_societe"]);
		$assistantDirection = 9;
		if(ATF::$codename == "att") $assistantDirection = 5;


		if($tva!=$infos["tva"] && (ATF::$usr->get("id_profil")!=1 && ATF::$usr->get("id_profil")!=$assistantDirection )){
			$profil=ATF::profil()->select(1);
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("error_403_facture_tva"),array("profil"=>$profil["profil"], "tva"=>$tva))
				,ATF::$usr->trans("Droits_d_acces_requis_pour_cette_operation")
			);
			$infos["tva"] = $tva;
		}

		if ($type_check=="facture") {
			if($acompte_pourcent!=100 && $acompte_pourcent){
				$infos["prix"]*=($acompte_pourcent/100);
				$infos["type_facture"]="acompte";
			}elseif($id_commande && ($anc_facture = $this->facture_by_commande($id_commande,true))){
				$infos["prix"]-=$anc_facture["prix"];
				$infos["type_facture"]="solde";
			}else{
				$infos["type_facture"]="facture";
			}
		}elseif($type_check=="avoir"){
			$infos["prix"]=0-$infos["prix"];
			if(!$infos["id_facture_parente"]){
				throw new errorATF("Pour un avoir, il est obligatoire de renseigner la facture parente",170);
			}

			$parent = $this->select($infos["id_facture_parente"]);
			unset($parent['ref']);
			//$infos = $parent;
			$infos["type_facture"]="avoir";
			$infos['prix'] = 0-$parent["prix"];

			// ATF::commande_facture()->q->reset()->addCondition('id_facture',$infos['id_facture_parente'])->end();
			// if($id_commande_factures=ATF::commande_facture()->select_all()){
			// 	$commande=ATF::commande()->select($id_commande_factures[0]["id_commande"]);

			// 	if($commande["prix"]!=$infos["prix"]){
			// 		$infos["frais_de_port"]=$infos["frais_de_port"];
			// 		//Si c'est un solde ou un acompte
			// 		$sum_anc_facture=ATF::facture()->facture_by_commande($commande["id_commande"],true);
			// 	}
			// }

			// $infos["prix"] = $infos["prix"] - $sum_anc_facture["prix"];


		}elseif($type_check=="factor"){
			$infos["type_facture"]="factor";
			if(!$societe["rib_affacturage"] || !$societe["iban_affacturage"] || !$societe["bic_affacturage"]){
				throw new errorATF("Il manque l'une de ces informations pour la société ".$societe["societe"]." : RIB, IBAN, BIC",167);
			}
		}

		$prix = 0;
		ATF::db($this->db)->begin_transaction();
		$prix = $infos["prix"];
		//*****************************Transaction********************************

			// Affaire
			if($infos["id_affaire"]){
				$etat_affaire=ATF::affaire()->select($infos["id_affaire"],"etat");
				if ($etat_affaire=="devis" || $etat_affaire=="commande") {
					$affaire["id_affaire"]=$infos["id_affaire"];
					$affaire["etat"]="facture";
					ATF::affaire()->u($affaire,$s);
				}
			} else {
				$affaire["id_societe"]=$infos["id_societe"];
				$affaire["etat"]="facture";
				$affaire["date"]=$infos["date"];
				$affaire["forecast"]=100;
				$affaire["affaire"]=$infos["affaire_sans_devis_libelle"];
				$infos["id_affaire"]=ATF::affaire()->insert($affaire,$s);
			}
			unset($infos["affaire_sans_devis_libelle"]);

			$prix = $infos["prix"];

			if($infos["type_facture"]=="facture" && $infos["id_termes"] === NULL){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("Vous devez spécifier les termes",167);
			}

			//Facture
			$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

			//Facture Ligne
			foreach($infos_ligne as $key=>$item){
				foreach($item as $k=>$i){
					$k_unescape=util::extJSUnescapeDot($k);
					$item[str_replace("facture_ligne.","",$k_unescape)]=$i;
					unset($item[$k]);
				}

				$item["id_fournisseur"]=$item["id_fournisseur_fk"];
				$item["id_compte_absystech"]=$item["id_compte_absystech_fk"];
				unset($item["id_fournisseur_fk"],$item["id_compte_absystech_fk"],$item["marge"],$item["marge_absolue"]);
				$item["id_facture"]=$last_id;
				$item["index"]=util::extJSEscapeDot($key);
				if(!$item["quantite"]){
					$item["quantite"]=0;
				}
				ATF::facture_ligne()->i($item,$s);
			}

			if($infos["periodicite"] && !$echeancier){

				$date = $infos["date_debut_periode"];
				$date = explode("-", $date);

				if(!$infos["date_debut_periode"] || !$infos["date"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF(ATF::$usr->trans("Il faut remplir la date (date d'édition) et la date de début de période pour une facture périodique"),175);
				}

				$total = 0;
				$dateFacture = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2]));

				switch ($infos["periodicite"]) {
	 				case 'mensuelle':
						$mois = mktime( 0, 0, 0, $date[1], 1, $date[2] );
						$dateFin = date("Y-m-d", mktime(0, 0, 0, $date[1], date('t',$mois), $date[2]));
						$dateDeb = date("Y-m-d" , $mois);

						$nbJoursMois = $this->date_diff($dateDeb, $dateFin);
						if($infos["date_fin_periode"]){
							$nbJoursAVenir = $this->date_diff($dateFacture, $infos["date_fin_periode"]);
						}else{
							$nbJoursAVenir = $this->date_diff($dateFacture, $dateFin);
						}


						if($nbJoursMois !== $nbJoursAVenir){
							$remise = $nbJoursAVenir-1;
							$item = array();
							$item["ref"] = "PRORATA";
						    $item["produit"] = "Remise prorata temporis sur un mois : ".($nbJoursMois-$remise)." jours sur ".$nbJoursMois." jours";
						    $item["quantite"] = 1;
						    $item["prix"] = $infos["prix"]*($nbJoursMois-$remise)/$nbJoursMois*-1;
						    $item["id_compte_absystech"] = "";
						    $item["id_facture"] = $last_id;
							$id = ATF::facture_ligne()->i($item,$s);

							$prix += $item["prix"];
						}
					break;

					case 'trimestrielle':
						//01 Janvier - 31 Mars


						if(intval($date[1]) <= 3){
							$mois = mktime( 0, 0, 0, 3, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  1, 1, $date[2])); // 01-01-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 3, date('t',$mois), $date[2]));

						}
						elseif(intval($date[1]) <= 6) {
							//01 Avril - 30 Juin
							$mois = mktime( 0, 0, 0, 6, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  4, 1, $date[2])); // 01-04-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 6, date('t',$mois), $date[2]));

						}
						elseif(intval($date[1]) <= 9) {
							//01 Juillet - 30 Septembre
							$mois = mktime( 0, 0, 0, 9, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  7, 1, $date[2])); // 01-07-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 9, date('t',$mois), $date[2]));
						}
						elseif(intval($date[1]) <= 12) {
							//01 Octrobre - 31 Decembre
							$mois = mktime( 0, 0, 0, 12, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  10, 1, $date[2])); // 01-10-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 12, date('t',$mois), $date[2]));

						}
						$nbJoursTrimestre = ATF::facture_absystech()->date_diff($dateDeb, $dateFin);
						if($infos["date_fin_periode"]){
							$nbJoursAVenir = ATF::facture_absystech()->date_diff($dateFacture, $infos["date_fin_periode"]);
						}else{
							$nbJoursAVenir = ATF::facture_absystech()->date_diff($dateFacture, $dateFin);
						}

						if($nbJoursTrimestre !== $nbJoursAVenir){
							$remise = $nbJoursAVenir-1;
							$item = array();
							$item["ref"] = "PRORATA";
						    $item["produit"] = "Remise prorata temporis sur un trimestre : ".($nbJoursTrimestre-$remise)." jours sur ".$nbJoursTrimestre." jours";
						    $item["quantite"] = 1;
						    $item["prix"] = $infos["prix"]*($nbJoursTrimestre-$remise)/$nbJoursTrimestre*-1;
						    $item["id_compte_absystech"] = "";
						    $item["id_facture"] = $last_id;
							ATF::facture_ligne()->i($item,$s);

							$prix += $item["prix"];
						}
					break;

					case 'semestrielle' :

						//01 Janvier au 30 Juin
						if(intval($date[1]) <= 6){
							$mois = mktime( 0, 0, 0, 6, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  1, 1, $date[2])); // 01-01-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 6, date('t',$mois), $date[2]));

						}//01 Juillet au 31 Decembre
						else{
							$mois = mktime( 0, 0, 0, 12, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  7, 1, $date[2])); // 01-01-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 12, date('t',$mois), $date[2]));
						}

						$nbJoursSemestre = ATF::facture_absystech()->date_diff($dateDeb, $dateFin);
						if($infos["date_fin_periode"]){
							$nbJoursAVenir = ATF::facture_absystech()->date_diff($dateFacture, $infos["date_fin_periode"]);
						}else{
							$nbJoursAVenir = ATF::facture_absystech()->date_diff($dateFacture, $dateFin);
						}


						if($nbJoursSemestre !== $nbJoursAVenir){
							$remise = $nbJoursAVenir-1;
							$item = array();
							$item["ref"] = "PRORATA";
						    $item["produit"] = "Remise prorata temporis sur un semestre : ".($nbJoursSemestre-$remise)." jours sur ".$nbJoursSemestre." jours";
						    $item["quantite"] = 1;
						    $item["prix"] = $infos["prix"]*($nbJoursSemestre-$remise)/$nbJoursSemestre*-1;
						    $item["id_compte_absystech"] = "";
						    $item["id_facture"] = $last_id;
							ATF::facture_ligne()->i($item,$s);

							$prix += $item["prix"];
						}

					break;

					case 'annuelle':
						$mois = mktime( 0, 0, 0, 12, 1, $date[2] );
						$dateDeb = date("Y-m-d", mktime(0, 0, 0, 1, 1, $date[2]));
						$dateFin = date("Y-m-d", mktime(0, 0, 0, 12,  date('t',$mois), $date[2]));

						$nbJoursAnnee = ATF::facture_absystech()->date_diff($dateDeb, $dateFin);
						if($infos["date_fin_periode"]){
							$nbJoursAVenir = ATF::facture_absystech()->date_diff($dateFacture, $infos["date_fin_periode"]);
						}else{
							$nbJoursAVenir = ATF::facture_absystech()->date_diff($dateFacture, $dateFin);
						}

						if($nbJoursAnnee !== $nbJoursAVenir){
							$remise = $nbJoursAVenir-1;
							$item = array();
							$item["ref"] = "PRORATA";
						    $item["produit"] = "Remise prorata temporis sur une année : ".($nbJoursAnnee-$remise)." jours sur ".$nbJoursAnnee." jours";
						    $item["quantite"] = 1;
						    $item["prix"] = $infos["prix"]*($nbJoursAnnee-$remise)/$nbJoursAnnee*-1;
						    $item["id_compte_absystech"] = "";
						    $item["id_facture"] = $last_id;
							ATF::facture_ligne()->i($item,$s);

							$prix += $item["prix"];
						}
					break;
				}

				if($infos["date_fin_periode"]){
					$dateFin = $infos["date_fin_periode"];
				}
				$this->u(array("id_facture"=>$last_id  ,"prix" => $prix, "date_fin_periode" => $dateFin));

				//Ajouter test pour savoir si la dateFin > date_fin maintenance avant de modifier
				ATF::affaire()->q->reset()->where("affaire.id_affaire", $infos["id_affaire"])->addField("affaire.date_fin_maintenance")->setLimit(1);
				$affaire = ATF::affaire()->select_row();

				if($affaire["affaire.date_fin_maintenance"]){
					$nbJoursDiff = $this->date_diff($affaire["affaire.date_fin_maintenance"] , $dateFin);
					if($nbJoursDiff > 0){
						ATF::affaire()->u(array("id_affaire" => $infos["id_affaire"], "date_fin_maintenance" => $dateFin));
					}
				}else{
					ATF::affaire()->u(array("id_affaire" => $infos["id_affaire"], "date_fin_maintenance" => $dateFin));
				}
			}


			//Commande Ligne
			if($id_commande){
				$commande_facture["id_facture"]=$last_id;
				$commande_facture["id_commande"]=$id_commande;
				ATF::commande_facture()->insert($commande_facture,$s);
			}

			//Commande
			if($id_commande && $finale){
				$commande["id_commande"]=$id_commande;
				$commande["etat"]="facturee";
				ATF::commande()->u($commande,$s);
			}


		//***************************************************************************************

		if($preview){
			if($infos['id_echeancier']){
				$pdf_binaire = ATF::pdf()->generic("facture",$last_id,true,$s,true);
				ATF::db($this->db)->rollback_transaction();
				return $pdf_binaire;
			}
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{


			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			/* MAIL */
			if($email){
				if(!$email["email"]){
					$id_contact_facturation=ATF::societe()->select($infos["id_societe"],"id_contact_facturation");
					if($id_contact_facturation){
						if(!$recipient=ATF::contact()->select($id_contact_facturation,"email")){
							ATF::db($this->db)->rollback_transaction();
							throw new errorATF("Il n'y a pas d'email pour ce contact",166);
						}
					}else{
						ATF::db($this->db)->rollback_transaction();
						throw new errorATF("Il n'y a pas d'email pour ce contact",166);
					}
				}else{
					$recipient = $email["email"];
				}
				$facture = ATF::facture()->select($last_id);
				$from = ATF::user()->select(ATF::$usr->getID(),"email");

				$info_mail["objet"] = "Votre Facture référence : ".$facture["ref"];
				$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".$from.">";
				$info_mail["html"] = false;
				$info_mail["template"] = 'devis';
				$info_mail["texte"] = $email["texte"];
				$info_mail["recipient"] = $recipient;
				//Ajout du fichier
				$path = $this->filepath($last_id,"fichier_joint");

				$this->facture_mail = new mail($info_mail);
				$this->facture_mail->addFile($path,$facture["ref"].".pdf",true);
				$this->facture_mail->send();

				if($email["emailCopie"]){
					$info_mail["recipient"] = $email["emailCopie"];
					$this->facture_copy_mail = new mail($info_mail);
					$this->facture_copy_mail->addFile($path,$facture["ref"].".pdf",true);
					$this->facture_copy_mail->send();
				}
			}


			if(ATF::affaire()->select($infos["id_affaire"] , "nature") == "consommable"){
				ATF::devis()->q->reset()->where("id_affaire", $infos["id_affaire"])
										->where("etat", "gagne");
				$devis = ATF::devis()->select_row();

				$duree = $devis["duree_contrat_cout_copie"];
				$nb_facture = intval($duree/3)-1;

				ATF::facture()->q->reset()->where("id_affaire",$infos["id_affaire"])
										  ->setCountOnly();
				$nb = 	ATF::facture()->select_row();
				if($nb_facture == $nb){
					//C'est l'avant derniere facture, il faut envoyer une alerte car le contrat arrive a echeance !
					ATF::tache()->insert(array("id_societe"=>$infos["id_societe"],
											   "id_user"=>54,
											   "tache"=>"Attention, echeance du contrat ".ATF::affaire()->select($infos["id_affaire"], "affaire")." proche",
											   "priorite"=>"grande",
											   "horaire_fin"=>date("Y-m-d",strtotime("+3 month")),
											   "no_redirect"=>true
											  )
										);
				}
			}
			ATF::db($this->db)->commit_transaction();

			// if($echeancier){
			// 	$pdf_binaire = ATF::pdf()->generic("facture",$last_id,true,$s,false);
			// 	return $pdf_binaire;
			// }
		}

		ATF::affaire()->redirection("select",$infos["id_affaire"]);

		return $this->cryptId($last_id);
	}

	/**Retourne le nombre de jours entre 2 dates
	 * @param $date1 date de début
	 * @param $date2 date de fin
	 * @return int nombre de jours entre 2 dates
	 */
	 public function date_diff($date1, $date2)
	{
		 $s = strtotime($date2)-strtotime($date1);
		 $d = intval($s/86400)+1;
		 return "$d";
	}

	/**
	 * Retourne false car impossibilité si facture accompte
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @return boolean
	 */
	public function can_update($id,$infos=false){
		if (!$infos) $infos = $this->select($id);
		if($infos["type_facture"]!="acompte" && $infos["type_facture"]!="solde"){
			return true;
		} else {
			throw new errorATF("Il est impossible de modifier une facture d'accompte ou de solde.",893);
		}
	}

	/**
	* Surcharge de l'insert afin d'insérer les lignes de factures et modifier l'état de l'affaire sur l'insert d'une facture
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function update($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
 		$preview=$infos["preview"];
		$type_check=$infos[$this->table]["mode"];
		$this->infoCollapse($infos);

		// On calcul le prix par rapport aux lignes
		foreach($infos_ligne as $key=>$item){

			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("facture_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}

			if(!$item["quantite"]) $item["quantite"]=0;

			$prixFinal += $item["prix"]*$item["quantite"];
		}

		$prixFinal += $infos["frais_de_port"];

		/*Formatage des numériques*/
		$infos["prix"]=util::stringToNumber($prixFinal);
		$infos["frais_de_port"]=util::stringToNumber($infos["frais_de_port"]);

		if($infos["emailTexte"]){
			$email["email"]=$infos["email"];
			$email["emailCopie"]=$infos["emailCopie"];
			$email["texte"]=$infos["emailTexte"];
		}else{
			$email=false;
		}

		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"],$infos["marge"],$infos["prix_achat"],$infos["sous_total"],$infos["mode"],$infos["acompte_pourcent"],$infos["id_commande"],$infos["finale"],$infos["preview"],$infos["marge_absolue"]);
		$societe=ATF::societe()->select($infos["id_societe"]);

		if($type_check=="avoir"){
			if($infos["prix"] > 0){
				$infos["prix"]=0-$infos["prix"];
			}
			$infos["type_facture"]="avoir";
			if(!$infos["id_facture_parente"]){
				throw new errorATF("Pour un avoir, il est obligatoire de renseigner la facture parente",170);
			}
		}elseif($type_check=="factor"){
			$infos["type_facture"]="factor";
			if(!$societe["rib_affacturage"] || !$societe["iban_affacturage"] || !$societe["bic_affacturage"]){
				throw new errorATF("Il manque l'une de ces informations pour la société ".$societe["societe"]." : RIB, IBAN, BIC",167);
			}
		}

		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

			//Facture

			if($infos["periodicite"]){
				$date = $infos["date_debut_periode"];
				$date = explode("-", $date);

				if(!$infos["date_debut_periode"] || !$infos["date"]){
					ATF::db($this->db)->rollback_transaction();
					throw new errorATF(ATF::$usr->trans("Il faut remplir la date (date d'édition) et la date de début de période pour une facture périodique"),175);
				}

				$total = 0;
				$dateFacture = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[0], $date[2]));

				switch ($infos["periodicite"]) {
	 				case 'mensuelle':
						$mois = mktime( 0, 0, 0, $date[1], 1, $date[2] );
						$dateFin = date("Y-m-d", mktime(0, 0, 0, $date[1], date('t',$mois), $date[2]));

						$dateDeb = date("Y-m-d" , $mois);

						$nbJoursMois = $this->date_diff($dateDeb, $dateFin);
						if($infos["date_fin_periode"]){
							$nbJoursAVenir = $this->date_diff($dateFacture, $infos["date_fin_periode"]);
						}else{
							$nbJoursAVenir = $this->date_diff($dateFacture, $dateFin);
						}

						if($nbJoursMois !== $nbJoursAVenir){
							$remise = $nbJoursAVenir-1;
							$item = array();
							$item["ref"] = "PRORATA";
						    $item["produit"] = "Remise prorata temporis sur un mois : ".($nbJoursMois-$remise)." jours sur ".$nbJoursMois." jours";
						    $item["quantite"] = 1;
						    $item["prix"] = $infos["prix"]*($nbJoursMois-$remise)/$nbJoursMois*-1;
						    $item["id_compte_absystech"] = "";
						    $item["id_facture"] = $this->decryptId($infos['id_facture']);

							$additionnalLines[] = $item;

							$infos["prix"] += $item["prix"];
						}

					break;

					case 'trimestrielle':
						if(intval($date[1]) <= 3){
							$mois = mktime( 0, 0, 0, 3, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  1, 1, $date[2])); // 01-01-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 3, date('t',$mois), $date[2]));

						}
						elseif(intval($date[1]) <= 6) {
							//01 Avril - 30 Juin
							$mois = mktime( 0, 0, 0, 6, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  4, 1, $date[2])); // 01-04-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 6, date('t',$mois), $date[2]));

						}
						elseif(intval($date[1]) <= 9) {
							//01 Juillet - 30 Septembre
							$mois = mktime( 0, 0, 0, 9, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  7, 1, $date[2])); // 01-07-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 9, date('t',$mois), $date[2]));
						}
						elseif(intval($date[1]) <= 12) {
							//01 Octrobre - 31 Decembre
							$mois = mktime( 0, 0, 0, 12, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  10, 1, $date[2])); // 01-10-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 12, date('t',$mois), $date[2]));

						}
						$nbJoursTrimestre = $this->date_diff($dateDeb, $dateFin);
						if($infos["date_fin_periode"]){
							$nbJoursAVenir = $this->date_diff($dateFacture, $infos["date_fin_periode"]);
						}else{
							$nbJoursAVenir = $this->date_diff($dateFacture, $dateFin);
						}

						if($nbJoursTrimestre !== $nbJoursAVenir){
							$remise = $nbJoursAVenir-1;
							$item = array();
							$item["ref"] = "PRORATA";
						    $item["produit"] = "Remise prorata temporis sur un trimestre : ".($nbJoursTrimestre-$remise)." jours sur ".$nbJoursTrimestre." jours";
						    $item["quantite"] = 1;
						    $item["prix"] = $infos["prix"]*($nbJoursTrimestre-$remise)/$nbJoursTrimestre*-1;
						    $item["id_compte_absystech"] = "";
						    $item["id_facture"] = $this->decryptId($infos['id_facture']);
							$additionnalLines[] = $item;

							$infos["prix"] += $item["prix"];
						}


					break;

					case 'semestrielle' :

						//01 Janvier au 30 Juin
						if(intval($date[1]) <= 6){
							$mois = mktime( 0, 0, 0, 6, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  1, 1, $date[2])); // 01-01-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 6, date('t',$mois), $date[2]));

						}//01 Juillet au 31 Decembre
						else{
							$mois = mktime( 0, 0, 0, 12, 1, $date[2] );
							$dateDeb = date("Y-m-d" , mktime(0, 0, 0,  7, 1, $date[2])); // 01-01-anneeFacture
							$dateFin = date("Y-m-d", mktime(0, 0, 0, 12, date('t',$mois), $date[2]));
						}

						$nbJoursSemestre = $this->date_diff($dateDeb, $dateFin);
						if($infos["date_fin_periode"]){
							$nbJoursAVenir = $this->date_diff($dateFacture, $infos["date_fin_periode"]);
						}else{
							$nbJoursAVenir = $this->date_diff($dateFacture, $dateFin);
						}


						if($nbJoursSemestre !== $nbJoursAVenir){
							$remise = $nbJoursAVenir-1;
							$item = array();
							$item["ref"] = "PRORATA";
						    $item["produit"] = "Remise prorata temporis sur un semestre : ".($nbJoursSemestre-$remise)." jours sur ".$nbJoursSemestre." jours";
						    $item["quantite"] = 1;
						    $item["prix"] = $infos["prix"]*($nbJoursSemestre-$remise)/$nbJoursSemestre*-1;
						    $item["id_compte_absystech"] = "";
						    $item["id_facture"] = $this->decryptId($infos['id_facture']);
							$additionnalLines[] = $item;

							$infos["prix"] += $item["prix"];
						}

					break;

					case 'annuelle':
						$mois = mktime( 0, 0, 0, 12, 1, $date[2] );
						$dateDeb = date("Y-m-d", mktime(0, 0, 0, 1, 1, $date[2]));
						$dateFin = date("Y-m-d", mktime(0, 0, 0, 12,  date('t',$mois), $date[2]));

						$nbJoursAnnee = $this->date_diff($dateDeb, $dateFin);
						if($infos["date_fin_periode"]){
							$nbJoursAVenir = $this->date_diff($dateFacture, $infos["date_fin_periode"]);
						}else{
							$nbJoursAVenir = $this->date_diff($dateFacture, $dateFin);
						}

						if($nbJoursAnnee !== $nbJoursAVenir){
							$remise = $nbJoursAVenir-1;
							$item = array();
							$item["ref"] = "PRORATA";
						    $item["produit"] = "Remise prorata temporis sur une année : ".($nbJoursAnnee-$remise)." jours sur ".$nbJoursAnnee." jours";
						    $item["quantite"] = 1;
						    $item["prix"] = $infos["prix"]*($nbJoursAnnee-$remise)/$nbJoursAnnee*-1;
						    $item["id_compte_absystech"] = "";
						    $item["id_facture"] = $this->decryptId($infos['id_facture']);
							$additionnalLines[] = $item;

							$infos["prix"] += $item["prix"];
						}
					break;

				}

				$infos["date_fin_periode"] = $dateFin;

				//Avant de modifier la date de fin de maintenance d'une affaire, il faut verifier si la date à modifier est < à la date de fin de la facture que l'on viens de modifier
				ATF::affaire()->q->reset()->addField("affaire.date_fin_maintenance")->where("affaire.id_affaire",$infos["id_affaire"]);
				$affaire = ATF::affaire()->select_row();

				if($affaire["affaire.date_fin_maintenance"]){
					$nbJoursDiff = $this->date_diff($affaire["affaire.date_fin_maintenance"] , $dateFin);
					if($nbJoursDiff > 0){
						ATF::affaire()->u(array("id_affaire" => $infos["id_affaire"], "date_fin_maintenance" => $dateFin));
					}
				}else{
					ATF::affaire()->u(array("id_affaire" => $infos["id_affaire"], "date_fin_maintenance" => $dateFin));
				}
			}

			if($infos["type_facture"]=="facture" && $infos["id_termes"] === NULL){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("Vous devez spécifier les termes",167);
			}

			parent::update($infos,$s);

			ATF::facture_ligne()->q->reset()
								   ->addCondition("id_facture",$this->decryptId($infos["id_facture"]));

			$facture_ligne=ATF::facture_ligne()->select_all();
			$facture_ligne_before = $facture_ligne;
			foreach($facture_ligne as $key=>$item){
				ATF::facture_ligne()->delete(array("id"=>$item["id_facture_ligne"]));
			}

			$facture_ligne_after = $infos_ligne;
			//Facture Ligne
			foreach($infos_ligne as $key=>$item){
				foreach($item as $k=>$i){
					$k_unescape=util::extJSUnescapeDot($k);
					$item[str_replace("facture_ligne.","",$k_unescape)]=$i;
					unset($item[$k]);
				}

				$item["id_fournisseur"]=$item["id_fournisseur_fk"];
				$item["id_compte_absystech"]=$item["id_compte_absystech_fk"];
				unset($item["id_fournisseur_fk"],$item["id_compte_absystech_fk"],$item["marge"],$item["marge_absolue"]);
				$item["id_facture"]=$infos["id_facture"];
				$item["index"]=util::extJSEscapeDot($key);
				if(!$item["quantite"]) $item["quantite"]=0;
				ATF::facture_ligne()->q->reset();
				ATF::facture_ligne()->i($item,$s);
			}

			foreach ($additionnalLines as $k=>$i) {
				ATF::facture_ligne()->i($i,$s);
			}



		//***************************************************************************************

		if($preview){
			$this->move_files($infos["id_facture"],$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($infos["id_facture"]);
		}else{
			$this->move_files($infos["id_facture"],$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base

			/* MAIL */
			if($email){
				if(!$email["email"]){
					$id_contact_facturation=ATF::societe()->select($infos["id_societe"],"id_contact_facturation");
					if(!$recipient=ATF::contact()->select($id_contact_facturation,"email")){
						ATF::db($this->db)->rollback_transaction();
						throw new errorATF("Il n'y a pas d'email pour ce contact",166);
					}
				}else{
					$recipient = $email["email"];
				}
				$facture = ATF::facture()->select($infos["id_facture"]);
				$ref_facture = $facture["ref"];
				$from = ATF::user()->select(ATF::$usr->getID(),"email");

				$info_mail["objet"] = "Votre Facture référence : ".$ref_facture;
				$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".$from.">";
				$info_mail["html"] = false;
				$info_mail["template"] = 'devis';
				$info_mail["texte"] = $email["texte"];
				$info_mail["recipient"] = $recipient;
				//Ajout du fichier
				$path = $this->filepath($infos["id_facture"],"fichier_joint");

				$this->facture_mail = new mail($info_mail);
				$this->facture_mail->addFile($path,$infos["ref"].".pdf",true);
				$this->facture_mail->send();

				if($email["emailCopie"]){
					$info_mail["recipient"] = $email["emailCopie"];
					$this->facture_copy_mail = new mail($info_mail);
					$this->facture_copy_mail->addFile($path,$infos["ref"].".pdf",true);
					$this->facture_copy_mail->send();
				}
			}

			ATF::db($this->db)->commit_transaction();
		}
		$id_affaire=$this->select($infos["id_facture"],"id_affaire");
		ATF::affaire()->redirection("select",$id_affaire);

		return true;
	}


	/**
	* Surcharge de delete afin de supprimer les lignes de commande et modifier l'état de l'affaire et du devis
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
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

				//Commande
				ATF::commande_facture()->q->reset()->addCondition("id_facture",$id)->end();
				if($tab_commande=ATF::commande_facture()->select_all()){
					//Pour toutes les commandes liées à la facture
					foreach($tab_commande as $key=>$item){
						$commande["commande"]["id_commande"]=$item["id_commande"];
						$commande["commande"]["etat"]="en_cours";
						ATF::commande()->u($commande,$s);
					}
				}

				//Facture
				parent::delete($id,$s);

				//On recupere les factures précédente pour récuperer la date de fin la plus proche
				$this->q->reset()->where("facture.id_affaire", $facture["id_affaire"])->addField("facture.date_fin_periode");
				$factures = $this->select_all();

				//Si pas d'autre facture la date de fin de période devient NULL

				if(is_array($factures)){
					$dateFin = "";
					foreach($factures as $k=>$v){
						if($v["facture.date_fin_periode"]){
							$nbJours = ATF::facture_absystech()->date_diff($dateFin, $v["facture.date_fin_periode"]);
							 if(($dateFin === "") || ($dateFin === NULL)){
								$dateFin = $v["facture.date_fin_periode"];
							}
							else{
								if($nbJours > 1){
									$dateFin = $v["facture.date_fin_periode"];
								}
							}
						}
					}
					ATF::affaire()->u(array("id_affaire" => $facture["id_affaire"], "date_fin_maintenance" => $dateFin));
				}else{
					ATF::affaire()->u(array("id_affaire" => $facture["id_affaire"], "date_fin_maintenance" => NULL));
				}


				//Affaire
				$this->q->reset()->addCondition("id_affaire",$facture["id_affaire"])->SetCount()->end();
				$autre_affaire=parent::sa();

				//S'il n'y a pas d'autres factures pour cette affaire
				if($autre_affaire["count"]==0){
					$affaire["id_affaire"]=$facture["id_affaire"];
					ATF::devis()->q->reset()->addCondition("id_affaire",$facture["id_affaire"])->SetCount()->end();
					$devis = ATF::devis()->sa();
					ATF::commande()->q->reset()->addCondition("id_affaire",$facture["id_affaire"])->SetCount()->end();
					$commande=ATF::commande()->sa();

					//S'il y a au moins une commande pour cette affaire
					if($commande["count"]>0){
						$affaire["etat"]="commande";
						ATF::affaire()->u($affaire,$s);
					//S'il y a au moins un devis
					}elseif($devis["count"]>0){
						$affaire["etat"]="devis";
						$affaire["forecast"]="20";
						ATF::affaire()->u($affaire,$s);
					//Sinon on peut tout supprimer
					}else{
						ATF::affaire()->delete($affaire["id_affaire"],$s);
						unset($facture["id_affaire"]);
					}
				}

				ATF::db($this->db)->commit_transaction();
				//*****************************************************************************

				if($facture["id_affaire"]){
					ATF::affaire()->redirection("select",$facture["id_affaire"]);
				}else{
					$this->redirection("select_all",NULL,"facture.html");
				}
		} elseif (is_array($infos) && $infos) {
            foreach($infos["id"] as $key=>$item){
                $this->delete($item,$s,$files,$cadre_refreshed);
            }
        }

		return true;
	}

	/**
	* Retourne un tableau pour le graphe d'affaire, dans statistique
	* @author DEV <dev@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Fanny DECLERCK  <fdeclerck@absystech.fr> (pour la requête de base ensuite transformée en querier)
	* @param array $liste_annees
	* @return array
	*/
	public function stats_CA($liste_annees){
		$this->q->reset();
		$this->q->addField("YEAR(`facture`.`date`)","year")
				->addField("MONTH(`facture`.`date`)","month")
				->addField("SUM(`facture`.`prix`)","nb");
		/*foreach($liste_annees as $key_list=>$item_list){
			//if($item_list)$this->q->addCondition("YEAR(`facture`.`date`)",$key_list);
			if($item_list){
				ATF::stats()->conditionYear($this->q,"`facture`.`date`",$key_list);
			}
		}*/
		ATF::stats()->conditionYear($liste_annees,$this->q,"`facture`.`date`");

		$this->q->addGroup("year")->addGroup("month");
		return parent::select_all();
	}

	/**
	* Permet de connaître toutes les factures d'une commande, en acompte ou pas
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_commande
	* @return array
	*/
	public function facture_by_commande($id_commande,$acompte=false){
		$this->q->reset()
			 ->addJointure("facture","id_facture","commande_facture","id_facture")
			 //->addField("SUM(facture.prix)","prix")
			 ->addCondition("id_commande",$id_commande);
			 //->setDimension("row")->end();
		if($acompte){
			$this->q->addCondition("type_facture","acompte");
		}
		$facture_by_commande=parent::select_all();
		if(is_array($facture_by_commande)){
			$prix = 0;
			foreach($facture_by_commande as $k=>$v){
				$this->q->reset()->where("id_facture_parente",$v["id_facture"]);
				$res = $this->select_all();
				if(!is_array($res)){
					$prix += $v["prix"];
				}
			}
			return array("prix" => $prix);
		}else{
			return false;
		}

		/*if($facture_by_commande["prix"]){
			return $facture_by_commande;
		}*/
	}

	/**
	* Renvoi le montant total de la ou des factures associé a une commande.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id_commande
	* @return float
	*/
	public function total_by_commande($id_commande){
		$this->q->reset()
			 ->addJointure("facture","id_facture","commande_facture","id_facture")
			 //->addField("SUM(facture.prix)","prix")
			 ->addCondition("id_commande",$id_commande);
			 //->setDimension("row")->end();

		$facture_by_commande=parent::select_all();
		if(is_array($facture_by_commande)){
			$prix = 0;
			foreach($facture_by_commande as $k=>$v){
				$prix += $v["prix"];
			}
			return $prix;
		}else{
			return 0.00;
		}

	}

	/**
	* Retourne false car suppression de facture impossible sauf si etat impayee ou pas derniere du mois
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @return boolean
	*/
	public function can_delete($id){
		if($this->select($id,"etat")=="impayee"){
			if(!$this->isLastOfMonth($id)){
				throw new errorATF("Il est impossible de supprimer cette facture car elle n'est pas la derniere du mois",893);
			}
			else{
				return true;
			}
		}
		else{
			throw new errorATF("Il est impossible de supprimer cette facture car elle est payée",892);
		}
	}

	/**
	* Statistiques : nombre de factures restantes à payer
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array session
	* return enregistrements
	*/
	public function stats($stats=false,$type=false,$widget=false) {
		switch ($type) {
			case "resteAPayer":
				$this->q->reset()->addField("DISTINCT(DATE_FORMAT(facture.date,  '%Y'))", "annee")->where("facture.etat", "impayee")->addGroup("annee");
				$annees = $this->sa();
				$years = array();
				foreach ($annees as $key => $value) {
					$years[$value["annee"]] = 1;
				}
				$this->q->reset();
				//on récupère la liste des années que l'on ne souhaite pas voir afficher sur les graphes
				//on les incorpore ensuite sur les requêtes adéquates


				foreach($years  as $key_list=>$item_list){
					if($item_list)$this->q->addCondition("YEAR(`date`)",$key_list);
				}

				$this->q->addField("YEAR(`date`)","year")
						->addField("MONTH(`date`)","month")
//						->addField("FLOOR(MONTH(`date`)/3)*3","month")
						->addField("SUM(`prix`)","nb")
						->addCondition("etat","impayee")
						->addGroup("year")
						->addGroup("month");
				$stats['DATA'] = parent::select_all();

				$this->q->reset("field,group");
				$this->q->addField("DISTINCT YEAR(`date`)","year");
				$stats['YEARS'] =parent::select_all();

//				foreach (util::month() as $k => $i) {
//					if (((int)($k)-1)%3==0) {
//						$Q = floor(((int)($k))/3)+1;
//						$stats['categories']["category"][] = array("label"=>"Q".$Q,"hoverText"=>$i);
//					}
//				}
				return parent::stats($stats,$type,$widget);

			case "top10negatif":
				$result=ATF::societe()->solde_total_global();
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
					//$i["societe"] = str_replace("'", " ", $i["societe"]);
					$graph['dataset']["solde"]['set'][$i["id_societe"]] = array(
						"value"=>$i['soldeTotal']
						,"alpha"=>100
						,"titre"=> $i["societe"]
						,"color"=>$i["credits"]<-100?"FF0033":($i["soldeTotal"]<-10?"FF6600":"FFFF00")
						,"link"=>urlencode("societe-select-".classes::cryptId($i["id_societe"]).".html")
					);
//					if (count($graph['dataset']["solde"]['set'])==10) {
//						break; // Pas plus de 10 sur le widget
//					}
				}
				return $graph;

			default:
				return parent::stats($stats,$type,$widget);
		}
	}

	/** Permet d'envoyer un mail au regénérateur pour qu'il garde une trace de la facture supprimée
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @return void
	*/
	function generatePDF($infos,&$s,$preview=false){

		$facture=$this->select($infos["id"]);
		$path = $this->filepath($infos["id"],"fichier_joint",$preview);
		if (file_exists($path)) {
			//Envoi d'un mail
			$info_mail["objet"] = "Facture avant régénération : ".$facture["ref"];
			$info_mail["from"] = ATF::user()->nom(ATF::$usr->getID())." <".ATF::$usr->get('email').">";
			$info_mail["html"] = false;
			$info_mail["template"] = 'devis';
			$info_mail["texte"] = "Facture de sauvegardre avant régénération : ".$facture["ref"]." (".$infos["id"].")";
			$info_mail["recipient"] = ATF::$usr->get('email');

			//Ajout du fichier
			$mail = new mail($info_mail);
			$mail->addFile($path,$infos["ref"].".pdf",true);
			$mail->send();

			ATF::$msg->addNotice(ATF::$usr->trans("email_sauvegarde_old_facture_send",$this->table));
		} else {
			ATF::$msg->addWarning(ATF::$usr->trans("email_sauvegarde_old_facture_doesnt_exist",$this->table));
		}

		$this->move_files($infos["id"],$s,false);

		ATF::$msg->addNotice(ATF::$usr->trans("facture_regenere_avec_succes",$this->table));
		$this->redirection("select",$infos["id"]);
		return true;
	}

	/**
    * Retourne la TVA pour une facture
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @return int
    */
	public function getTVA($id_societe=false){
		$societe=ATF::societe()->select($id_societe);
		$adrr_FR = (($societe["facturation_id_pays"]=="FR" && $societe["facturation_adresse"]) || (!$societe["facturation_adresse"] && $societe["id_pays"]=="FR"));
		$TVA_FR = substr($societe["reference_tva"],0,2)=="FR";
		if ($TVA_FR || !$societe["reference_tva"] && $adrr_FR) {
			return  __TVA__;
		} else {
			return 1;
		}
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @return string
    */
	public function default_value($field){
		if(/*$field!="mode" &&*/ $field!="acompte_pourcent" && $field!="emailCopie"){
			if(ATF::_r('id_facture')){
				$infos=ATF::facture()->select(ATF::_r('id_facture'));
				if($field=="prix_achat" || $field=="marge" || $field=="marge_absolue"){
					ATF::facture_ligne()->q->reset()
										->addCondition("id_facture",ATF::facture_ligne()->decryptId(ATF::_r('id_facture')))
										->addField("SUM(`prix_achat` * `quantite`)","prix_achat")
										->setStrict()
										->setDimension('cell');

					$prix_achat=ATF::facture_ligne()->select_all();
				}
			}elseif(ATF::_r('id_commande')){
				$infos=ATF::commande()->select(ATF::_r('id_commande'));
				if($field=="prix_achat" || $field=="marge" || $field=="marge_absolue"){
					$prix_achat=$infos["prix_achat"];
				}
			}

			if($field=="id_societe" || $field=="email" || $field=="tva"){
				if($infos["id_societe"]){
					$id_societe=$infos["id_societe"];
				}elseif(ATF::_r('id_affaire')){
					$affaire=ATF::affaire()->select(ATF::_r('id_affaire'));
					$id_societe=$affaire["id_societe"];

				}elseif(ATF::_r('id_societe')){
					$id_societe=ATF::_r('id_societe');
				}
			}
		}



		switch ($field) {
			case "id_societe":
				if ($id_siege = ATF::societe()->select($id_societe,"id_filiale")) {
					return $id_siege;
				} else {
					return $id_societe;
				}
			case "id_affaire":
				return $infos["id_affaire"];
			case "tva":
				return $this->getTVA($id_societe);
			case "mode":
				return $infos['type_facture']?$infos['type_facture']:"facture";
			case "acompte_pourcent":
				return 100;
			case "date":
				return date("Y-m-d");
			case "prix":
				return $infos["prix"];
			case "frais_de_port":
				return $infos["frais_de_port"];
			case "sous_total":
				return $infos["prix"]-$infos["frais_de_port"];
			case "prix_achat":
				return $prix_achat;
			case "emailCopie":
				return ATF::$usr->get("email");
			case "marge":
				return round((($infos["prix"]-$prix_achat)/$infos["prix"])*100,2)."%";
			case "marge_absolue":
				return ($infos["prix"]-$infos["frais_de_port"])-$prix_achat;
			case "email":
				if($id_societe){
					if($id_contact_facturation=ATF::societe()->select($id_societe,"id_contact_facturation")){
						return ATF::contact()->select($id_contact_facturation,"email");
					}else{
						return false;
					}
				}else{
					return false;
				}
			case "id_termes":
				if(ATF::_r('id_affaire')){
					$affaire=ATF::affaire()->select(ATF::_r('id_affaire'));
					$id_termes=$affaire["id_termes"];
				}elseif(ATF::_r('id_societe')){
					$societe = ATF::societe()->select(ATF::_r('id_societe'));
					$id_termes = $societe["id_termes"];
				}elseif(ATF::_r('id_commande')){
					$affaire=ATF::affaire()->select( ATF::commande()->select(ATF::commande()->decryptId(ATF::_r('id_commande')) , "id_affaire"));
					$id_termes=$affaire["id_termes"];
				}else{
					$id_termes = NULL;
				}

				return $id_termes;
			default:
				return parent::default_value($field);
		}
	}

//	/**
//	* Donne le mail actuel
//	* @return mixed
//	*/
//	public function getCurrentMail(){
//		//Current mail
//		if(!$this->current_mail) throw new errorATF(ATF::$usr->trans("null_current_mail",$this->table));
//		return $this->current_mail;
//	}
//
//	/**
//	* Initialise le mail courant
//	* @param string $mail le nom du mail courant
//	*/
//	public function setCurrentMail($mail){
//		$this->current_mail=&$this->$mail;
//	}

	/**
    * Select classique qui ne prend pas en compte certaines données lors du cloner
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int id
	* @param string field
	* @return array
    */
	public function select($id,$field=NULL) {
		$facture=parent::select($id,$field);
		if((ATF::_r("event")=="cloner") && is_array($facture)){
			$facture["date"]="";
			$facture["date_previsionnelle"]="";
			$facture["date_effective"]="";
			$facture["date_relance"]="";
		}
		return $facture;
	}

	/**
	* Pour les autocomplete, retourne une conditions au format URL   arg1=2&arg2=3...
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $class Classe des enregistrements affichés dans l'autocomplète
	* @param array $infos ($requests habituellement attendu)
	*	int $infos[id_affaire]
	*	int $infos[id_societe]
	* @param string $condition_field
	* @param string $condition_value
	* @return array Conditions de filtrage
	*/
	public function autocompleteConditions(classes_optima $class,$infos,$condition_field=NULL,$condition_value=NULL) {
		$this->infoCollapse($infos);
		switch ($class->table) {
			case "affaire":
				if ($infos["id_societe"]) {
					$conditions["condition_field"][] = "facture.id_societe";
					$conditions["condition_value"][] = $infos["id_societe"];
				}
			break;
		}
		return array_merge_recursive((array)($conditions),parent::autocompleteConditions($class,$infos,$condition_field,$condition_value));
	}


	/**
	 * Filtrage d'information selon le profil
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 */
	protected function saFilter(){
		if (ATF::$usr->get("id_profil")==11) {
			// Profil apporteur d'affaire
			$this->q
				->from("facture","id_affaire","affaire","id_affaire")
				->where("affaire.id_commercial",ATF::$usr->getID());
		}
	}


	/*
	* Méthode qui prépare le pager
	* @author Mathieu mtribouillard <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @param decimal $montant
	* @return object $q
	*/

	public function rapprochementFacture($id_societe,$montant=false){

		//Les champs que l'on souhaite afficher dans le grid
		$fields=array(
						0=>"ref",
						1=>"prix"
					);

		$q = ATF::_s("pager")->getAndPrepare("Factures");
		$q->reset()
		  ->setView(array("order"=>$fields))
		  ->addCondition("id_societe",$id_societe)
		//On ajoute id_societe pour pouvoir le retouver lors de rapprochementFactureSA()
		  ->addValue("id_societe",$id_societe);

		$group=ATF::societe()->getGroup($id_societe);
		foreach($group as $item){
			$q->addCondition("id_societe",$item["id_societe"]);
		}

		//On cherche dans une fourchette de +/- 2 €
		if($montant!="NaN"){
			$q->addConditionBetween("prix",($montant-2)." AND ".($montant+2),"OR",false,'BETWEEN')
			  ->addValue("montant",$montant);
		}

		$q->end();

		return $q;
	}

	/*
	* Exeécution du querier du pager
	* @author Mathieu mtribouillard <mtribouillard@absystech.fr>
	* @return array $data
	*/

	public function rapprochementFactureSA(){

		//Pas de limite
		$this->q->setLimit(-1)->end();

		//Facture correspondante
		$data=parent::sa();

		//Si montant on doit faire un test pour les combinaisons de prix de facture
		if($values=$this->q->getValues()){
			$montant=$values["montant"];

			//On récupère les factures ayant un prix < montant (évidemment il ne peut pas y avoir un élément d'une somme qui est > au montant recherché)
			$this->q
				 ->reset()
				 ->addCondition("id_societe",$values["id_societe"])
				 ->addCondition("prix",($montant+2),false,false,"<=")
//				 ->addCondition("etat","impayee")
				 ->addOrder("prix");

			$dataToutes=parent::sa();
			foreach($dataToutes as $key=>$item){
				$tabId_facture=array();
				$tabId_facture[]=$item["id_facture"];
				$prix=$item["prix"];
				//Pour chaque facture on teste toutes les combinaisons possibles
				$this->incremanteFacture($dataToutes,$prix,$tabId_facture,$montant,$data);
			}
		}
		$dataRapprochementFactureSA["count"]=count($data["data"]);
		$dataRapprochementFactureSA["data"]=$data["data"];

		return $dataRapprochementFactureSA;
	}

	/*
	* Permet de tester toutes les comnaisons existantes pour un montant
	* @author Mathieu mtribouillard <mtribouillard@absystech.fr>
	* @param array $dataToutes toutes les factures de cette société qui ont un prix inférieur au montant recherché
	* @param decimal $prix prix de la facture mère (1er élément de la combinaison)
	* @param array $tabId_facture tableau des factures qui forme la combinaison
	* @param decimal $montant montant recherché
	* @array data $tableau de facture retourné
	*/
	public function incremanteFacture($dataToutes,$prix,$tabId_facture,$montant,&$data){
		foreach($dataToutes as $k=>$i){
			unset($dataToutes[$i]);
			//Il ne faut pas que l'élément existe déjà dans la liste (on peut pas faire la somme de 2 fois la même facture...)
			if(!in_array($i["id_facture"],$tabId_facture)){
				//Si le prix est dans la fourchette alors il est bon
				if((($prix+$i["prix"])>=($montant-2)) && (($prix+$i["prix"])<=($montant+2))){
					$tabId_facture[]=$i["id_facture"];
					asort($tabId_facture);
					$nb=$prixTotal=0;
					$dataRetour=array();
					//On construit l'enregistrement avec toutes les factures de la combinaison
					foreach($tabId_facture as $key=>$Id_facture){
						$facture=ATF::facture()->select($Id_facture);
						$prixTotal+=$facture["prix"];
						if($nb>0){
							$dataRetour["ref"].=" ";
							$dataRetour["prix"].=" + ";
						}
						$nb++;
						$dataRetour["ref"].=$facture["ref"];
						$dataRetour["prix"].=$facture["prix"];
						$dataRetour["facture.id_facture"].=$Id_facture;
					}
					$dataRetour["prix"].=" = ".$prixTotal;

					//Si data existe il faut tester que cette combianaison n'existe pas déjà
					if($data["data"]){
						$exist=false;
						foreach($data["data"] as $ke=>$it){
							if($it["facture.id_facture"]==$dataRetour["facture.id_facture"]){
								$exist=true;
								continue;
							}
						}
						if(!$exist){
							$data["data"][]=$dataRetour;
						}
					}else{
						$data["data"][]=$dataRetour;
					}
					unset($tabId_facture[array_search($i["id_facture"],$tabId_facture)]);
					return true;
				//Si le prix est inférieur alors c'est que la facture peut être un élément d'une combinaison
				}elseif(($prix+$i["prix"])<($montant-2)){
					$tabId_facture[]=$i["id_facture"];
					$prix+=$i["prix"];
					//On essaye donc cette combinaison
					if(!$this->incremanteFacture($dataToutes,$prix,$tabId_facture,$montant)){
						//S'il n'y ap as d'autres factures c'est que cette combinaison est inexploitable et il faut retirer les factures insérés
						$prix-=$i["prix"];
						unset($tabId_facture[array_search($i["id_facture"],$tabId_facture)]);
					}
				}else{
					return false;
				}
			}
		}
		return false;
	}
	/*
	* Permet de savoir si la date de la facture est la derniere du mois
	* @author Morgan FlLEURQUIN <mfleurquin@absystech.fr>
	* @param int id_facture l'id de la facture
	* @return boolean true si c'est la derniere du mois | false sinon
	*/
	public function isLastOfMonth($id_facture){
		//Recupere la date de notre facture
		$this->q->reset()->AddField("facture.ref")->where("facture.id_facture",$id_facture);
		$ref = $this->select_cell();

		//Recupere toute les factures du mois
		$this->q->reset()->AddField("facture.ref")->setLimit(1);
		$res = $this->select_row();

		$ref = substr($ref,3);
		$res = substr($res["facture.ref"],3);
		$ref = intval($ref);
		$res = intval($res);


		if($res === $ref){
			return true;
		}
		else {
			return false;
		}
	}

	/**
	* Module de generation de lettre de change
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function lettre_change($infos){
		$factures = explode(",",$infos["facture"]);
		if($factures[0]){
			// On retire les factures déja payées
			foreach ($factures as $key => $value) {
				if($this->select($value , "etat") == "payee"){
					unset($factures[$key]);
				}
			}
			ksort($factures);

			if($factures[0]){
				$id_societe = $this->select($factures[0] , "id_societe");

				if(!ATF::societe()->select($id_societe , "rib")){
					throw new errorATF("Il faut inserer un RIB pour la societe",167);
				}
				if(!ATF::societe()->select($id_societe , "banque")){
					throw new errorATF("Il faut inserer une banque pour la societe",167);
				}


				$file = "lettre_de_change";
				$infos["filestoattach"][$file] = "";
				$info = array("id_societe" => $id_societe, "factures" => $factures, "echeance" => $infos["date"]);
				$return = ATF::pdf()->generic("lettre_de_change",$info,true,$s,false);

				$tmpfname = tempnam("/tmp", "lettre");
				if(file_put_contents($tmpfname, $return)){
					$info_mail["objet"] = "lettre de change";
					$info_mail["texte"] = "test";
					$info_mail["template"] = 'lettre_change';
					$info_mail["recipient"] = ATF::user()->select(ATF::$usr->getID() , "email");
					$mail = new mail($info_mail);
					$mail->addFile($tmpfname,"lettre_de_change.pdf",false);
					$mail->send();
					return true;
				}
			}else{ throw new errorATF("Toutes les factures sont déja payées",167);	}
		}return false;
	}

	/**
	* Renvoi les avoir disponible pour une société en se basant sur une facture
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function autocompleteFactureAvoirDispo($infos,$reset=true) {
		$this->q->reset();
		if ($infos['id_facture']) {
			$facture = $this->select($infos['id_facture']);
			$societe = ATF::societe()->select($facture['id_societe']);
		}
		$this->q->reset()
				->addCondition("facture.type_facture","avoir")
				->addCondition("facture.etat","impayee");
		if ($soceite['id_societe']) {
			$this->q->addCondition('facture.id_societe',$societe['id_societe']);
		}

		return parent::autocomplete($infos,false);
	}


	/**
	* Permet de récupérer la liste des factures pour telescope
	* @package Telescope
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $get array Paramètre de filtrage, de tri, de pagination, etc...
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	//$order_by=false,$asc='desc',$page=false,$count=false,$noapplyfilter=false
	public function _GET($get,$post) {
		// Gestion du tri
		if (!$get['tri']) $get['tri'] = "facture.date";
		if (!$get['trid']) $get['trid'] = "desc";

		// Gestion du limit
		if (!$get['limit']) $get['limit'] = 30;

		// Gestion de la page
		if (!$get['page']) $get['page'] = 0;

		$colsData = array(
			"facture.id_facture"=>array("visible"=>false),
			"facture.id_societe"=>array("visible"=>false),
			"facture.ref"=>array(),
			"facture.date"=>array(),
			"facture.etat"=>array("visible"=>false),
			"facture.prix"=>array(),
			"facture.tva"=>array(),
			"facture.date_previsionnelle"=>array(),
			"facture.date_effective"=>array(),
			"facture.date_relance"=>array()
		);



		$this->q->reset();

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			$this->q->setSearch($get["search"]);
		}

		if ($get['id']) {
			$this->q->where("id_facture",$get['id'])->setLimit(1);
		} else {
			if ($get['id_societe']) {
				$this->q->where("facture.id_societe",$get['id_societe']);
			}
			$this->q->setLimit($get['limit']);
		}

		//$this->q->addOrder("facture.date","asc");

		switch ($get['tri']) {
			case 'id_societe':
				$get['tri'] = "facture.".$get['tri'];
			break;
		}

		if($get["filter"]){
			foreach ($get["filter"] as $key => $value) {
				if (strpos($key, 'facture') !== false) {
					$this->q->addCondition(str_replace("'", "",$key), str_replace("'", "",$value), "AND");
				}
			}
		}

		if ($get['tri'] != "prix_ttc") $get['tri'] = "facture.".$get['tri'];
		$this->q->addField($colsData);

		$this->q->from("facture","id_societe","societe","id_societe");

		$this->q->addField("ROUND(IF(facture.date_effective IS NOT NULL
								,0
								,IF(
									(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
									,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
									,(facture.prix*facture.tva)
								)),2)","solde")
			    ->addField("TO_DAYS(IF(facture.date_effective IS NOT NULL,facture.date_effective,NOW())) - TO_DAYS(facture.date_previsionnelle)","retard")
			    ->addField("IF(facture.etat!='perte'
							,IF((TO_DAYS(IF(facture.date_effective IS NULL,NOW(),facture.date_effective)) - TO_DAYS(facture.date_previsionnelle))>1
								,40+ ((((TO_DAYS(IF(facture.date_effective IS NULL,NOW(),facture.date_effective)) - TO_DAYS(facture.date_previsionnelle)) *0.048)/365)
								    *ROUND(IF(
										(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
										,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
										,facture.prix*facture.tva
									),2))
							,IF( ((((TO_DAYS(IF(facture.date_effective IS NULL,NOW(),facture.date_effective)) - TO_DAYS(facture.date_previsionnelle)) *0.048)/365)
								    *ROUND(IF(
										(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
										,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
										,facture.prix*facture.tva
									),2))>0
								, ((((TO_DAYS(IF(facture.date_effective IS NULL,NOW(),facture.date_effective)) - TO_DAYS(facture.date_previsionnelle)) *0.048)/365)
							    *ROUND(IF(
									(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
									,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
									,facture.prix*facture.tva
								),2))
								, 0 )
							)
						,0)","interet")
			    ->addGroup("facture.id_facture");

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
	        $return = $data['data'][0];
		} else {
			// Envoi des headers
			header("ts-total-row: ".$data['count']);
			header("ts-max-page: ".ceil($data['count']/$get['limit']));
			header("ts-active-page: ".$get['page']);

	        $return = $data['data'];
		}

		return $return;
	}

	public function _getFacture($get,$post, &$s) {
		ATF::facture_ligne()->q->reset()->addCondition("id_facture", $get["id_facture"]);
		$facture_ligne=ATF::facture_ligne()->select_all();
		$detail_facture = $this->select($get["id_facture"]);
		if ($detail_facture['id_affaire']) {
			ATF::affaire()->q->reset()->addCondition("affaire.id_affaire", $detail_facture['id_affaire']);
			$affaire= array_shift(ATF::affaire()->sa());
		} else {
			$affaire = false;
		}

		if ($detail_facture['id_societe']) {
			ATF::societe()->q->reset()->addCondition("id_societe", $detail_facture['id_societe']);
			$societe= array_shift(ATF::societe()->sa());
		} else {
			$societe = false;
		}

		return array(
			"facture"=> $detail_facture,
			"facture_ligne"=>$facture_ligne,
			"affaire" => $affaire,
			"societe" => $societe,
			"data" =>$this->getBase64Facture($get["id_facture"])
		);
	}

	public function _getFactureSociete($get,$post){
		$this->q->reset()->where("facture.id_societe",$post["id_societe"]);
		return $this->sa();
	}

	public function getBase64Facture($id) {
		$path = $this->filepath($id,"fichier_joint",$preview);
		if (file_exists($path)) {
			return base64_encode(file_get_contents($path));
		} else {
			return false;
		}

	}

	public function _graph_impaye($get,$post){

		$date = date("Y-m");
		$date_start = date("Y-m", strtotime("-1 year"));

		ATF::facture()->q->reset()->where("facture.etat","impayee")
								  ->addField("facture.date","date")
								  ->addOrder("facture.date");
		$fact = ATF::facture()->select_all();

		//Initialisation du tableau des mois
		for ($i=date("Y")-1; $i<=date("Y") ; $i++) {
			for($j=1;$j<=12;$j++){
				if($i == date("Y")){
					if($j<=date("n")) $evo[$i][$j] = 0;
				}else{
					$evo[$i][$j] = 0;
				}
			}
		}

		// On renseigne le tabeau
		$prix = 0;
		foreach ($fact as $key => $value) {
			$prix += number_format($value["prix_ttc"],0,",","");
			if(date("Y-m", strtotime($value["date"])) <= $date_start){
				$old[date("Y", strtotime($value["date"]))] += number_format($value["prix_ttc"],0,",","");
				$evo[date("Y")-1][1] = number_format($value["prix_ttc"],0,",","");
			}else{
				$evo[date("Y", strtotime($value["date"]))][date("n", strtotime($value["date"]))] = $prix;
			}
		}

		$start = 0;
		foreach ($old as $key => $value) {
			$old[$key] += $start;
			$start += $value;
		}

		$data = $old;

		foreach ($evo as $year => $mois) {
			foreach ($mois as $num_mois => $value) {
				if($value == 0) {
					if($num_mois == 1){
						$evo[$year][$num_mois] = $evo[$year-1][12];
					}else{
						$evo[$year][$num_mois] = $evo[$year][$num_mois-1];
					}
				}

				$data[$year][$num_mois] = $evo[$year][$num_mois];
			}
		}


		return $data;
	}


	public function _getImpaye($get,$post){
		if($get["filter"]["facture.id_societe"]) $id_societe =  $get["filter"]["facture.id_societe"];
		$get["filter"]= array();
		if($id_societe) $get["filter"]["facture.id_societe"] = $id_societe;
		$get["filter"]["facture.etat"] = "impayee";
		return $this->_GET($get,$post);
	}

	public function _getPaye($get,$post){
		if($get["filter"]["facture.id_societe"]) $id_societe = $get["filter"]["facture.id_societe"];
		$get["filter"]= array();
		if($id_societe) $get["filter"]["facture.id_societe"] = $id_societe;
		$get["filter"]["facture.etat"] = "payee";
		$get['limit'] = 15;
		$get['tri'] = "facture.date";
		$get['trid'] = "desc";
		return $this->_GET($get,$post);
	}

};

class facture_att extends facture_absystech {
	/**
	* Retourne le total des factures en attente de paiement
    * @author Yann GAUTHERON <ygautheron@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $annee
	* @param boolean $avant VRAI pour demander toutes les années avant celle du paramètre $annee
	* @return int
	*/
	public function getTotalImpayees($annee=0,$avant=false){
		$this->q->reset()
			->addCondition("etat","impayee")
			->setDimension('cell')
			->addField("SUM(prix)","nb");
		if ($annee) {
			if ($avant) {
				$this->q->addCondition("facture.date",$annee."-06-30","AND",false,"<=");
			} else {
				$this->q->addConditionBetween("facture.date",$annee."-07-01 AND ".($annee+1)."-06-30","OR",false,'BETWEEN');
			}
		}
		return parent::sa();
	}



};
class facture_wapp6 extends facture_absystech { };
class facture_demo extends facture_absystech { };
?>
