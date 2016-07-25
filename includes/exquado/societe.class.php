<?
/**
* Classe Societé
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../societe.class.php";
class societe_exquado extends societe {
	/*--------------------------------------------------------------*/
	/*                   Constructeurs                              */
	/*--------------------------------------------------------------*/
	public function __construct() {
		parent::__construct();
		$this->table = "societe";
		unset($this->colonnes['panel']['facturation_fs']);
		
		/*-----------Quick Insert-----------------------*/
		$this->quick_insert = array('societe'=>'societe');
		$this->colonnes['fields_column'][] = "societe.nom_commercial";
		$this->colonnes['fields_column'][] = "societe.code_client";
		
		// Panel prinicpal
		$this->colonnes['primary'] = array(
			"code_client"
			,"ref"
			,"nom"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"societe"
				,"nom_commercial"
			))
			,"sirens"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"siren"
				,"siret"
			))
			,"id_famille"
			,"avis_credit"
			,"cs_avis_credit"
			,"score"
			,"cs_score"
			,"contentieux" 
			,"id_owner"
			,"etat"			
			,"id_assistante"
			,"id_filiale"
			,"date_creation"
			,"relation"
			,"joignable"
		);

		/* Définition statique des clés étrangère de la table */
		$this->onglets = array(
			 'contact'=>array('opened'=>true)
			,'affaire'=>array('opened'=>true)
			,'formation_devis'
			,'suivi'=>array('opened'=>true)
			,'devis'
			,'commande'
			,'tache'
			,'parc'
			,'ged'
			,'user'
			,'societe'=>array('field'=>'societe.id_filiale')/*,'societe_domaine'*/
		);		
		// Infos codifiées
		$this->colonnes['panel']['codes_fs']["les_codes"]=array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
			"code_groupe"
			,"code_fournisseur"
		));
		
		// Facturation
		$this->colonnes['panel']['facturation_fs']["ref_tva"]=array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
			"reference_tva"
			,"tva"
		));
		$this->colonnes['panel']['facturation_fs']["banque_ref"]=array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
			"IBAN"
			,"BIC"
		));
		$this->colonnes['panel']['facturation_fs'][]="RIB";
		$this->colonnes['panel']['facturation_fs'][]="divers_2";
		$this->colonnes['panel']['facturation_fs'][]="nom_banque";
		$this->colonnes['panel']['facturation_fs'][]="ville_banque";
		$this->colonnes['panel']['facturation_fs'][]='rum'; 
		
		// Adresses en +
		$this->colonnes['panel']["coordonnees_supplementaires_fs"]["adresse_siege_social"]=array("xtype"=>"textarea");

		$this->colonnes['panel']['adresse_complete_fs']["id_contact_signataire"]=array("xtype"=>"int","numberfield"=>8);	

		// Portail
		$this->colonnes['panel']['portail_fs'] = array(
			"divers_3"
			,"id_accompagnateur"
		);
		$this->panels['portail_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true);
		$this->colonnes['panel']["structure_secteur_fs"]["portail"]=array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'portail_fs');		
		$this->colonnes['panel']["structure_secteur_fs"][] = "lastaccountdate";
		$this->colonnes['panel']["structure_secteur_fs"][] = "receivables";																
		$this->colonnes['panel']["structure_secteur_fs"][] = "securitieandcash";
		$this->colonnes['panel']["structure_secteur_fs"][] = "operatingincome";
		$this->colonnes['panel']["structure_secteur_fs"][] = "netturnover";
		$this->colonnes['panel']["structure_secteur_fs"][] = "operationgprofitless";
		$this->colonnes['panel']["structure_secteur_fs"][] = "financialincome";
		$this->colonnes['panel']["structure_secteur_fs"][] = "financialcharges"; 

		$this->colonnes['panel']['autres'] = array("id_apporteur","id_fournisseur","id_prospection","id_campagne");
		
		$this->colonnes['panel']['delai_rav'] = array(
			"fournisseur_delai_rav"
			,"fournisseur_delai_livraison"
			,"fournisseur_delai_installation"
			,"fournisseur_arav_orange"
			,"fournisseur_arav_rouge"				
		);

		$this->colonnes['panel']['delai_fournisseur'] = array(
			 "fournisseur_nbj_livraison"
			,"fournisseur_nbj_installation"
		);

		$this->panels['delai_rav'] = array('nbCols'=>2,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);
		$this->panels['delai_fournisseur'] = array('nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);
	
		$this->colonnes['panel']['deploiement'] = array( 
			 "delai_rav_panel"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'delai_rav')
			,"delai_fournisseur_panel"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'delai_fournisseur') );
		$this->panels['deploiement'] = array("visible"=>true);


		$this->colonnes['bloquees']['select'] = array('joignable');


		if(ATF::$codename == "cleodisbe"){
			$this->colonnes['primary']["sirens"]["fields"][0] = "num_ident";
			$this->colonnes['primary']["sirens"]["fields"][1] = "reference_tva";
			
			$this->colonnes['panel']['facturation_fs']["ref_tva"]=array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array("tva"));		

		}


		$this->fieldstructure();
		
		unset($this->colonnes['panel']['facturation_fs']["solde_et_relance"]);
		$this->checkAndRemoveBadFields('caracteristiques');
		$this->checkAndRemoveBadFields('facturation_fs');
		
		$this->foreign_key["id_apporteur"] = "societe";
		$this->foreign_key["id_fournisseur"] = "societe";
		$this->foreign_key["id_filiale"] = "societe";
		$this->foreign_key["id_contact_signataire"] = "contact";
		$this->foreign_key["id_prospection"] = "contact";
		$this->foreign_key["id_assistante"] = "user";


		
		
		// Pouvoir modifier massivement
		$this->no_update_all = false; 
		
		// on montre que pour joindre la table domaine, on passe par une table de jointure qui est societe_domaine, si on créé un filtre dans le module société
		$this->listeJointure['domaine']="societe_domaine";
		
		// Droits sur méthodes Ajax
		$this->addPrivilege("getParc");
		$this->addPrivilege("getParcVente");
		$this->addPrivilege("getTreePanel");
		$this->addPrivilege("getChildren");

		$this->addPrivilege("autocompleteFournisseursDeCommande");
		$this->addPrivilege("autocompleteAvecAdresse");
		$this->addPrivilege("autocompleteAvecFiliale");
		$this->addPrivilege("autocompleteFournisseurFormationDevis");
		$this->addPrivilege("importProspect","insert");

		$this->autocomplete = array(
			"field"=>array("societe.societe","societe.nom_commercial","societe.code_client")
			,"show"=>array("societe.societe","societe.nom_commercial","societe.code_client")
			,"popup"=>array("societe.societe","societe.nom_commercial","societe.code_client")
			,"view"=>array("societe.societe","societe.nom_commercial","societe.code_client")
		);


	}

	public function getOpca(){
		$this->q->reset()->where("societe.etat", "actif");
		return $this->select_all();	
	}

	/*
	* Surcharge de l'export
	* Permet de formater les champs textes de CreditSafe pour pouvoir faire les manip dans les fichiers Excel
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function export(&$infos,&$s){	

		$champsFormate = array( "societe.receivables",
								"societe.securitieandcash",
								"societe.operatingincome",
								"societe.netturnover",
								"societe.operationgprofitless",
								"societe.financialincome",
								"societe.financialcharges"
							  );
		foreach ($champsFormate as $key => $value) {
			if(!array_key_exists($value, $infos[0])){ unset($champsFormate[$key]); }
		}
		foreach ($infos as $k => $v) {
			foreach ($champsFormate as $key => $value) { $infos[$k][$value] = str_replace(" ", "", $infos[$k][$value]);	}
		}	
		parent::export($infos,$s);
	}

	/**
	* Méthode qui retourne les parcs dispo pour les Avenants et les AR
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $ligne_affaires 
	*/
	public function getParc(&$infos){
		$id_societe=$this->decryptId($infos["id_societe"]);

		$this->q->reset() ->addField("affaire.id_affaire")
						 ->addField("affaire.ref")
						 ->addField("affaire.affaire")
						 ->addJointure("societe","id_societe","affaire","id_societe",NULL,NULL,NULL,NULL,"INNER")
						 ->addJointure("affaire","id_affaire","commande","id_affaire",NULL,NULL,NULL,NULL,"INNER")
						 ->addCondition("affaire.id_societe",$id_societe,NULL,1)
						 ->addCondition("affaire.id_filiale",$id_societe,"XOR",1)
						 ->addCondition("commande.etat","non_loyer",NULL,2)
						 ->addCondition("commande.etat","mis_loyer","OR",2)
						 ->addCondition("commande.etat","mis_loyer_contentieux","OR",2)
						 ->addCondition("commande.etat","prolongation_contentieux","OR",2)
						 ->addCondition("commande.etat","prolongation","OR",2)
						 ->addCondition("commande.etat","restitution_contentieux","OR",2)
						 ->addCondition("commande.etat","restitution","OR",2);

		if($infos["type"]=="avenant"){
			$this->q->addCondition("affaire.nature","avenant","AND",NULL,"!=");
		}
				
		if($affaire=parent::sa("affaire.id_affaire")){
			foreach($affaire as $key=>$item){
				//Parc de l'affaire
				ATF::parc()->q->reset()->addCondition("parc.id_affaire",$item["affaire.id_affaire_fk"])
									   ->addCondition("parc.existence","actif","AND")
									   ->addCondition("parc.etat","loue","AND",1)
									   ->addCondition("parc.etat","reloue","OR",1)
									   ->addOrder("parc.id_parc","asc");

				$parc=ATF::parc()->sa();
				
				if($ligne_affaire=$this->formateGetParc($parc,$item,$infos["id_affaire"])){
					$ligne_affaires[]=$ligne_affaire;
				}
			}
		}
		$infos['display'] = true;		

		return json_encode($ligne_affaires);
	}
	
	/**
	* Méthode qui formate les données pour qu'elles soient comprises par le treepanel
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $ligne_affaire 
	*/
	public function formateGetParc($parc,$item,$id_affaire_courante=false){

		if($parc){
			$id_affaire=ATF::affaire()->cryptId($item["affaire.id_affaire_fk"]);
			foreach($parc as $k=>$i){
				//Il faut tester si ce parc n'est pas déjà en inactif dans une affaire (c'est à dire en attente de devenir actif)
				//Exemple : Une affaire B vient AR une affaire A, si je fais un avenant C sur l'affaire A je ne dois pas récupérer les parcs déjà récupérer par l'affaire B sauf si le devis de B est perdu
				ATF::parc()->q->reset()
							  ->addCondition("id_affaire",$i["id_affaire"],"AND",false,">")
							  ->addCondition("serial",$i["serial"],"AND")
							  ->addOrder("id_parc","asc");
				
				if($i["provenance"]){
					ATF::parc()->q->addCondition("id_affaire",$i["provenance"],"AND",false,"!=");
				}
				
				if($id_affaire_courante){
					ATF::parc()->q->addCondition("id_affaire",$id_affaire_courante,"AND",false,"!=");	
				}
	
				$otherParc=ATF::parc()->sa();
	
				$parc_libre=true;
				if(count($otherParc)>0){
					foreach($otherParc as $k_=>$i_){
						if(ATF::affaire()->select($i_["id_affaire"],"etat")!="perdue"){
							$parc_libre=false;
						}
					}
				}
				
				if($parc_libre){
					if($i["provenance"]){
						$suffix=" - Parc provenant de l'affaire ".$item["affaire.affaire"]." (".$item["affaire.ref"].")";
					}else{
						$suffix="";
					}
					$produit=ATF::produit()->select($i["id_produit"]);
					$ligne_parc[]=array(
						"text"=>$i["libelle"]." ".$i["ref"]." (".$i["serial"].") - '".ATF::$usr->trans($i["etat"])."'".$suffix
						,"id"=>"parc_".$i["id_parc"]
						,"leaf"=>true
						,"icon"=>ATF::$staticserver."images/blank.gif"
						,"checked"=>false		
						,"id_produit_fk"=>$i["id_produit"]		
						,"produit"=>$i["libelle"]
						,"ref"=>$produit["ref"]		
						,"type"=>$produit["type"]		
						,"serial"=>$i["serial"]		
						,"quantite"=>1		
						,"visibilite_prix"=>"visible"		
						,"id_affaire_provenance"=>$item["affaire.id_affaire_fk"]	
						,"id_parc"=>$i["id_parc"]		
					);
				}
			}
			
			if ($ligne_parc) {
				$ligne_affaire=array(
					"text"=>$item["affaire.ref"]." ".$item["affaire.affaire"]
					,"id"=>"affaire_".$item["affaire.id_affaire_fk"]
					,"leaf"=>false
					,"href"=>"javascript:window.open('affaire-select-".$id_affaire.".html');"
					,"cls"=>"folder"
					,"expanded"=>false
					,"adapter"=>NULL
					,"children"=>$ligne_parc
					,"checked"=>false
				);
			}
			return $ligne_affaire;
		}else{
			return false;
		}
	}
		
	/**
	* Méthode qui retourne les parcs dispo pour les ventes
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $ligne_affaires 
	*/
	public function getParcVente(&$infos){
		$id_societe=$this->decryptId($infos["id_societe"]);

		$this->q->reset()->addOrder("affaire.id_affaire","asc")
						 ->addField("affaire.id_affaire")
						 ->addField("affaire.ref")
						 ->addField("affaire.affaire")
						 ->addJointure("societe","id_societe","affaire","id_societe",NULL,NULL,NULL,NULL,"INNER")
						 ->addJointure("affaire","id_affaire","commande","id_affaire",NULL,NULL,NULL,NULL,"INNER")
						 ->addCondition("affaire.id_societe",$id_societe,NULL,1)
						 ->addCondition("commande.etat","arreter",false,false,"!=")
						 ->addCondition("affaire.id_filiale",$id_societe,"XOR",1);
				
		if($affaire=parent::sa()){
			foreach($affaire as $key=>$item){
				//Parc de l'affaire
				ATF::parc()->q->reset()->addCondition("parc.id_affaire",$item["affaire.id_affaire_fk"])
									   ->addCondition("parc.existence","actif","AND")
									   ->addCondition("parc.etat","broke","AND");
				$parc1=ATF::parc()->sa();
				
				if($ligne_affaire=$this->formateGetParc($parc1,$item,$infos["id_affaire"])){
					$ligne_affaires[]=$ligne_affaire;
				}
			}
		}
	
		$this->q->reset()->addOrder("affaire.id_affaire","asc")
						 ->addField("affaire.id_affaire")
						 ->addField("affaire.ref")
						 ->addField("affaire.affaire")
						 ->addJointure("societe","id_societe","affaire","id_societe",NULL,NULL,NULL,NULL,"INNER")
						 ->addJointure("affaire","id_affaire","commande","id_affaire",NULL,NULL,NULL,NULL,"INNER")
						 ->addCondition("affaire.id_societe",$id_societe,NULL,1)
						 ->addCondition("commande.etat","arreter")
						 ->addCondition("affaire.id_filiale",$id_societe,"XOR",1);

		if($affaire=parent::sa()){
			foreach($affaire as $key=>$item){
				//Parc de l'affaire
				ATF::parc()->q->reset()
							  ->addCondition("parc.id_affaire",$item["affaire.id_affaire_fk"])
							  ->addCondition("parc.existence","actif");

				$parc2=ATF::parc()->sa();
				
				if($ligne_affaire=$this->formateGetParc($parc2,$item,$infos["id_affaire"])){
					$ligne_affaires[]=$ligne_affaire;
				}
			}
		}
		
		$infos['display'] = true;

		return json_encode($ligne_affaires);
	}
	

	

	/**
	* Autocomplete retournant seulement les fournisseurs ayant des produits dans les ligne de la commande passée en paramètre,
	* des lignes qui ne sont pas reprise (sans id_affaire_provenance)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteFournisseursDeCommande($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			->from("societe","id_societe","commande_ligne","id_fournisseur")
			->addGroup("societe.id_societe")
			->addField("societe.id_contact_signataire")
			->addField(array("CONCAT(societe.id_contact_signataire)"=>array("alias"=>"id_contact_signataire_fk","nosearch"=>true)))
			->where("societe.fournisseur","oui");
		return parent::autocomplete($infos,false);
	}

	/**
	* Permet de retourner l'adresse d'une societe
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	* @param string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteAvecAdresse($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			->addField("adresse")
			->addField("ville")
			->addField("cp")
			->where("societe.fournisseur","oui");
		return $this->autocomplete($infos,false);
	}
	/**
	* Permet de retourner l'adresse d'une societe
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteAvecFiliale($infos,$reset=true) {

//		$this->q->reset()
//				->addField("societe.*")
//				->setStrict()
//		     ->addCondition("societe","%cleodis%",NULL,false,"LIKE")
//			 ->setToString();
//			 
//		$q1=$this->sa();
//			 
//		$this->q->reset()
//				->addField("societe.*")
//				->setStrict()
//			 ->addJointure("societe","id_societe","societe","id_filiale","parent")
//		     ->addCondition("parent.societe","%cleodis%",NULL,false,"LIKE")
//			 ->setToString();
//			 
//		$q2=$this->sa();
//		
//		$this->q->reset()
//					->addUnion($q1)
//					->addUnion($q2);
//		$union=$this->q->getUnion();	
//		$this->q->reset()->setSubQuery($union,'uni');

		if ($reset) {
			$this->q->reset();
		}
		$this->q
			 ->addCondition("societe","%cleodis%",NULL,false,"LIKE")
			 ->addField("tva");
		$autocomplete= $this->autocomplete($infos,false);
		return $autocomplete;
	}

	/** 
	* Mise à jour des infos
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		// Vérification qu'il n'existe aucun autre parc d'existence active avec le même serial
		$this->infoCollapse($infos);

		// Si avis_credit change, on crée un suivi !
		$avis_credit = $this->select($infos["id_societe"],"avis_credit");
		$notifie = "43,21"; // 43 Lejeune Nicolas et 21 Severine Mazars
		if (!preg_match("/".$this->select($infos["id_societe"],"id_owner")."/",$notifie)) {
			$notifie .= ",".$this->select($infos["id_societe"],"id_owner");
		}
		
		if ($infos["avis_credit"] && ($avis_credit !== $infos["avis_credit"])) {		
			$suivi = array(
				"id_user"=>ATF::$usr->get('id_user')
				,"id_societe"=>$infos['id_societe']
				,"type_suivi"=>'Contentieux'
				,"texte"=>"La société passe de l'avis crédit '".ATF::$usr->trans("societe_avis_credit_".$avis_credit)."' à '".ATF::$usr->trans("societe_avis_credit_".$infos["avis_credit"])."'"
				,'public'=>'non'
				,'suivi_societe'=>array(0=>ATF::$usr->getID())
				,'suivi_notifie'=>$notifie
			);
			ATF::suivi()->insert($suivi);
		}

		// Si score change, on crée un suivi !
		$score = $this->select($infos["id_societe"],"score");		
		if ($infos["score"] && $score!=$infos["score"]) {			
			$suivi = array(
				"id_user"=>ATF::$usr->get('id_user') 
				,"id_societe"=>$infos['id_societe']
				,"type_suivi"=>'Contentieux'
				,"texte"=>"La société passe du score '".$score."' à '".$infos["score"]."'"
				,'public'=>'non'
				,'suivi_societe'=>array(0=>ATF::$usr->getID())
				,'suivi_notifie'=>$notifie
			);
			ATF::suivi()->insert($suivi);
		}
				
		return parent::update($infos,$s,$files,$cadre_refreshed,$nolog);
	}


	/** 
	* Insert
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if($infos["societe"]["siret"] != NULL){
			//On check si le siret existe déja
			$this->q->reset()->where("siret",$infos["societe"]["siret"]);
			if($this->select_all()){
				throw new errorATF("Une société existe déja avec le SIRET ".$infos["societe"]["siret"],878);
			}
		}		
		return parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);
	}

	public function autocompleteFournisseurFormationDevis($infos,$reset=true,$count=false){	
		if($reset){
			$this->q->reset();
		}
		
		
		$this->q->from("societe","id_societe","formation_devis_fournisseur","id_societe")
				->from("formation_devis_fournisseur","id_formation_devis","formation_devis","id_formation_devis")				
				->where($infos["condition_field"],ATF::formation_devis()->decryptId($infos["condition_value"]))
				->addField("societe.id_societe","id_societe")
				->addField("societe.societe","nom");
		if($count){
			$this->q->setCount();
			$return = $this->select_all();
		}else{
			$return = parent::autocomplete($infos,false);
		}	
		
		return $return;
	}



	/** 
	* Permet d'intégrer
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function importProspect(&$infos,&$s,$files=NULL) {
		$infos['display'] = true;
		$path = $files['file']['tmp_name']; 


		if ($infos['id_owner']) $id_owner = ATF::user()->decryptId($infos['id_owner']);

		if ($infos['id_apporteur']) $id_apporteur = ATF::user()->decryptId($infos['id_apporteur']);

		if ($infos['id_prospection']) $id_prospection = ATF::contact()->decryptId($infos['id_prospection']);

		// Récupération des données du fichier temporaire
		$f = fopen($path,"r");
		
		// Vérification des colonnes
		$cols = fgetcsv($f, 1, ";");

		$societeInserted = $contactInserted = 0;

		ATF::db($this->db)->begin_transaction();
		$erreurs = array();

		$lineCompteur = 0;
		while (($data = fgetcsv($f, 10000, ";")) !== FALSE) {
			$lineCompteur++;
			if ($lineCompteur==1 || !$data[2]) continue;

			$data = array_map("utf8_encode",$data);

			// Gestion del'état :
			$etat = 'actif';
			/*switch ($data[24]) {
				case "Actif économiquement":
					$etat = 'actif';
				break;
				default:
					$etat = "inactif";
				break;
			}*/

			// Gestion del'effectif :
			switch ($data[29]) {
				case "Aucun salarié":
					$effectif = 1;
				break;
				case "3 à 5 salariés":
				case "6 à 9 salariés":
					$effectif = 10;
				break;
				case "10 à 19 salariés":
				case "20 à 49 salariés":
					$effectif = 50;
				break;
				case "50 à 99 salariés":
					$effectif = 100;
				break;
				case "100 à 199 salariés":
					$effectif = 500;
				break;
				default:
					$effectif = NULL;
				break;
			}


			$societe = array(
				"id_owner"=>$id_owner,
				"id_apporteur"=>$id_apporteur,
				"id_prospection"=>$id_prospection,
				"relation"=>$infos['relation'],
				"code_client"=>$data[0],
				"siret"=>$data[1],
				"societe"=>$data[2],
				"nom_commercial"=>$data[3],
				"adresse"=>$data[4],
				"adresse_2"=>$data[5],
				"cp"=>$data[6],
				"ville"=>$data[7],
				"adresse_3"=>$data[8],
				"siren"=>$data[10],
				"structure"=>$data[19],
				"tel"=>$data[21],
				"fax"=>$data[22],
				"etat"=>$etat,
				"capital"=>$data[26],
				"naf"=>$data[27],
				"activite"=>$data[28],
				"reference_tva"=>$data[33],
				"cs_score"=>$data[31],
				"cs_avis_credit"=>$data[34],
				"date_creation"=>date("Y-m-d",strtotime(str_replace("/","-",$data[23]))),
				"effectif"=>$effectif
			);

			if($data[12]) $societe["id_fournisseur"] = $data[12]; 
			
			$birthday = date("Y-m-d",strtotime(str_replace("/","-",$data[17])));
			$contact = array(
				"civilite"=>$data[14]?strtolower($data[14]):"m",
				"prenom"=>$data[15],
				"nom"=>$data[16],
				"anniversaire"=>$data[17]?$birthday:NULL,
				"fonction"=>$data[20],
			);
			

			try { 
				$contact['id_societe'] = $this->i($societe);


				$societeInserted++;
				if(str_replace(" ", "", $contact["nom"])){
					$id_c = ATF::contact()->i($contact);

					$contactInserted++;
				}

			} catch (errorATF $e) {

				$msg = $e->getMessage();
                
				if (preg_match("/generic message/",$msg)) {
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

		fclose($handle);	

		if (!empty($erreurs)) {
			$return['errors'] = $erreurs;
			$return['success'] = false;
			ATF::db($this->db)->rollback_transaction();
		} else {
			$return['warnings'] = $warnings;
			$return['societeInserted'] = $societeInserted;
			$return['contactInserted'] = $contactInserted;
			$return['success'] = true;
			ATF::db($this->db)->commit_transaction();
		}

		return json_encode($return);
	}


	/** 
	* Permet de connaitre l'investissement en cours d'un client
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer
	*/
	public function getInfosInvestissement($id_societe) {
		$totalInvestissement = $totalRestant = 0;

		ATF::affaire()->q->reset()->where("affaire.id_societe",$id_societe)
								  ->where("affaire.etat","commande","OR")
								  ->where("affaire.etat","facture","OR");
		$affaires = ATF::affaire()->select_all();

		foreach ($affaires as $kaff => $vaff) {
			ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire",$vaff["affaire.id_affaire"])
											  ->addField("bon_de_commande.prix","prix")
											  ->addField("bon_de_commande.bon_de_commande","commande");
			$commandes = ATF::bon_de_commande()->select_all();
			$encours = 0;
			foreach ($commandes as $kcom => $vcom) {	
				$totalInvestissement += $vcom["prix"];
				if($vcom["solde_ht"] < 0){	$encours += $vcom["solde_ht"];	}
				
			}

			ATF::facturation()->q->reset()->where("id_affaire",$vaff["affaire.id_affaire"])
										  ->addField("SUM(facturation.montant)","prix") 
										  ->whereIsNull("facturation.id_facture");
			$facturations = ATF::facturation()->select_all();

			foreach ($facturations as $kfact => $vfact) {	$totalRestant += $vfact["prix"]; }			
		}
		return array("investissement"=>number_format($totalInvestissement,2,"," ," "), "restant"=>number_format($totalRestant+$encours,2,"," ," "));
	}

};



?>