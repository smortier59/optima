<?
require_once dirname(__FILE__)."/../societe.class.php";
/**
* @package Optima
* @subpackage nco
*/
class societe_lm extends societe {
	/**
	* Constructeur par défaut
	*/
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
			,"id_owner"
			,"etat"
			,"joignable"
			,"type_societe"/*=>array("listeners"=>array("select"=>"ATF.selectType_client","afterRender"=>"ATF.loadClient"))*/
		);

		$PanelCoordonne = $this->colonnes["panel"]["coordonnees"];

		unset($this->colonnes["panel"]["coordonnees"]);


		$this->colonnes['panel']['client']=array(
			"nom_client"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"civilite"
				,"nom"
				,"prenom"
			))
			,"id_lm"
			,"id_carte_maison"
			,"id_magasin"
			,"offre_lmA"
			,"offre_lm"
			,"mdp"
		);




		$this->colonnes['panel']['soc'] = array(
			"nom_societe"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"societe"
				,"nom_commercial"
			))
			,"sirens"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"siren"
				,"siret"
			))
			,"avis_credit"
			,"cs_avis_credit"
			,"score"
			,"cs_score"
			,"contentieux"
			,"id_assistante"
			,"date_creation"
			,"relation"
			,"client_id"
			,"client_secret"
			,"email_notification"

		);

		$this->panels['soc'] = array('nbCols'=>2,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);
		$this->panels['caracteristiques'] = array('nbCols'=>2,'isSubPanel'=>true,'collapsible'=>true,'visible'=>true);

		$this->colonnes['panel']['autres'] = array("id_apporteur","id_fournisseur","id_prospection","id_campagne");
		$this->panels['autres'] = array('nbCols'=>2,'isSubPanel'=>true,'collapsible'=>true,'visible'=>false);



		$this->colonnes['panel']['infos_soc']=array(
			"socs"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'soc')
			,"carac"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'caracteristiques')
			,"autre"=>array("custom"=>true,'xtype'=>'fieldset','panel_key'=>'autres')
		);
		$this->panels['infos_soc'] = array('nbCols'=>1,'collapsible'=>true,'visible'=>false);


		$this->colonnes["panel"]["coordonnees"] = $PanelCoordonne;




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


		$this->colonnes['bloquees']['select'] = array('joignable');


		/* Définition statique des clés étrangère de la table */
		$this->onglets = array(
			 'contact'=>array('opened'=>true)
			,'affaire'=>array('opened'=>true)
			,'suivi'=>array('opened'=>true)
			,'devis'
			,'commande'
			,'tache'
			,'parc'
			,'ged'
			,'user'
		);

		$this->fieldstructure();

		unset($this->colonnes['panel']['facturation_fs']["solde_et_relance"]);
		$this->checkAndRemoveBadFields('caracteristiques');
		$this->checkAndRemoveBadFields('facturation_fs');

		$this->foreign_key["id_apporteur"] = "societe";
		$this->foreign_key["id_fournisseur"] = "societe";
		$this->foreign_key["id_contact_signataire"] = "contact";
		$this->foreign_key["id_prospection"] = "contact";
		$this->foreign_key["id_assistante"] = "user";


		$this->field_nom = "%societe%%nom% %prenom%";

		// Pouvoir modifier massivement
		$this->no_update_all = false;

		// on montre que pour joindre la table domaine, on passe par une table de jointure qui est societe_domaine, si on créé un filtre dans le module société
		$this->listeJointure['domaine']="societe_domaine";

		// Droits sur méthodes Ajax
		$this->addPrivilege("getParc");
		$this->addPrivilege("getParcVente");
		$this->addPrivilege("getTreePanel");
		$this->addPrivilege("getChildren");
		$this->addPrivilege("autocompleteFournisseurs");
		$this->addPrivilege("autocompleteFournisseursDeCommande");
		$this->addPrivilege("setToken");

		$this->autocomplete = array(
			 "field"=>array("societe.societe","societe.nom_commercial","societe.code_client","societe.nom","societe.prenom")
			,"show"=>array("societe.societe","societe.nom_commercial","societe.code_client","societe.nom","societe.prenom")
			,"popup"=>array("societe.societe","societe.nom_commercial","societe.code_client","societe.nom","societe.prenom")
			,"view"=>array("societe.societe","societe.nom_commercial","societe.code_client","societe.nom","societe.prenom")
		);

	}


	/**
	 * Surcharge de l'autocomplete pour afficher le client si ce n'est pas une société
	 * @param  [type]  $infos [description]
	 * @param  boolean $reset [description]
	 * @return [type]         [description]
	 * @author  Morgan Fleurquin <mfleurquin@absystech.fr>
	 */
	public function autocomplete($infos,$reset=true) {
		$res = parent::autocomplete($infos,$reset);
		foreach ($res as $key => $value) {
			if(!$value[1]){
				$client = ATF::societe()->select($value["raw_0"] , "nom")." ".ATF::societe()->select($value["raw_0"] , "prenom")." (".ATF::societe()->select($value["raw_0"] , "ref").")";
				$res[$key][1] = $res[$key]["raw_1"] = $client;
			}
		}

		return $res;
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

		log::logger($infos , "mfleurquin");

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

	public function autocompleteFournisseurs($infos,$reset=true) {
		if ($reset) {
               $this->q->reset();
       }
       $this->q->where("societe.fournisseur","oui");

       return parent::autocomplete($infos,false);
	}

	/**
	* Mise à jour des infos
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
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

		if($infos["societe"]["type_societe"] == "client"){
			$infos["societe"]["societe"] = $infos["societe"]["nom"]." ".$infos["societe"]["prenom"];
		}

		$last_id = parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);

		if($infos["societe"]["type_societe"] == "client"){
			ATF::contact()->i(array("civilite"=>$infos["societe"]["civilite"] ,
									 "nom"=>ucfirst($infos["societe"]["nom"]),
									 "prenom"=>ucfirst($infos["societe"]["prenom"]),
									 "id_societe"=>$last_id,
									 "id_owner"=>$infos["societe"]["id_owner"]

									)
							  );
		}

		return $last_id;
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
						 ->addCondition("commande.etat","arreter",false,false,"!=");

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
						 ->addCondition("commande.etat","arreter");

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

	//Méthode qui permet de générer un token et l'insère en base de donnée
	public function setToken($infos){
		try {
			$token = substr(sha1(__ABSOLUTE_PATH__.microtime().mt_rand(0,100000)), 0, 25);
			$expire_time = strtotime("+5 minutes", strtotime(date("Y-m-d H:i:s")));

			$token_insert = array(
				"token"=>$token
				,"expire_time"=>date("Y-m-d H:i:s", $expire_time)
				,"id_societe"=>$infos['id_societe']
			);
			$id = ATF::token()->insert($token_insert);
			if ($id) $infos_token = ATF::token()->select($id);
			if ($infos_token)
				return $infos_token['token'];


		} catch (Exception $e) {
			log::logger($e->getMessage(), 'alahlah');
		}
	}


};