<?
require_once dirname(__FILE__)."/../societe.class.php";
/**  
* @package Optima
* @subpackage AbsysTech
*/
class societe_absystech extends societe {
	/**
	* Constructeur par défaut
	*/
	public function __construct() {
		parent::__construct();
		$this->table = "societe";
		
		/*-----------Colonnes Select all par défaut------------------------------------------------*/
		$this->colonnes['fields_column'] = array(	
			 'societe.societe'
			,'societe.tel' => array("renderer"=>"tel","width"=>120)
			,'societe.email'=>array("renderer"=>"email","width"=>180)
			,'societe.ville'
			,'credits'=>array("custom"=>true,"width"=>100)
			,'dernierSuivi'=>array("custom"=>true)
			,'completer'=>array('custom'=>true,"renderer"=>"progress","aggregate"=>array("min","avg"),"width"=>100,"align"=>"center")
			,'societe.meteo'=>array("renderer"=>"meteo","width"=>50,"nosort"=>true,"align"=>"center")
			,'atcard'=>array("renderer"=>"atcard","width"=>50,'custom'=>true,"nosort"=>true,"align"=>"center")
			,'derniereFacture'=>array("renderer"=>"derniereFacture","custom"=>true,"width"=>100,"align"=>"center")
			,'renderAjoutTache'=>array("renderer"=>"tacheActions","width"=>60,"custom"=>true)
			,'societe.id_apporteur_affaire'
		);
		
		/*-----------Colonnes bloquées select -----------------------*/
		$this->colonnes['bloquees']['select'] = array(

			'societe.meteo'
		);

		// Adresse de facturation
		array_unshift($this->colonnes['panel']['adresse_facturation_complete_fs'],"facturer_le_siege");

		
		$this->colonnes['bloquees']['insert'][] = "credits";
		$this->colonnes['bloquees']['insert'][] = "meteo";
		$this->colonnes['bloquees']['insert'][] = "meteo_calcul";
		$this->colonnes['bloquees']['update'][] = "credits";
		$this->colonnes['bloquees']['update'][] = "meteo";
		$this->colonnes['bloquees']['update'][] = "meteo_calcul";
		
		$this->colonnes['primary']["credits"] = array("custom"=>true);
		
		$this->colonnes["rapprocher"] = array(
			 'id_societe'=>array("disabled"=>true),
			 'montant'=>array("formatNumeric"=>true,"xtype"=>"textfield"),
			 'factures'=>array("custom"=>true)
		);
		$this->panels['rapprocher'] = array("visible"=>true,'nbCols'=>1);
		$this->colonnes['panel']['affacturage_fs'] = array(
			"rib_affacturage"
			,"iban_affacturage"
			,"bic_affacturage"
		);
		$this->panels['affacturage_fs'] = array("visible"=>true,'nbCols'=>1,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);
		$this->colonnes['panel']['coordonnees']["affacturage"] = array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'affacturage_fs');
		
		$this->colonnes['panel']['mdp'] = array(
			"divers_5"
			,"mdp_client"
			,"mdp_absystech"
		);
		$this->panels["mdp"] = array('nbCols'=>3);
		
		$this->colonnes["speed_insert"] = array(
			'societe'
		);
		
		$this->foreign_key['id_apporteur_affaire'] = "societe";

		$this->fieldstructure();

		

		// champ parc remplacer par stock
		$this->onglets = array(
			'contact'=>array('opened'=>true)
			,'suivi'=>array('opened'=>true)
			,'affaire'=>array('opened'=>true)
			,'affaire_cout_page'=>array('opened'=>true) 
			,'devis'
			,'commande'
			,'facture'
			,'livraison'
			,'gestion_ticket'
			,'tache'
			,'hotline'
			//,'stock'
			,'ged'
			,'gep_projet'
			,'user'
			,'societe'=>array('field'=>'societe.id_filiale')
			,'facture_fournisseur'=>array('opened'=>true)
		);

		$this->colonnes['bloquees']['select'] =   array("divers_5");
		
		$this->quick_action['select']['atcard'] = array('privilege'=>'export');
		$this->quick_action['select_all']['atcardImport'] = array('privilege'=>'import');
		
		$this->addPrivilege('rapprocher');
		$this->addPrivilege('updateRapprocher');
		$this->addPrivilege("atcard","export"); 
		$this->addPrivilege("atcardImport","import"); 
		$this->addPrivilege("autocompleteAvecTermes");
		$this->addPrivilege("autocompleteAvecTVA");
		$this->addPrivilege("autocompleteFournisseursDeCommande");
		$this->addPrivilege("autocompleteOnlyActive");
		$this->addPrivilege("add_ticket","insert");
		
		
		$this->selectExtjs=true;
	}
/*
    public function select($id,$f=false) {
        $r = parent::select($id,$f);
        $r['societe'] = addslashes($r['societe']);
        $r['societe'] = str_replace("'","OO",$r['societe']);
        return $r;
    }
    
    public function nom($id) {
        $r = parent::nom($id);
        $r = addslashes($r);
        return $r;        
    }
*/     
	
	/** 
	* Surcharge de l'insertion pour les socits
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Les informations  insrer
	* @param array $s la session
	* @param array $files Les fichiers uploads ventuels
	* @param array $cadre_refreshed Le cadre refreshed utilis pour le rafraichissement ajax
	*/
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);
		$infos["divers_5"]=substr(md5(time()),0,4); // Mot de passe hotline
		$infos["mdp_client"]=util::generateRandWord(9);
		$infos["mdp_absystech"]=util::generateRandWord(9);

		ATF::db($this->db)->begin_transaction();
		
		try {
			//Unset du crédit pour les duplicates
			unset($infos["credits"]);
			$id_societe = parent::insert($infos,$s,$files,$cadre_refreshed);
		
			if(ATF::famille()->nom($infos["id_famille"])=="Particulier"){
				$contact=array(
									"civilite"=>NULL,
									"nom"=>$infos["societe"],
									"etat"=>$infos["etat"],
									"id_societe"=>$id_societe,
									"id_owner"=>$infos["id_owner"],
									"adresse"=>$infos["adresse"],
									"adresse_2"=>$infos["adresse_2"],
									"adresse_3"=>$infos["adresse_3"],
									"cp"=>$infos["cp"],
									"ville"=>$infos["ville"],
									"id_pays"=>$infos["id_pays"],
									"tel"=>$infos["tel"],
									"fax"=>$infos["fax"],
									"email"=>$infos["email"],
									"cle_externe"=>$infos["cle_externe"]
								);
								
				ATF::contact()->insert($contact,$s);
				
				$this->redirection("select",$id_societe);
			}
		} catch (error $e) {
			ATF::db($this->db)->rollback_transaction();	
			throw new errorATF(ATF::$usr->trans("probleme_insertion",$this->table)." => ".$e->getMessage(),$e->getCode());
		}
		
		ATF::db($this->db)->commit_transaction();

		return $id_societe;
	}
	
	/**
    * Retourne toutes les sociétés débitrices
    * @author Jérémie Gwiazdowski <jgw@absystech.fr>
    * @return array $societe
    */   	
	public function societes_debitrices($limite=NULL){
		$this->q->reset()->addField('societe.id_societe','id_societe')->addField('societe.societe','societe')->setToString();
		$subQuery = $this->saCustom();

		//Sociétés négatives par ordre croissant
		$this->q->reset()
			->addField("g2.id_societe","id_societe")
			->addField("g2.societe","societe")
			->addField("g2.credits","credits")
			->addField("g2.societe","societe")
			->setSubQuery($subQuery,"g2")
			->where("g2.credits","0","AND",false,"<")
			->addOrder("g2.credits")
			->setStrict();
		if ($limite) {
			$this->q->setLimit($limite);
		}
		
		return $this->select_all();
	}

	/**
	* Surcharge du select-All
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function saCustom(){
		$this->q
			->addField("(IF(LENGTH(societe.email)>0,1,0)".
			"+IF(LENGTH(societe.tel)>0,1,0)".
			"+IF(LENGTH(societe.fax)>0,1,0)".
			"+IF(LENGTH(societe.siren)>0,1,0)".
			"+IF(LENGTH(societe.siret)>0,1,0)".
			"+IF(LENGTH(societe.web)>0,1,0)".
			"+IF(LENGTH(societe.capital)>0,1,0)".
			"+IF(LENGTH(societe.effectif)>0,1,0)".
			"+IF(LENGTH(societe.adresse)>0,1,0)".
			"+IF(LENGTH(societe.cp)>0,1,0)".
			"+IF(LENGTH(societe.ville)>0,1,0))*100/11","completer");
		
//		// Derrnier crédit restant basé sur gestion_ticket
//		$g = new gestion_ticket();
//		$g->q->addField("solde")->addField("id_societe")->addOrder("id_gestion_ticket","desc")->setToString();
//		$this->q
//			->addField("g.solde","credits")
//			->from("societe","id_societe","(".$g->sa().")","id_societe","g");
//		
//		// Date de dernier suivi effectué
//		$s = new suivi();
//		$s->q->addField("date")->addField("id_societe")->addOrder("id_suivi","desc")->setToString();
//		$this->q
//			->addField("s.date","dernierSuivi")
//			->from("societe","id_societe","(".$s->sa().")","id_societe","s");
//			
//		// Nécessaire pour que les jointures ne retournent que le dernier suivi, et derneir crédit
//		$this->q->addGroup('societe.id_societe');

		// Derrnier crédit restant basé sur gestion_ticket
		$g = new gestion_ticket();
		//$g->q->setAlias("gSub")->addField("MAX(gSub.id_gestion_ticket)","id")->orWhere("gSub.id_societe","societe.id_societe",false,"=",false,true)->setStrict()->setToString();
		$g->q->setAlias("gSub")->addField("gSub.id_gestion_ticket","id")->orWhere("gSub.id_societe","societe.id_societe",false,"=",false,true)->setStrict()->addOrder("gSub.date","desc")->addOrder("gSub.id_gestion_ticket","desc")->setLimit(1)->setToString();
		$this->q->orWhere("g.id_gestion_ticket","(".$g->sa().")","gSubWhereGesTic","=",true,true)
			->addField("g.solde","credits")
			->from("societe","id_societe","gestion_ticket","id_societe","g",NULL,NULL,"gSubWhereGesTic");

		// Derrnier crédit restant basé sur gestion_ticket
		$s = new suivi();
		$s->q->setAlias("sSub")->addField("MAX(sSub.id_suivi)","id")->orWhere("sSub.id_societe","societe.id_societe",false,"=",false,true)->setStrict()->setToString();
		$this->q->orWhere("s.id_suivi","(".$s->sa().")","sSubWhereSuivi","=",true,true)
			->addField("s.date","dernierSuivi")
			->from("societe","id_societe","suivi","id_societe","s",NULL,NULL,"sSubWhereSuivi");

		// Date de la dernière facture
		$f = new facture();
		$f->q->setAlias("fSub")->addField("MAX(fSub.id_facture)","id")->orWhere("fSub.id_societe","societe.id_societe",false,"=",false,true)->setStrict()->setToString();
		$this->q->orWhere("f.id_facture","(".$f->sa().")","fSubWhereFacture","=",true,true)
			->addField("f.date","derniereFacture")
			->from("societe","id_societe","facture","id_societe","f",NULL,NULL,"fSubWhereFacture");

		return parent::saCustom();
	}
			
	/**
    * Retourne le solde négatif de toutes les sociétés
    * @author Jérémie Gwiazdowski <jgw@absystech.fr>, Nicolas BERTEMONT <nbertemont@absystech.fr>
    * @return array
    */   	
	public function getSoldeSNegatives(){
		$this->q->reset()->setToString()
						->addCondition("societe.etat","actif");
		$subQuery = $this->saCustom();

		//Somme des sociétés négatives
		$this->q->reset()
			->setSubQuery($subQuery,"g2")
			->addField("SUM(g2.credits)")
			->addCondition("g2.credits","0","AND",false,"<") 
			->setStrict()
			->setDimension("cell");
		return $this->select_all();
	}
	
	/**
	* Retourne le cumul des solde de toutes les sociétés (permet de voir s'il y a du crédit de tickets au client)
    * @author Jérémie Gwiazdowski <jgw@absystech.fr>
    * @return array
	*/
	public function getSoldeS(){
		$this->q->reset()->setToString();
		$subQuery = $this->saCustom();
		
		//Somme des soldes des sociétés
		$this->q->reset()
			->setSubQuery($subQuery,"g2")
			->addField("SUM(credits)")
			->setStrict()
			->setDimension("cell");

		return $this->select_all();
	}
	
	/**
    * Dans le cas où on ajoute des tickets en passant par le select des sociétés
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
    * @author Fanny DECLERCK <fdeclerck@absystech.fr>
	* @param array $infos $infos['id_societe'] et $infos['credits']
	* @param array $s 
	* @param file $files
	* @param array $cadre_refreshed
    * @return boolean true 
    */   	
	public function add_ticket($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		return ATF::gestion_ticket()->add_ticket($infos,$s,$files,$cadre_refreshed);
	}
	
	/**
    * Redirection vers le portail hotline 
    * @author Fanny DECLERCK <fdeclerck@absystech.fr>
    * @author Jérémie Gwiazdowski <jgw@absystech.fr>	
	* @param string $reference la Référence de la société (SLI09110001)
	* @param string $password_hotline Le mot de passe hotline (divers_5) de la société
    * @return string $url Le lien direct vers la hotline
    */   	
	public function redirect_hotline($reference,$password_hotline){
		/*lien pour autologin et redirect*/
		return __HOTLINE_URL__."login.php?login=".base64_encode($reference)."&password=".base64_encode($password_hotline)."&contact=".base64_encode("choix_contact")."&url=".base64_encode(__HOTLINE_URL__."choix_contact.php")."&schema=".base64_encode(ATF::$codename);
	}
	
	/** 
	* Envoi les identifiants hotline par mail via le contact concerné
	* @author Jérémie Gwiazdowski
	* @param array $infos le tableau contenant  : id_contact et id_societe
	* @param array $s la session
	* @param array $files Les fichiers uploadés éventuels
	* @param array $cadre_refreshed Le cadre refreshed utilisé pour le rafraichissement ajax
	* @return boolean true si l'envoie de mail s'est bien passé ?
	*/
	public function send_identifiants_hotline($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		//Test du contact
		if(!$infos['id_contact']) throw new errorATF(ATF::$usr->trans('si_hotline_no_contact',$this->table));
		//Test de la societe
		if(!$infos['id_societe']) throw new errorATF(ATF::$usr->trans('si_hotline_no_societe',$this->table));
		
		//Recherche du contact
		$contact=ATF::contact()->select($infos['id_contact']);
		
		//Mail vide
		if(!$contact['email']) throw new errorATF(ATF::$usr->trans('si_hotline_contact_no_mail',$this->table));
		
		//Recherche de la societe
		$societe=$this->select($infos['id_societe']);
		
		//Construction du mail
		$infos_mail['societe']=$societe;
		$infos_mail['contact']=$contact;
		$infos_mail['direct_link']=$this->redirect_hotline($societe['ref'],$societe['divers_5']);
		$infos_mail["from"] = "Support AbsysTech <no-reply@absystech.fr>";
		$infos_mail["objet"] = ATF::$usr->trans("mail_identifiants_hotline");
		$infos_mail["recipient"] = $contact['email'];
		$infos_mail["template"] = "societe_identifiants_hotline";
		
		//Envoi du mail
		$mail = new mail($infos_mail);
		$mail->send();
		
		//Notice
		ATF::$msg->addNotice(ATF::$usr->trans("mail_send_identifiants_hotline",$this->table));
		
		return true;
	}
	
	/**
	* Donne le solde en fonction des gestion_ticket
	* @param int $id_societe L'identifiant de la societé désirée
	* @param string $dateMax la dernière date désirée
	*/
	public function getSolde($id_societe,$dateMax=NULL){
		$id_societe=$this->decryptId($id_societe);
		//Recherche de la dernière opération
		ATF::gestion_ticket()->q->reset()->addField("MAX(operation)")->addCondition("id_societe",$id_societe)->setDimension("cell");
		if($dateMax){
			ATF::gestion_ticket()->q->addCondition("date",$dateMax,"AND",false,"<");
		}
		$operation=ATF::gestion_ticket()->sa();
		if($operation){
			//Recherche du solde
			ATF::gestion_ticket()->q->reset()->addField("solde")->addCondition("id_societe",$id_societe)->addCondition("operation",$operation)->setDimension("cell");
			return ATF::gestion_ticket()->sa();
		}else{
			return 0;
		}
	}
	
	/**
	* Fonction faisant partie de la météo "météo"
	* Renvoi le solde_total c'est à diore la différence entre la somme des factures moins la somme des paiements
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return float $solde_total
	*/	
	public function solde_total($id_societe){
		// Création de la subQuery
		$this->q->reset() 
			->addField("facture.prix*facture.tva","facture")
			->addField("ROUND(IF(facture.date_effective IS NOT NULL
				,0
				,IF(
					(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
					,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
					,facture.prix*facture.tva
				)),2)","solde")
			->addJointure("societe","id_societe","facture","id_societe",NULL,NULL,NULL,NULL,"INNER")
			->addJointure("facture","id_facture","facture_paiement","id_facture")
			->addCondition("societe.id_societe",$id_societe)
			->addGroup("facture.id_facture")
			->setStrict()
			->setToString();
		$subQuery = parent::select_all();
		
		// Utilisation de la subQuery
		$this->q->reset()
			->addField('SUM(a.solde)','soldeTotal')
			->setSubQuery($subQuery,'a')			
			->setDimension('cell');
		return parent::select_all();
	}
	
	/**
	* Solde total pour toutes les sociétés
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	* @return float $solde_total
	*/	
	public function solde_total_global($id_societe){
		// Création de la subQuery
		$this->q->reset() 
			->addField("facture.id_societe","id_societe")
			->addField("societe.societe","societe")
			->addField("facture.prix*facture.tva","facture")
			->addField("-ROUND(IF(facture.date_effective IS NOT NULL
				,0
				,IF(
					(facture.prix*facture.tva)-SUM(facture_paiement.montant)>=0
					,(facture.prix*facture.tva)-SUM(facture_paiement.montant)
					,facture.prix*facture.tva
				)),2)","solde")
			->addJointure("societe","id_societe","facture","id_societe",NULL,NULL,NULL,NULL,"INNER")
			->addJointure("facture","id_facture","facture_paiement","id_facture")
			->addGroup("societe.id_societe")
			->addGroup("facture.id_facture")
			->setStrict()
			->setToString();
		$subQuery = parent::select_all();
		
		// Utilisation de la subQuery
		$this->q->reset()
			->addField('SUM(a.solde)','soldeTotal')
			->addField('a.id_societe','id_societe')
			->addField("a.societe","societe")
			->addGroup("id_societe")
			->setSubQuery($subQuery,'a')
			->setStrict();			
		$this->q->setToString();
		$finalSubQuery = parent::sa();
		
		$this->q->reset()
			->addField('soldeTotal')
			->addField('id_societe')
			->addField('societe')
			->setSubQuery($finalSubQuery,'final')
			->setStrict()
			->addOrder("soldeTotal","asc")
			->setLimit(15);			
		
		return parent::sa();
	}

	/**
	* Fonction faisant partie de la météo "météo"
	* Renvoi un tableau contenant par année , la marge le CA et le ratio ca/marge
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return array $chiffre_par_an
	*/	
	public function chiffre_par_an($id_societe){

		$chiffre_par_an =array();

		//Somme des factures (prix)
		$this->q->reset()
			->addField("YEAR(facture.date)","annee")
			->addField("(SUM(facture.prix))","prix")
			->addJointure("societe","id_societe","facture","id_societe",NULL,NULL,NULL,NULL,"INNER")
			->addCondition("societe.id_societe",$id_societe)
			->addOrder("YEAR(facture.date)","DESC")
			->addGroup("YEAR(facture.date)");
		$facture = parent::select_all();

		//Somme des commandes (prix d'achat)
		$this->q->reset()
			->addField("YEAR(commande.date)","annee")
			->addField("(SUM(IF(`commande`.`prix_achat` IS NULL OR `commande`.`etat` = 'annulee', 0, `commande`.`prix_achat`)))","prix_achat")
			->addJointure("societe","id_societe","commande","id_societe",NULL,NULL,NULL,NULL,"INNER")
			->addCondition("societe.id_societe",$id_societe)
			->addOrder("YEAR(commande.date)","DESC")
			->addGroup("YEAR(commande.date)");
		$commande = parent::select_all();

		foreach($commande as $key=>$item){
			$prix=0;
			
			//Pour chaque année où il y a eu une commande on récupère les factures (s'il y en a)
			foreach($facture as $k=>$i){
				if($i["annee"]==$item["annee"]){
					$prix=$i["prix"];
					unset($facture[$k]);
				}
			}
			
			//Pour chaque année calcul de la marge du ca et du ratio facture/commande
			$chiffre_par_an[]=array(
				"annee"=>$item["annee"],
				"marge"=>$prix-$item["prix_achat"],
				"pourcent"=>((1-($item["prix_achat"]/$prix))*100),
				"ca"=>$prix
			);
		}
		
		//Pour les années où il y a des factures mais pas de commande
		if($facture){
			foreach($facture as $key=>$item){
				array_unshift($chiffre_par_an,array(
					"annee"=>$item["annee"],
					"marge"=>$item["prix"],
					"pourcent"=>100,
					"ca"=>$item["prix"]
				));
			}
		}

		return $chiffre_par_an;
	}

	/**
	* Fonction faisant partie de la météo "météo"
	* Renvoi le nombre d'affaire perdu, le nombre de devis perdu, le pourcentage d'affaire perdu sur l'ensemble des affaire, le pourcentage de devis perdu sur l'ensemble des devis
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	* @return float $solde_total
	*/	
	public function affaire_perdu($id_societe){

		// Affaires
		$this->q->reset()
			 ->addField("COUNT(affaire.id_affaire)","affaire")
			 ->addField("SUM(IF(affaire.etat='perdue',1,0))","affaire_perdu")
			 ->addJointure("societe","id_societe","affaire","id_societe",NULL,NULL,NULL,NULL,"INNER")
			 ->addCondition("societe.id_societe",$id_societe)
			 ->setDimension('row');
		$affaire = parent::select_all();
		$affaire["affaire_perdu_pourcent"]=($affaire["affaire_perdu"]/$affaire["affaire"]*100);
		
		$this->q->reset()
			 ->addField("COUNT(devis.id_devis)","devis")
			 ->addJointure("societe","id_societe","affaire","id_societe",NULL,NULL,NULL,NULL,"INNER")
			 ->addJointure("affaire","id_affaire","devis","id_affaire",NULL,NULL,NULL,NULL,"INNER")
			 ->addCondition("societe.id_societe",$id_societe)
			 ->setDimension('row');
		$affaire = array_merge($affaire,parent::select_all());
		
		ATF::devis()->q->reset()
			 ->addField("id_affaire")
			 ->addCondition("id_societe",$id_societe)
			 ->addCondition("etat","perdu","AND")
			 ->setStrict()
			 ->settoString();
		$subquery = ATF::devis()->select_all();
				
		ATF::devis()->q->reset()
			 ->addField("COUNT(id_devis)","devis_perdu")
			 ->addCondition("id_affaire",$subquery,NULL,NULL,"IN",false,true)
			 ->addCondition("devis.id_societe",$id_societe,"AND")
			 ->setDimension('row');


		$subquery = ATF::devis()->select_all();

		$affaire = array_merge($affaire,ATF::devis()->select_all());

		$affaire["devis_perdu_pourcent"]=($affaire["devis_perdu"]/$affaire["devis"]*100);
	
		return $affaire;
	}

	/**
	* Fonction faisant partie de la météo "météo"
	* Renvoi un la différence moyenne entre date prévisonnelle et date effective
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	* @return array $chiffre_par_an
	*/	
	public function datediff($id_societe){
		$this->q->reset()
			->addField("ROUND(SUM(DATEDIFF(date_effective,facture.date))/COUNT(facture.id_facture),0)","datediff")
			->addJointure("societe","id_societe","facture","id_societe",NULL,NULL,NULL,NULL,"INNER")
			->addCondition("societe.id_societe",$id_societe)
			->addConditionNotNull("facture.date")
			->addConditionNotNull("date_effective")
			->setDimension('cell');
		return parent::select_all();
	}
	
	/**
	* Fonction "météo" qui pour une société renvoie un indice qui permet de classer la société
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	* @return array $meteo
	*/	
	public function meteo($id_societe){

		//Somme des factures (prix)
		$this->q->reset()
			->addField("(SUM(facture.prix))","prix")
			->addJointure("societe","id_societe","facture","id_societe",NULL,NULL,NULL,NULL,"INNER")
            ->addCondition("facture.date",date('Y-m-d',strtotime("-3 year")),"AND",false,">=")
			->addCondition("societe.id_societe",$id_societe);
		$facture = parent::select_all();

		//Somme des commandes (prix d'achat)
		$this->q->reset()
			->addField("(SUM(IF(`commande`.`prix_achat` IS NULL OR `commande`.`etat` = 'annulee', 0, `commande`.`prix_achat`)))","prix_achat")
			->addJointure("societe","id_societe","commande","id_societe",NULL,NULL,NULL,NULL,"INNER")
            ->addCondition("commande.date",date('Y-m-d',strtotime("-3 year")),"AND",false,">=")
			->addCondition("societe.id_societe",$id_societe);
		$commande = parent::select_all();
		
		$meteo["marge"]=$facture[0]["prix"]-$commande[0]["prix_achat"];
		$meteo['solde_total']=$this->solde_total($id_societe);
		$meteo['datediff']=$this->datediff($id_societe);
		$meteo['affaire_perdu']=$this->affaire_perdu($id_societe);
		$meteo['chiffre_par_an']=$this->chiffre_par_an($id_societe);
		
		if($meteo['solde_total']==NULL && $meteo['affaire_perdu']['devis_perdu']==0 && $meteo['datediff']==0 && $marge==0){
			$meteo["icone"]="Fog";
			$meteo["echelle"]="Pas de données";
		}else{

			$marge=$meteo['marge'];
			$datediff=$meteo['datediff'];
			$devis_perdu_pourcent=$meteo["affaire_perdu"]['devis_perdu_pourcent'];
			$solde_total=$meteo['solde_total'];
			$credit=$this->getSolde($id_societe);

			$coefficient_datediff=__METEO_COEFF_DATEDIFF__;
			$coefficient_solde_total=__METEO_COEFF_SOLDE_TOTAL__;
			$coefficient_marge=__METEO_COEFF_MARGE__;
			$coefficient_devis_perdu=__METEO_COEFF_DEVIS_PERDU__;
			$coefficient_credit=__METEO_COEFF_CREDIT__;

			$limite_datediff=__METEO_LIMITE_DATEDIFF__;
			$limite_solde_total=__METEO_LIMITE_SOLDE_TOTAL__;
			$limite_marge=__METEO_LIMITE_MARGE__;
			$limite_devis_perdu=__METEO_LIMITE_DEVIS_PERDU__;

			//On stock le calcul de météo
			$meteo["meteo_calcul"]="(".__METEO_COEFF_DATEDIFF__."*(((".$datediff."/".__METEO_LIMITE_DATEDIFF__.")-1)+1))-(".__METEO_COEFF_CREDIT__."*".$credit.")+(".__METEO_COEFF_SOLDE_TOTAL__."*(((".$solde_total."/".__METEO_LIMITE_SOLDE_TOTAL__.")-1)+1))-(".__METEO_COEFF_MARGE__."*(((".$marge."/".__METEO_LIMITE_MARGE__.")-1)+1))+(".__METEO_COEFF_DEVIS_PERDU__."*(((".$devis_perdu_pourcent."/".__METEO_LIMITE_DEVIS_PERDU__.")-1)+1))";

			//Un datediff supérieur à 30 engendre du malus
			$datediff=__METEO_COEFF_DATEDIFF__*((($datediff/__METEO_LIMITE_DATEDIFF__)-1)+1);

			$credit=__METEO_COEFF_CREDIT__*$credit;

			//Un solde supérieur à 200 engendre du malus
			$solde_total=__METEO_COEFF_SOLDE_TOTAL__*((($solde_total/__METEO_LIMITE_SOLDE_TOTAL__)-1)+1);

			//Un marge inférieur à 5000 engendre du malus
			$marge=__METEO_COEFF_MARGE__*((($marge/__METEO_LIMITE_MARGE__)-1)+1);

			//Un pourcentage supérieur à 30 engendre du malus
			$devis_perdu_pourcent=__METEO_COEFF_DEVIS_PERDU__*((($devis_perdu_pourcent/__METEO_LIMITE_DEVIS_PERDU__)-1)+1);
			
			
			//Calcul de la météo
			$meteo["meteo"]=$datediff-$credit+$solde_total-$marge+$devis_perdu_pourcent;
			$meteo["meteo_calcul"]=round($meteo["meteo"],4)." = ".$meteo["meteo_calcul"];			
		}
		return $meteo;
	}
	
	/**
	* Fonction appeler depuis une crontab qui met à jour l'indice météo de chaque société
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	* @return array $chiffre_par_an
	*/	
	public function meteo_societe($id_societe=false){
		$this->q->reset();
		
		//Si l'on souhaite mettre à jour seulement une société
		if($id_societe){
			$this->q->addCondition("id_societe",$id_societe);
		}
		
		$societe=parent::select_all();
		$big["chiffre"]=0;
		$small["chiffre"]=1000000;

		$big["datediff"]=0;
		$small["datediff"]=1000000;

		$big["affaire_perdu"]['devis_perdu_pourcent']=0;
		$small["affaire_perdu"]['devis_perdu_pourcent']=1000000;

		$big["marge"]=0;
		$small["marge"]=1000000;

		$big["solde_total"]=-1000000;
		$small["solde_total"]=1000000;

		$meteo_tot=0;
		$nb_tot=0;
		$nb_societe=count($societe);
		foreach($societe as $key=>$item){
			$meteo[$key]=$this->meteo($item["id_societe"]);
			if($meteo[$key]["meteo"]){
				$meteo[$key]["societe"]=$item["societe"];
				$this->update(array("id_societe"=>$item["id_societe"],"meteo"=>$meteo[$key]["meteo"],"meteo_calcul"=>$meteo[$key]["meteo_calcul"]));
				$nb_tot++;
				$meteo_tot+=$meteo[$key]["meteo"];
//print_r($meteo[$key]);	
				if($meteo[$key]["meteo"]>=$big["chiffre"]){
					$big["chiffre"]=$meteo[$key]["meteo"];
					$big["societe"]=$item["societe"];
				}
				if($meteo[$key]["meteo"]<=$small["chiffre"]){
					$small["chiffre"]=$meteo[$key]["meteo"];
					$small["societe"]=$item["societe"];
				}
				if($meteo[$key]["marge"]>=$big["marge"]){
					$big["marge"]=$meteo[$key]["marge"];
					$big["marge_societe"]=$item["societe"];
				}
				if($meteo[$key]["marge"]<=$small["marge"]){
					$small["marge"]=$meteo[$key]["marge"];
					$small["marge_societe"]=$item["societe"];
				}
				if($meteo[$key]["datediff"]>=$big["datediff"]){
					$big["datediff"]=$meteo[$key]["datediff"];
					$big["datediff_societe"]=$item["societe"];
				}
				if($meteo[$key]["datediff"]<=$small["datediff"]){
					$small["datediff"]=$meteo[$key]["datediff"];
					$small["datediff_societe"]=$item["societe"];
				}
				if($meteo[$key]["affaire_perdu"]['devis_perdu_pourcent']>=$big["affaire_perdu"]['devis_perdu_pourcent']){
					$big["affaire_perdu"]['devis_perdu_pourcent']=$meteo[$key]["affaire_perdu"]['devis_perdu_pourcent'];
					$big["affaire_perdu"]['devis_perdu_pourcent_societe']=$item["societe"];
				}
				if($meteo[$key]["affaire_perdu"]['devis_perdu_pourcent']<=$small["affaire_perdu"]['devis_perdu_pourcent']){
					$small["affaire_perdu"]['devis_perdu_pourcent']=$meteo[$key]["affaire_perdu"]['devis_perdu_pourcent'];
					$small["affaire_perdu"]['devis_perdu_pourcent_societe']=$item["societe"];
				}
				if($meteo[$key]["solde_total"]>=$big["solde_total"]){
					$big["solde_total"]=$meteo[$key]["solde_total"];
					$big["solde_total_societe"]=$item["societe"];

				}
				if($meteo[$key]["solde_total"]<=$small["solde_total"]){
					$small["solde_total"]=$meteo[$key]["solde_total"];
					$small["solde_total_societe"]=$item["societe"];
				}
			}else{
				$this->update(array("id_societe"=>$item["id_societe"],"meteo"=>NULL,"meteo_calcul"=>NULL));
				unset($meteo["key"]);
			}			
		}
		//Trie du tableau en DESC de valeur météo (les + mauvais en premier...)
		foreach ($meteo as $key => $item) {
			$trie[$key]  = $item['meteo'];
		}
		array_multisort($trie, SORT_DESC, $meteo);

		foreach ($meteo as $key => $item) {
//print_r("\n".$key." : ".$item["societe"]." : ".$item["meteo"]);
		}

		//Calcul de la moyenne 'relative'
		$meteo_moyenne=$meteo[round($nb_tot/2)]["meteo"];
		
		//Mis à jour des constantes
		$id_constante=ATF::constante()->getConstante("__METEO_MOYENNE__");
		ATF::constante()->update(array("id_constante"=>$id_constante,"valeur"=>round($meteo_moyenne,4)));

		$id_constante=ATF::constante()->getConstante("__METEO_BIG__");
		ATF::constante()->update(array("id_constante"=>$id_constante,"valeur"=>round($big["chiffre"],4)));

		$id_constante=ATF::constante()->getConstante("__METEO_SMALL__");
		ATF::constante()->update(array("id_constante"=>$id_constante,"valeur"=>round($small["chiffre"],4)));

//		$moyenne=$meteo_tot/$nb_tot;
// print_r("\nmoyenne : ".$moyenne);
// print_r("\nmeteo_moyenne : ".$meteo_moyenne);
// print_r("\n+ Gros chiffre = ".$big["societe"]." : ".$big["chiffre"]);
// print_r("\n+ Petit chiffre = ".$small["societe"]." : ".$small["chiffre"]);
// 
// print_r("\n+ Gros chiffre datediff ".$big["datediff_societe"]." : ".$big["datediff"]);
// print_r("\n+ Petit chiffre datediff ".$small["datediff_societe"]." : ".$small["datediff"]);
// 
// print_r("\n+ Gros chiffre marge ".$big["marge_societe"]." : ".$big["marge"]);
// print_r("\n+ Petit chiffre marge ".$small["marge_societe"]."  : ".$small["marge"]);
// 
// print_r("\n+ Gros chiffre solde_total ".$big["solde_total_societe"]."  : ".$big["solde_total"]);
// print_r("\n+ Petit chiffre solde_total ".$small["solde_total_societe"]." : ".$small["solde_total"]);
// 
// print_r("\n+ Gros chiffre devis_perdu_pourcent ".$big["affaire_perdu"]['devis_perdu_pourcent_societe']."  : ".$big["affaire_perdu"]['devis_perdu_pourcent']);
// print_r("\n+ Petit chiffre devis_perdu_pourcent ".$small["affaire_perdu"]['devis_perdu_pourcent_societe']."  : ".$small["affaire_perdu"]['devis_perdu_pourcent']);
// 
// 
// print_r("\nRatio societe/meteo = ".$nb_tot." / ".$nb_societe);
	}

	/**
	* Fonction meteo_icone qui donne, pour un indice météo, l'icone correspondante et le classement (echelle)
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id
	* @return array $return
	*/	
	public function meteo_icone($meteo){
	
		if($meteo!="0.0000"){
			//Définition des icones
			$echelle[1]["icone"]="Sunny";
			$echelle[2]["icone"]="Mostly_Sunny";
			$echelle[3]["icone"]="Mostly_Cloudy";
			$echelle[4]["icone"]="Cloudy";
			$echelle[5]["icone"]="Rain";
			$echelle[6]["icone"]="Cloudy_With_Dizzle";
			$echelle[7]["icone"]="Rain_Or_Snow";
			$echelle[8]["icone"]="Flurries";
			$echelle[9]["icone"]="Snow";
			$echelle[10]["icone"]="Thunderstorms";
			
			$meteo_moyenne=__METEO_MOYENNE__;
			$small=__METEO_SMALL__;
			$big=__METEO_BIG__;
	
			$j=0;
			for($i=2;$i<=10;$i+=2){
				$j++;
				$echelle[$j]["echelle"]=(($meteo_moyenne)-$small)*($i/10)+$small;
			}
	
			for($i=2;$i<=10;$i+=2){
				$j++;
				$echelle[$j]["echelle"]=($big-($meteo_moyenne))*($i/10)+($meteo_moyenne);
			}
			
			//Pour chaque societe meteo on définit son classement
			for($i=1;$i<=10;$i++){
				if($meteo<=$echelle[$i]["echelle"]){
					$return["echelle"]=$i;
					$return["icone"]=$echelle[$i]["icone"];
					//On sort de la boucle
					$i=11;
				}
			}
		}else{
			$return["icone"]="Fog";
			$return["echelle"]="Pas de données pour la météo";
		}
		
		return $return;
	}
		
	/** 
	* Import d'une ATCard
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return void 
	*/
	public function atcardImport(&$infos,&$s,$files=NULL,&$cadre_refreshed) {
		if (!$files) return false;
		$infos['display']=true;
		$f = $files["atcardImport"];
		if (!$f['size']) {
			throw new errorATF(ATF::$usr->trans("fichier_corrompu",$this->table));	
		}

		if($fichier=fopen($f['tmp_name'],"r")){
			$read = fread($fichier, filesize($f['tmp_name']));
			
			//Récupération de la société 
			$data = explode("===BEGIN:CONTACT===",$read);
			
			
			ATF::db($this->db)->begin_transaction();
			try {
				if ($data[0]) {
					$data[0] = str_replace("\n","",$data[0]);
					$data[0] = str_replace("\r","",$data[0]);
					$data[0] = substr($data[0],42);
					

					$societe = unserialize($data[0]);
					unset($data[0]);
					// Insertion de la société
					array_shift($societe);
					unset($societe['solde'],$societe['ref'],$societe['date'],$societe['id_filiale'],$societe['divers_5'],$societe['meteo'],$societe['meteo_calcul'],$societe['mdp_client'],$societe['mdp_absystech']);
					$societe['id_owner'] = ATF::$usr->getID();
					$insert['societe'] = $societe;
					$id_societe = $this->insert($insert);
					unset($insert);
					//Insertion des contacts
					foreach ($data as $k=>$i) {
						$i = str_replace("\n","",$i);
						$i = str_replace("\r","",$i);
						$insert['contact'] = unserialize($i);
						array_shift($insert['contact']);
						$insert['contact']['id_owner'] = ATF::$usr->getID();
						$insert['contact']['id_societe'] = $id_societe;
						ATF::contact()->i($insert);
						unset($insert);
					}
				}
			} catch (error $e) {
				ATF::db($this->db)->rollback_transaction();	
				throw new errorATF(ATF::$usr->trans("problemeLorsDeLimport",$this->table)." => ".$e->getMessage());
			}
			ATF::db($this->db)->commit_transaction();
		}
		
		ATF::$json->add("success",true);
		ATF::$json->add("id_societe",$this->cryptID($id_societe));

		return ATF::$json->getJson();

	}

	/** 
	* Génère la ATcard puis l'envoi en DL au navigateur
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return void 
	*/
	public function atcard(&$infos,&$s,$files=NULL,&$cadre_refreshed) {
		$infos['display'] = true;
		$id = $this->decryptId($infos['id_societe']);
		$t = $this->filepath($id,"ATCARD-".$this->nom($infos['id_societe']));
		$this->generateATcard($id,$t);
		$this->dl(array("id_societe"=>$id,"field"=>"ATCARD-".$this->nom($infos['id_societe']),"type"=>"atcf"));
	}

	/** 
	* Généère la ATcard des sociétés + contact.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return void
	*/
	public function generateATcard($id,$target){
		util::mkdir(dirname($target));
		if(file_exists($target)){
			unlink($target);
		}
		touch($target);
		
		if($fichier=fopen($target,"w")){
			$begin="BEGIN:ATCARD\nVERSION:1.0\n";
			fwrite($fichier,$begin);
			$societe = $this->select($id);
//			$societe['societe'] = "ZorianSpecialTU";
			//Infos de la société
			$atcf['societe'] = "===BEGIN:SOCIETE===";
			$atcf['societe'] .= "\n";
			$atcf['societe'] .= serialize($societe);
			
			//Infos des contacts de cette société
			ATF::contact()->q->reset()->where("id_societe",$societe['id_societe']);
			foreach (ATF::contact()->sa() as $contact) {
				$tmp = "===BEGIN:CONTACT===";
				$tmp .= "\n";
				$tmp .= serialize($contact);
				
				$atcf['contact'] .= $tmp."\n";
			}
			fwrite($fichier,$atcf['societe']."\n".$atcf['contact']);
			$end="END:ATCARD";
			fwrite($fichier,$end);
			fclose($fichier);
		}
	}
	

	/** 
	* Généère la ATcard des sociétés + contact.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return void
	*/
	public function getCSSSocieteDebitrice($data){
		if (!$data['id_societe']) return false;
		ATF::gestion_ticket()->q->reset()
			->where('id_societe',$data['id_societe'])
			->setLimit(1)
			->setDimension('row')
			->addOrder('date','desc');
		$record = ATF::gestion_ticket()->sa();

		$SixMonthLess = mktime(0,0,0,date("m")-6,date("d"),date("Y"));
		$recordTS = strtotime($record['date']);
		
		if ($recordTS<$SixMonthLess) {
			return "societeDebitriceToCall";	
		} else {
			return "societeDebitrice";	
		}
			
	
	}
	
	/**
	* Autocomplete avec les termes associés à chaque société
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteAvecTermes($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q
			->addField("societe.id_termes")
			->addField(array("CONCAT(societe.id_termes)"=>array("alias"=>"id_termes_fk","nosearch"=>true)));		// Entourloupe habituelle à l'autojoin
		return parent::autocomplete($infos,false);
	}
	
	
	public function autocompleteOnlyActive($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q->where("societe.etat", "actif");
		// Entourloupe habituelle à l'autojoin
		return parent::autocomplete($infos,false);
	}
	
	
	/**
	* Autocomplete avec la TVA associés à chaque société
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteAvecTVA($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$data=parent::autocomplete($infos,false);
		foreach($data as $key => $item){
			$data_return[$key][0]=$item[2];	
			$data_return[$key][1]=$item["raw_0"];	
			$data_return[$key][2]=$item[1];	
			$data_return[$key]["raw_0"]=ATF::facture()->getTVA($item["raw_0"]);	
			$data_return[$key]["raw_1"]=$item["raw_0"];
			$data_return[$key]["raw_2"]=$item["raw_1"];
			$data_return[$key]["email_facturation"]=ATF::contact()->select(ATF::societe()->select($item["raw_0"],"id_contact_facturation"),"email");
		}
		
		return $data_return;
	}

		
	/**
	* Autocomplete retournant les fournisseurs d'une commande
	* @author MOUAD EL HIZABRI
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
		    ->where("societe.fournisseur","oui");

       return parent::autocomplete($infos,false);
	}
	
	/**
	* Autocomplete retournant les fournisseurs
	* @author MOUAD EL HIZABRI
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteFournisseurs($infos,$reset=true) {
		if ($reset) {
               $this->q->reset();
       }
       $this->q->where("societe.fournisseur","oui");

       return parent::autocomplete($infos,false);
	}
	
	
	/**
	* Retourne les sociétés aléatoirement, sans les particuliers
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return array
	*/
	public function rpcGetRecentToRecallForMobile($infos){
		$this->q->reset()
			->where("id_famille",2,"OR","famille","!=");
		return parent::rpcGetRecentToRecallForMobile($infos,true);
	}
	
	/*
	* Permet de faire une insertion rapide en extjs
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @return une nouvelle fenêtre
	*/
	public function rapprocher(&$infos){
		ATF::$html->array_assign($infos);
		ATF::$html->assign("table",$this->table);

		$infos["display"]=true;
		return ATF::$html->fetch("rapprocher-societe.tpl.js");
	}
	
	/*
	* Permet de remplacer une carte via une fenêtre extjs
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @return une nouvelle fenêtre
	*/
	
	public function updateRapprocher(){
		return true;
	}

	public function estFermee($id_soc){		
		if($this->select($id_soc , "etat") == "inactif"){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Surcharge de la méthode update dans le cas où l'on rends la société inactive, on modifie le mdp hotline
	* @author morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function update($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos);	
        $infos['id_societe'] = $this->decryptId($infos['id_societe']);	
		$retour=parent::update($infos,$s,$files,$cadre_refreshed);	
		
		if($infos_soc['etat']!=$infos['etat'] && $infos['etat']=='inactif'){				
			$new = substr(md5(time()),0,4);
			$this->u(array("id_societe" => $infos["id_societe"] , "divers_5" => $new));		
		}		
		return $retour;
	}

};

class societe_att extends societe_absystech {
	/** 
	* Retourne le prfixe utilis, peut tre surcharg
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return string $prefix
	*/
	public function create_ref_prefix(){
		return "AS";
	}
};

class societe_demo extends societe_absystech { }
class societe_wapp6 extends societe_absystech { }
?>